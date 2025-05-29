<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\BillingDetail;
use App\Models\Country;
use App\Models\EventContact;
use App\Models\Events;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Sector;
use App\Models\State;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\EventParticipation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminApplicationSubmitted;
use App\Mail\UserApplicationSubmitted;
use App\Models\SecondaryEventContact;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{


    //construct function to check whether user is logged in or not
    public function __construct()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    }

    //call the ProductCategory model

    //generate application id as from the constants file
    public function generateApplicationId()
    {
        //call the construct function
        $this->__construct();

        $applicationId = config('constants.APPLICATION_ID_PREFIX') . substr(uniqid(), -5);
        //make sure that it doesn't match with any existing application id
        if (Application::where('application_id', $applicationId)->exists()) {
            return $this->generateApplicationId();
        }
        return $applicationId;
    }




    public function showForm($slug, Request $request)
    {
        $this->__construct();

        // Fetch only necessary columns and cache if possible
        $eventExists = Events::where('slug', $slug)->first(['event_name', 'event_year']);
        if (!$eventExists) {
            return redirect()->back()->withErrors(['error' => 'Event does not exist.']);
        }

        $role = 'exhibitor';
        if (!in_array($role, ['exhibitor', 'sponsor'])) {
            abort(404);
        }


        // Use caching for sector data as it doesn't change frequently
        $sectors = Cache::remember('sectors', 60, function () {
            return Sector::select('id', 'name')->get();
        });

        $subSectors = config('constants.SUB_SECTORS', []);

        $countries = Cache::remember('countries', 60, function () {
            return Country::select('id', 'name', 'code')->get();
        });

        $states = Cache::remember('states', 60, function () {
            return State::select('id', 'name')->get();
        });

        // Fetch only latest application for the user
        $application = Application::where('user_id', auth()->id())
            ->latest()
            ->select('*') // Select only necessary fields
            ->first();

        $eventContact = $billing = null;
        if ($application) {
            // Use eager loading to minimize queries
            $application->load(['eventContact', 'billingDetail']);
            $eventContact = $application->eventContact;
            $billing = $application->billing;
            $eventContact = EventContact::where('application_id', $application->id)->first();
            $billing = BillingDetail::where('application_id', $application->id)->first();
        }

        // $business = $this->typesofbusiness;

        $stall_type = ['Shell Scheme', 'Raw Space']; // Replace with dynamic data if needed

        //pass event name and year to the view
        $event = Events::where('slug', $slug)->first(['id', 'event_name', 'event_year']);



        return view('exhibitor.page', compact(
            'role',
            'countries',
            'states',
            'sectors',
            'application',
            'eventContact',
            'billing',
            'subSectors',
            'stall_type',
            'event'
        ));
    }


    public function submitForm(Request $request)
    {


        //    dd($request->all());

        $this->__construct();
        $role = 'exhibitor';

        if (!in_array($role, ['exhibitor', 'sponsor'])) {
            abort(404);
        }
        // Validation
        $validated = $request->validate([
            'billing_country' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Country::where('id', $value)->exists()) {
                        $fail('The selected country for billing is invalid.');
                    }
                },
            ],
            'gst_compliance' => [
                'required_if:billing_country,101,351',
                'boolean'
            ],
            'gst_no' => 'nullable|string|required_if:gst_compliance,1',
            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'contactNoCode' => 'required|string|max:5',
            'contactPhone_code' => 'required|string|max:5',
            'billing_phoneCode' => 'required|string|max:5',
            //  'state' => 'required|string|max:255',
            'state' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $country = Country::find($request->input('country'));
                    if (!$country || !State::where('country_id', $country->id)->where('id', $value)->exists()) {
                        $fail('The selected state is invalid for the selected country.');
                    }
                },
            ],
            'company_no' => 'nullable|string|max:15',
            'company_email' => 'required|email|max:255',
            //if website is without http or https then add https to it
            'website' => 'nullable|string|max:255',
            'pan_no' => 'nullable|string',
            'tan_no' => 'nullable|string',
            'event_contact_salutation' => 'required|string|max:10',
            'event_contact_first_name' => 'required|string|max:255',
            'event_contact_last_name' => 'required|string|max:255',
            'event_contact_designation' => 'required|string|max:255',
            'event_contact_email' => 'required|email|max:255',
            'event_contact_phone' => 'required|string|max:15',
            'billing_company' => 'required|string|max:255',
            'billing_contact_name' => 'required|string|max:255',
            'billing_email' => 'required|email|max:255',
            'billing_phone' => 'required|string|max:15',
            'billing_address' => 'required|string|max:500',
            'billing_postal_code' => 'required|string|max:10',
            'billing_city' => 'required|string|max:255',
            'event_id' => 'required|exists:events,id',
            'assoc_mem' => 'nullable|string',
            'country' => 'required|exists:countries,id',

            'billing_state' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $country = Country::find($request->input('country'));
                    if (!$country || !State::where('country_id', $country->id)->where('id', $value)->exists()) {
                        $fail('The selected state is invalid for the selected country.');
                    }
                },
            ],
            'gst_certificate' => 'nullable|file|mimes:pdf|max:2048',
            'sector' => 'required|string|max:255',
            'sub_sector' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'previous_participation' => 'required|string|max:255',
            'stall_category' => 'required|string|max:255',
            'interested_sqm' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $exhibitionCosts = config('constants.exhibition_cost', []);
                    // Accept values like 9, 9s, 18, 18s, etc.
                    if (!array_key_exists($value, $exhibitionCosts)) {
                        $fail('The selected sqm is not valid.');
                    }
                },
            ],
            'sponsorship_apply' => 'required|boolean',
        ]);






        // Check if the application already exists
        $application = Application::firstOrNew([
            'user_id' => auth()->id(),
            'event_id' => $request->event_id,
        ]);

        if ($request->hasFile('gst_certificate')) {
            // Store the new file and update the application record
            $gst_certificate = $request->file('gst_certificate')->store('gst_certificates', 'public');
            $application->certificate = $gst_certificate;
        }
        if ($request->billing_country == 101 || $request->billing_country == 351) {
            $payment_currency = 'INR';
        } else {
            $payment_currency = 'USD';
        }

        $companyPhone = $request->contactNoCode . '-' . $request->company_no;
        $contactPhone = $request->contactPhone_code . '-' . $request->event_contact_phone;
        $billingPhone = $request->billing_phoneCode . '-' . $request->billing_phone;

        //if website is without http or https then add https to it
        if ($request->website && !preg_match('/^https?:\/\//', $request->website)) {
            $request->website = 'https://' . $request->website;
        }

        $application->fill([
            'headquarters_country_id' => $request->headquarters_country,
            'billing_country_id' => $request->billing_country,
            'gst_compliance' => $request->gst_compliance,
            'company_name' => $request->company_name,
            'address' => $request->address,
            'landline' => $companyPhone,
            'postal_code' => $request->postal_code,
            'city_id' => $request->city,
            'country_id' => $request->country,
            'state_id' => $request->state,
            'main_product_category' => $request->main_product_category,
            'gst_no' => $request->gst_no,
            'pan_no' => $request->pan_no,
            'tan_no' => $request->tan_no,
            //            'certificate' => $gst_certificate,
            'company_email' => $request->company_email,
            'website' => $request->website,
            'type_of_business' => implode(',', $request->type_of_business),
            'payment_currency' => $payment_currency,
            'status' => 'initiated',
            'application_id' => $application->exists ? $application->application_id : $this->generateApplicationId(),
            'country_name' => $request->country,
            'assoc_mem' => $request->assoc_mem,
            'region' => $request->region,
            'participated_previous' => $request->previous_participation,
            'stall_category' => $request->stall_category,
            'interested_sqm' => $request->interested_sqm,
            'sponsorship_apply' => $request->sponsorship_apply,
            'sector_id' => $request->sector,
            'sub_sector' => $request->sub_sector,

        ]);

        // Save the application
        $application->save();

        // Update or create an EventContact
        $eventContact = EventContact::updateOrCreate(
            // Matching criteria to find the record
            [
                'application_id' => $application->id,
                'email' => $request->event_contact_email,
            ],
            // Fields to update or insert
            [
                'salutation' => $request->event_contact_salutation,
                'first_name' => $request->event_contact_first_name,
                'last_name' => $request->event_contact_last_name,
                'designation' => $request->event_contact_designation,
                'job_title' => $request->event_contact_designation,
                'contact_number' => $contactPhone,
            ]
        );

        // Update or create billing details
        $billing = BillingDetail::updateOrCreate(
            ['application_id' => $application->id],
            [
                'billing_company' => $request->billing_company,
                'contact_name' => $request->billing_contact_name,
                'email' => $request->billing_email,
                'phone' => $billingPhone,
                'address' => $request->billing_address,
                'postal_code' => $request->billing_postal_code,
                'city_id' => $request->billing_city,
                'state_id' => $request->billing_state,
                'country_id' => $request->billing_country,
                'gst_id' => $request->gst_no,
                'same_as_basic' => '0',
            ]
        );

        //redirect to apply page with name application.show
        return redirect()->route('application.show')->with('success', 'Application saved successfully!');

        return redirect()->route("dashboard.{$role}")->with('success', 'Application saved successfully!');
    }


    public function review()
    {
        $this->__construct();
        $application = Application::where('user_id', auth()->id())->latest()->first();
        $eventContact = EventContact::where('application_id', $application->id)->first();
        $billing = BillingDetail::where('application_id', $application->id)->first();

        return view('applications.review', [
            'application' => $application,
            'eventContact' => $eventContact,
            'billing' => $billing,
        ]);
    }
    //second step of form submission

    public function apply()
    {
        $this->__construct();
        //if already filled the form then show the form with the filled data
        $application = Application::where('user_id', auth()->id())->latest()->first();
        //return to event participation page with route name event.list'
        if (!$application) {
            return redirect()->route('event.list');
        }
        //        $participation_type =  ['Onsite' => 'active', 'Hybrid' => 'active', 'Online' => 'disabled']; // Replace with dynamic data if needed
        $participation_type =  ['Onsite' => 'active']; // Replace with dynamic data if needed
        if ($application) {
            return view('applications.apply-page', compact('application'));
        }
        return view('applications.apply-page', compact(''));
    }
    public function apply_spon()
    {
        $this->__construct();
        //if already filled the form then show the form with the filled data
        $application = Application::where('user_id', auth()->id())->latest()->first();
        //return to event participation page with route name event.list'
        if (!$application) {
            return redirect()->route('event.list');
        }


        //        $participation_type =  ['Onsite' => 'active', 'Hybrid' => 'active', 'Online' => 'disabled']; // Replace with dynamic data if needed
        $participation_type =  ['Onsite' => 'active']; // Replace with dynamic data if needed
        $productGroups = [
            'Semiconductor Design (EDA, IP, etc)',
            'Semiconductor Equipment',
            'Semiconductor Materials',
            'Flat panel display',
            'Fab facilities and semiconductor-related services',
            'Government / Association / Research',
            'Semiconductor Front-end manufacturing (IDM, foundry, etc)',
            'Semiconductor Back-end manufacturing (assembly, packaging, testing)',
        ];
        $sectors = Sector::select('id', 'name')->get();
        $stall_type = ['Shell Scheme', 'Bare Space']; // Replace with dynamic data if needed
        if ($application) {
            return view('sponsor.apply-page', compact('application', 'productGroups', 'sectors', 'stall_type', 'participation_type'));
        }

        return view('sponsor.apply-page', compact('participation_type', 'productGroups', 'sectors', 'stall_type'));
    }
    public function apply_new()
    {
        $this->__construct();
        //if already filled the form then show the form with the filled data
        $application = Application::where('user_id', auth()->id())->latest()->first();
        $participation_type =  ['Onsite' => 'active', 'Hybrid' => 'active', 'Online' => 'disabled']; // Replace with dynamic data if needed
        $productGroups = ['Group A', 'Group B', 'Group C']; // Replace with dynamic data if needed
        $sectors = Sector::select('id', 'name')->get();
        $stall_type = ['Shell Scheme', 'Bare Space']; // Replace with dynamic data if needed
        if ($application) {
            return view('sponsor.apply-page', compact('application', 'productGroups', 'sectors', 'stall_type', 'participation_type'));
        }
        return view('sponsor.apply-page', compact('participation_type', 'productGroups', 'sectors', 'stall_type'));
    }

    public function apply_store(Request $request)
    {
        $this->__construct();

        // get the application from the the auth user id
        $application = Application::where('user_id', auth()->id())->latest()->first();
        //if application is not found then return to event list page with error message
        if (!$application) {
            return redirect()->route('event.list')->withErrors(['error' => 'Application not found. Please fill the form first.']);
        }

        // if application is submitted then reoute to $paymentPageUrl = route('paymentPage', ['id' => $application->application_id]);

        if ($application->submission_status === 'submitted') {
            // Redirect to payment page with error message
            // // redirect to /paymentpage/$application->application_id
            return redirect("/paymentPay/{$application->application_id}")->with('error', 'Application already submitted. Please proceed to payment.');

            return redirect()->route('paymentPage', ['id' => $application->application_id])->with('error', 'Application already submitted. Please proceed to payment.');
        }

        //update the application with the submission_status and submission_date
        $application->submission_status = 'submitted';
        $application->submission_date = now();
        $application->status = 'submitted';
        $application->save();


        // get the interested_sqm from the application->interested_sqm
        $interested_sqm = $application->interested_sqm;

        $sqm = $application->interested_sqm;
        $isShell = Str::contains(strtolower($sqm), 's');
        $sqmValue = (int) filter_var($sqm, FILTER_SANITIZE_NUMBER_INT);
        if ($isShell) {
            $rate = config('constants.SHELL_SCHEME_RATE');
        } else {
            $rate = config('constants.RAW_SPACE_RATE');
        }
        $total = $sqmValue * $rate;



        // get the discount percentage from the promo code if applicable
        $discountPercentage = $request->input('discount_percentage_mn', 0);




        // calculate the total amount based on the interested_sqm and stall_category

        // $stallPrice = $total;
        // $gst = round($stallPrice * 0.18);
        // $processingCharge = config('constants.IND_PROCESSING_CHARGE');
        // $processingBase = $stallPrice + $gst;
        // if ($application->payment_currency == 'USD') {
        //     $processingChargeRate = config('constants.INT_PROCESSING_CHARGE', 0);
        // } else {
        //     $processingChargeRate = config('constants.IND_PROCESSING_CHARGE', 0);
        // }
        // $processingCharge = round($processingBase * ($processingChargeRate / 100));
        // $grandTotal = $stallPrice + $gst + $processingCharge;

        // print_r([
        //     'stall_price' => $stallPrice,
        //     'gst' => $gst,
        //     'processing_charge' => $processingCharge,
        //     'total' => $grandTotal,
        // ]);

        // CALL the calculateStallTotal function to get the total price breakdown
        $stallTotal = $this->calculateStallTotal(
            $application->stall_category,
            $sqmValue,
            $application->payment_currency,
            $discountPercentage, // Assuming no discount for now
            0 // Assuming no early bird rate for now
        );




        //create or update Invoice for the application

        $invoice = Invoice::updateOrCreate(
            ['application_id' => $application->id],
            [
                'application_id' => $application->id,
                'type' => 'Stall Booking',

                'invoice_no' => $application->application_id,
                'amount' => $stallTotal['total'],
                'currency' => $application->payment_currency,
                'status' => 'unpaid',
                'payment_due_date' => now()->addDays(15), // Assuming payment due in 30 days
                'discount_per' => $discountPercentage, // Assuming no discount for now
                'discount' => $stallTotal['stall_price'] * ($discountPercentage / 100),
                'gst' => $stallTotal['gst'],
                'processing_charges' => $stallTotal['processing_charge'],
                'price' => $stallTotal['stall_price'],
                'total_final_price' => $stallTotal['total'],
                'application_no' => $application->application_id,
            ]
        );

        $route  = 'paymentPage'; // Default route to payment page

        // check what is the path from route with id = $application->application_id
        $paymentPageUrl = route('paymentPage', ['id' => $application->application_id]);

        dd($paymentPageUrl);




        return redirect("/paymentPay/{$application->application_id}")->with('error', 'Application already submitted. Please proceed to payment.');
        //        return redirect()->route('terms')->with('success', 'Application saved successfully!');

        //        return response()->json(['message' => 'Data updated', 'application' => $application]);

    }


    // get the view of payment page using the application id

    public function paymentPage($applicationId)
    {


        $application = Application::where('application_id', $applicationId)->first();
        if (!$application) {
            return "No application found with this ID.";
        }

        $invoice = Invoice::where('invoice_no', $applicationId)
            ->where('type', 'Stall Booking')
            ->first();

        if (!$invoice) {
            return "No invoice found for this application.";
        }

        //        dd($invoice);

        // return view of email.exhibitor_invoice
        return view('pay.exhibitor_invoice', [
            'application' => $application,
            'invoice' => $invoice,
            'billingDetail' => $application->billingDetail,
        ]);
    }

    //make a function where I pass the stall category, stall size, payment currency, discount percentage, early bird rate then calculate the total price
    /**
     * Calculate the total price for a stall and return breakdown.
     *
     * @param string $stallCategory
     * @param int $stallSize
     * @param string $paymentCurrency
     * @param float $discountPercent
     * @param float $earlyBirdRate
     * @return array
     */
    private function calculateStallTotal($stallCategory, $stallSize, $paymentCurrency, $discountPercent = 0, $earlyBirdRate = 0)
    {
        // Get base rate based on stall category and currency
        if (strtolower($stallCategory) === 'shell scheme') {
            $rate = $paymentCurrency === 'USD'
                ? config('constants.SHELL_SCHEME_RATE_USD', 0)
                : config('constants.SHELL_SCHEME_RATE', 0);
        } else {
            $rate = $paymentCurrency === 'USD'
                ? config('constants.RAW_SPACE_RATE_USD', 0)
                : config('constants.RAW_SPACE_RATE', 0);
        }

        // Use early bird rate if provided and greater than zero
        if ($earlyBirdRate > 0) {
            $rate = $earlyBirdRate;
        }

        $stallPrice = $stallSize * $rate;

        // Apply discount if any
        if ($discountPercent > 0) {
            $stallPrice -= ($stallPrice * ($discountPercent / 100));
        }

        // GST calculation (18%)
        $gst = round($stallPrice * 0.18);

        // Processing charge
        if ($paymentCurrency === 'USD') {
            $processingChargeRate = config('constants.INT_PROCESSING_CHARGE', 0);
        } else {
            $processingChargeRate = config('constants.IND_PROCESSING_CHARGE', 0);
        }
        $processingCharge = round(($stallPrice + $gst) * ($processingChargeRate / 100));

        // Total
        $total = $stallPrice + $gst + $processingCharge;

        return [
            'stall_price' => $stallPrice,
            'gst' => $gst,
            'processing_charge' => $processingCharge,
            'total' => $total,
        ];
    }


    //terms and conditions with I acknowledge that I have read the above terms and condition carefully.* checkbox
    public function terms()
    {
        $this->__construct();
        $application = Application::where('user_id', auth()->id())->latest()->first();
        return view('applications.terms_new', compact('application'));
    }

    //terms and conditions with I acknowledge that I have read the above terms and condition carefully.* checkbox
    public function terms_store(Request $request)
    {
        $this->__construct();
        $validatedData = $request->validate([
            'terms_accepted' => 'accepted',
        ]);

        $application = Application::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'terms_accepted' => 1,
            ]
        );

        //redirect to route named preview
        return redirect()->route('application.preview')->with('success', 'Application saved successfully!');

        return response()->json(['message' => 'Data updated', 'application' => $application]);
    }

    //preview of everything filled by the user.
    public function preview()
    {
        $this->__construct();
        //if user is not logged in then redirect to login page

        $application = Application::where('user_id', auth()->id())->latest()->first();


        $eventContact = EventContact::where('application_id', $application->id)->first();
        $billing = BillingDetail::where('application_id', $application->id)->first();

        $invoice = Invoice::where('application_id', $application->id)->first() ?? null;
        //$id = $invoice->id;
        //        dd($invoice, $id);
        $payments = array();
        if ($invoice) {
            $in_id = $invoice->id;
            //            dd($in_id);
            //$payments = Payment::where('application_id', $application->application_id)->get();
            $payments = Payment::where('invoice_id', $in_id)->get();
        }

        //        dd($payments);


        return view('applications.preview_new', [
            'application' => $application,
            'eventContact' => $eventContact,
            'billing' => $billing,
            'invoice' => $invoice,
            'payments' => $payments,
        ]);
    }

    //final
    public function final(Request $request)
    {
        $this->__construct();
        $application = Application::where('user_id', auth()->id())->latest()->first();

        $application->submission_status = 'submitted';
        $application->submission_date = now();
        $application->save();

        // Get the admin email (replace with your actual admin email)

        // Send email to admin and organisers using BCC
        Mail::to(config('constants.admin_emails.to'))->bcc(config('constants.admin_emails.bcc'))->queue(new AdminApplicationSubmitted($application));

        $userEmails = [
            $application->eventContact->email,
            // $application->billingDetail->email,
            // auth()->user()->email
        ];

        foreach ($userEmails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($email)->send(new UserApplicationSubmitted($application));
                } catch (\Exception $e) {
                    // Handle the error (log it, notify admin, etc.)
                    \Log::error("Failed to send email to {$email}: " . $e->getMessage());
                }
            } else {
                // Handle invalid email (log it, notify admin, etc.)
                \Log::error("Invalid email address: {$email}");
            }
        }





        // dd($application);
        //if invoice details are found for application->id in invoice table then pass the details in compact
        //else pass null



        //redirect to application.final with $application
        return redirect()->route('application.preview')->with('success', 'Application submitted successfully! ')->with(compact('application'));

        #return redirect()->route('dashboard.exhibitor')->with('success', 'Application submitted successfully!');
    }
    public function final_admin(Request $request)
    {
        $this->__construct();
        //add a validation of application_id in request
        $validated = $request->validate([
            'application_id' => 'required|exists:applications,application_id',
        ]);



        $application = Application::where('application_id', $request->application_id)->first();

        $application->submission_status = 'submitted';
        $application->submission_date = $application->updated_at ?? now();
        $application->save();

        // Get the admin email (replace with your actual admin email)
        $emails = ['test.interlinks@gmail.com'];

        // Send email to admin and organisers using BCC
        Mail::to(config('constants.admin_emails.to'))->bcc(config('constants.admin_emails.bcc'))->queue(new AdminApplicationSubmitted($application));

        $userEmails = [
            $application->eventContact->email,
            // $application->billingDetail->email,
            // auth()->user()->email
        ];

        foreach ($userEmails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($email)->send(new UserApplicationSubmitted($application));
                } catch (\Exception $e) {
                    // Handle the error (log it, notify admin, etc.)
                    \Log::error("Failed to send email to {$email}: " . $e->getMessage());
                }
            } else {
                // Handle invalid email (log it, notify admin, etc.)
                \Log::error("Invalid email address: {$email}");
            }
        }





        // dd($application);
        //if invoice details are found for application->id in invoice table then pass the details in compact
        //else pass null



        //redirect to application.final with $application
        return redirect()->route('application.preview')->with('success', 'Application submitted successfully! ')->with(compact('application'));

        #return redirect()->route('dashboard.exhibitor')->with('success', 'Application submitted successfully!');
    }
    //invoice display by post
    public function invoice(Request $request, $applicationId)
    {
        $this->__construct();
        //get application id from request
        #$applicationId = $request->input('application_id');
        //get application details from application model
        #$application = Application::find($applicationId);
        $application = Application::where('application_id', $applicationId)->first();
        $productCategories = ProductCategory::select('id', 'name')->get();
        //verify the user_id and application_id from the application model
        if ($application->user_id != auth()->id()) {
            return redirect()->route('dashboard.exhibitor')->withErrors(['error' => 'Unauthorized access.']);
        }
        $application->main_product_category = $productCategories->where('id', $application->main_product_category)->first()->name;
        //get invoice details from invoice model
        $applicationId = $application->id;
        $invoice = Invoice::where('application_id', $applicationId)->first();
        //dd($invoice);
        return view('applications.invoice_info', compact('application', 'invoice'));
    }


    //application info from application id
    public function applicationInfo()
    {
        $this->__construct();
        //from the auth user take the application id and get the details of the application

        $applicationId = auth()->user()->applications->first()->id;

        $productCategories = ProductCategory::select('id', 'name')->get();

        //get application details from application model
        $application = Application::find($applicationId);
        //get invoice details from invoice model
        $invoice = Invoice::where('application_id', $applicationId)->first();
        //billing details from billing detail model
        $billingDetails = BillingDetail::where('application_id', $applicationId)->first();
        //event contact details from event contact model
        $eventContact = EventContact::where('application_id', $applicationId)->first();

        return view('applications.application_info', compact('application', 'invoice', 'billingDetails', 'eventContact', 'productCategories'));
    }

    //make a ajax call to get the interested sqm based on the stall category
    public function getSQMOptions(Request $request)
    {
        $stallType = $request->input('stall_type');
        $startValue = ($stallType === "Bare") ? 18 : 9; // Set start value based on selection
        $options = [];

        for ($i = $startValue; $i <= 900; $i += 9) {
            $options[] = ['value' => $i, 'text' => $i . ' sqm'];
        }

        return response()->json($options);
    }

    //make ajax call to get the country code based on the country id
    public function getCountryCode(Request $request)
    {
        $countryId = (int) $request->input('country_id');
        //input type is int so check if it is integer or not
        if (!is_int($countryId)) {
            return response()->json(['error' => 'Invalid country ID.']);
        }
        //validate the country id
        if (!Country::where('id', $countryId)->exists()) {
            return response()->json(['error' => 'Invalid country ID.']);
        }
        $country = Country::find($countryId);
        return response()->json([
            'code' => $country->code,
            'id' => $country->id
        ]);
    }
}
