<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceMail;
use App\Models\BillingDetail;
use App\Models\EventContact;
use App\Models\SecondaryEventContact;
use App\Models\ProductCategory;
use App\Models\Sector;
use App\Models\Sponsorship;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Application;
use App\Http\Middleware\Auth;
use App\Helpers\ExhibitorPriceCalculator;
use App\Models\Invoice;
use App\Models\Country;
use App\Models\State;
use App\Http\Controllers\MailController;
use App\Models\DeletedBillingDetail;
use App\Models\DeletedEventContact;
use App\Models\DeletedApplication;
use App\Models\DeletedSecondaryEventContact;
//log
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;



class AdminController extends Controller
{


    //call the middleware to check if user is logged in
    // public function __construct()
    // {
    //     if (auth()->check() && auth()->user()->role !== 'admin') {
    //         return redirect('/login');
    //     }

    // }

     public function __construct()
    {
        $this->middleware([ 'admin']);
    }


    public function getUsers(Request $request)
    {
        // Check if the user is logged in and has an admin role
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $sortField = $request->input('sort', 'name'); // Default sort by 'name'
        $sortDirection = $request->input('direction', 'asc'); // Default sort 'asc'
        $perPage = $request->input('per_page', 10); // Default 10 items per page

        $users = User::orderBy($sortField, $sortDirection)->paginate($perPage);

        return response()->json($users);
    }


    //return view at admin.test
    public function test()
    {
        return view('admin.test');
    }

    //fetch all application list
    public function index($status = null)
    {
        //check user is logged in or not
        if (!auth()->check()) {
            return redirect('/login');
        }
        $slug = 'Application List';

        if ($status) {
            if ($status == 'in-progress') {
                $status = 'in progress';
            }
            $slug = $status . ' - Application List ';
//            $applications = Application::with('eventContact')->where('submission_status', $status)->whereDoesntHave('sponsorships')->get();
            $applications = Application::with('eventContact')->where('submission_status', $status)->where('application_type', 'exhibitor')->get();
        } else {
            $applications = Application::with('eventContact')->get();
            // $applications = Application::with('eventContact')->whereDoesntHave('sponsorships')->get();
        }

        if($status == 'approved'){
        //    $applications = Application::with('eventContact', 'invoice')->where('submission_status', 'approved')->whereDoesntHave('sponsorships')->get();
            // $applications = Application::with('eventContact', 'invoice')->where('submission_status', 'approved')->where('application_type', 'exhibitor')->get();
            $applications = Application::with('eventContact', 'invoice')->where('submission_status', 'approved')->get();

            // dd($applications);
            //total revenue from all approved applications from price field in invoice table
            $totalRevenue = Invoice::where('type', 'Stall Booking')->where('payment_status', 'unpaid')->sum('price');

            return view('dashboard.approved_list', compact('applications', 'slug', 'totalRevenue'));
        }


        return view('dashboard.list', compact('applications', 'slug'));
    }

        public function applicationUpdate(Request $request, $id)
        {

            // dd($request->all());
            // Validate the incoming request data
            $request->validate([
                'company_name' => 'required|string|max:255',
                'website' => 'nullable|url',
                'address' => 'required|string|max:255',
                'postal_code' => 'required|string|max:20',
                'main_product_category' => 'required|exists:product_categories,id',
                'type_of_business' => 'required|string|max:255',
                'sectors' => 'nullable|array',
                'sectors.*' => 'exists:sectors,id',
                'stall_category' => 'nullable|string|max:255',
                'interested_sqm' => 'nullable|integer',
                'allocated_sqm' => 'nullable|integer',
                'semi_member' => 'nullable|string',
                'semi_memberID' => 'nullable|string|max:100',
                'event_contact_name' => 'nullable|string|max:255',
                'event_contact_design' => 'nullable|string|max:255',
                'event_contact_email' => 'nullable|email',
                'event_contact_mobile' => 'nullable|string|max:20',
                'secondary_contact_name' => 'nullable|string|max:255',
                'secondary_contact_design' => 'nullable|string|max:255',
                'secondary_contact_email' => 'nullable|email',
                'secondary_contact_mobile' => 'nullable|string|max:20',
                'gst_compliance' => 'nullable|string',
                'gst_no' => 'nullable|string|max:20',
                'pan_no' => 'nullable|string|max:20',
                'billing_company' => 'nullable|string|max:255',
                'contact_name' => 'nullable|string|max:255',
                'billing_email' => 'nullable|email',
                'billing_phone' => 'nullable|string|max:20',
                'billing_address' => 'nullable|string|max:255',
                'billing_city' => 'nullable|string|max:255',
                'billing_state' => 'nullable|exists:states,id',
                'billing_country' => 'nullable|exists:countries,id',
            ]);

            // Find the application
            $application = Application::findOrFail($id);

            // Update the basic fields
            $application->company_name = $request->company_name ?? $application->company_name;
            $application->website = $request->website ?? $application->website;
            $application->address = $request->address ?? $application->address;
            $application->postal_code = $request->postal_code ?? $application->postal_code;
            $application->main_product_category = $request->main_product_category ?? $application->main_product_category;
            $application->type_of_business = $request->type_of_business ?? $application->type_of_business;
            $application->sector_id = json_encode($request->sectors) ; // Save as JSON

            // Exhibition Info
            $application->stall_category = $request->stall_category ?? $application->stall_category;
            $application->interested_sqm = $request->interested_sqm ?? $application->interested_sqm;
            $application->allocated_sqm = $request->allocated_sqm ?? $application->allocated_sqm;
            $application->semi_member = $request->semi_member == 'Yes' ? 1 : 0;
            $application->semi_memberID = $request->semi_memberID ?? $application->semi_memberID;

            // Update Event Contact Person
            if ($request->has('event_contact_name')) {
                $eventContact = $application->eventContact;
                // Split the name by comma to separate name and job title
                // $nameParts = explode(',', $request->event_contact_name);

                // Handle cases where the name might have a space-separated first and last name
                // $fullName = trim($nameParts[0]);
                $nameArray = explode(' ', $request->event_contact_name);
                $eventContact->first_name = array_shift($nameArray); // First name is the first part
                $eventContact->last_name = implode(' ', $nameArray); // Remaining parts as last name

                // Assign first name and last name
                // $eventContact->first_name = isset($nameArray[0]) ? $nameArray[0] : $application->eventContact->first_name;
                // $eventContact->last_name = isset($nameArray[1]) ? $nameArray[1] : $application->eventContact->last_name;

                // Assign job title from the second part after the comma
                $eventContact->job_title = $request->event_contact_design ?? $application->eventContact->job_title;


                $eventContact->email = $request->event_contact_email ?? $application->eventContact->email;
                $eventContact->contact_number = $request->event_contact_mobile ?? $application->eventContact->contact_number;
                $eventContact->save();
            }

            // Update Secondary Event Contact
            if ($request->has('secondary_contact_name')) {
                $secondaryContact = $application->secondaryEventContact;
                // $nameParts = explode(' ', $request->secondary_contact_name);

                // Handle cases where the name might have a space-separated first and last name
                // $fullName = trim($nameParts[0]);
                $nameArray = explode(' ', $request->secondary_contact_name);
                $secondaryContact->first_name = array_shift($nameArray); // First name is the first part
                $secondaryContact->last_name = implode(' ', $nameArray); // Remaining parts as last name

                // Assign first name and last name
                // $secondaryContact->first_name = isset($nameArray[0]) ? $nameArray[0] : $application->secondaryEventContact->first_name;
                // $secondaryContact->last_name = isset($nameArray[1]) ? $nameArray[1] : $application->secondaryEventContact->last_name;

                // Assign job title from the second part after the comma
                $secondaryContact->job_title = $request->secondary_contact_design ?? $application->secondaryEventContact->job_title;
                $secondaryContact->email = $request->secondary_contact_email ?? $application->secondaryEventContact->email;
                $secondaryContact->contact_number = $request->secondary_contact_mobile ?? $application->secondaryEventContact->contact_number;
                $secondaryContact->save();
            }

            // Update Company Details
            $application->gst_compliance = $request->gst_compliance == 'Yes' ? 1 : 0;
            $application->gst_no = $request->gst_no ?? $application->gst_no;
            $application->pan_no = $request->pan_no ?? $application->pan_no;

            // Update Billing Details
            $billingDetails = $application->billingDetail;
            if ($billingDetails) {
                $billingDetails->billing_company = $request->billing_company ?? $billingDetails->billing_company;
                $billingDetails->contact_name = $request->contact_name ?? $billingDetails->contact_name;
                $billingDetails->email = $request->billing_email ?? $billingDetails->email;
                $billingDetails->phone = $request->billing_phone ?? $billingDetails->phone;
                $billingDetails->address = $request->billing_address ?? $billingDetails->address;
                $billingDetails->city_id = $request->billing_city ?? $billingDetails->city_id;
                $billingDetails->state_id = $request->billing_state ?? $billingDetails->state_id;
                $billingDetails->country_id = $request->billing_country ?? $billingDetails->country_id;
                $billingDetails->save();
            } else {
                // Optionally, handle the case where billing details are missing
                Log::error('Billing details not found for application ID: ' . $application->id);
                return redirect()->back()->withErrors(['error' => 'Billing details not found for this application.']);
            }

            // Save the application
            $application->save();

            // Redirect with success message
            return redirect()->back()->with('success', 'Application information updated successfully!');
        }


    //users list
    public function users()
    {
        //check user is logged in or not
        // if (!auth()->check()) {
        //     return redirect('/login');
        // }
        $slug = 'Users List';
        $users = User::all();
        return view('dashboard.users', compact('users', 'slug'));
    }

    //Approving the application with application id and updating application_status to approved
    //calculating the total amount of application and updating the total_amount field in event_contact table
    


    public function approve(Request $request)
    {
        //check user is logged in or not
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect('/login');
        }
        //log the request
        //Log::info('Approve Application Request', $request->all());
        //validate the id from request from application model exist or not
        $request->validate([
            'id' => 'required|exists:applications,id',
            'isPavilion' => 'required|boolean',
            'allocateSqm' => 'required|string',
            'stallNumber' => 'required|string',
            'boothType' => 'required|string',
            'booth_cat' => 'required|string',
        ]);

        $allocateSqm = intval($request->allocateSqm);
        $id = $request->input('id');
        $application = Application::find($id);

        // check if the application is already approved
        if ($application->submission_status == 'approved') {
            return response()->json(['message' => 'Application Already Approved', 'application_id' => $application->id, 'company_name' => $application->company_name]);
        }
        $nos = 1;
        $region = $application->region;
        $membershipType = $application->membership_verified == 1 ? 'SEMI' : 'Non-SEMI';
        $boothType = $request->boothType;
        $stallType = $application->stall_category;
        $booth_cat = $request->booth_cat;



        //define the early bird date and regular date
        //define the early bird date and regular date
        $earlyBirdDate = '2025-03-31';
        $regularDate = '2025-04-01';
        $submissionDate = Carbon::parse($application->submission_date);
        $earlyBird = $submissionDate->lte(Carbon::parse($earlyBirdDate));

        //if early bird then store value in $earlybird 'Early Bird' : 'Regular';
        $earlyBird = $earlyBird ? 'Early Bird' : 'Regular';


        $currencyType = $application->billingDetail->country->name != 'India' ? 'EUR' : 'INR';


        $currencyType = $application->payment_currency;
        $stallSize = $allocateSqm;

        //dd($membershipType, $boothType, $stallType, $earlyBird, $currencyType);

        //$price = ExhibitorPriceCalculator::calculatePrice($stallSize, $membershipType, $boothType, $stallType, $earlyBird, $currencyType  );        //create new invoice for the application
        $application->submission_status = 'approved';
        $application->approved_date = now();
        //is_pavilion from request isPavilion
        $application->is_pavilion = $request->isPavilion;
        //allocated_sqm from request allocated_sqm
        $application->allocated_sqm = $allocateSqm;
        $application->stallNumber = $request->stallNumber;
        $application->pref_location = $request->boothType;
        $application->stall_category = $booth_cat;
        $application->save();

        $stallType = $application->stall_category;
        $boothType = $application->pref_location;
        $membershipType = $application->membership_verified == 1 ? 'SEMI' : 'Non-SEMI';
        $stallSize = $allocateSqm;

        $eventContact = EventContact::find($application->event_contact_id);
        //calculatePrice
        Log::info('Logging details', [
            'stallSize' => $stallSize,
            'membershipType' => $membershipType,
            'boothType' => $boothType,
            'stallType' => $stallType,
            'earlyBird' => $earlyBird,
            'currencyType' => $currencyType,
        ]);
        $price = ExhibitorPriceCalculator::calculatePrice($stallSize, $membershipType, $boothType, $stallType, $earlyBird, $currencyType );
        //create new invoice for the application
        $amount = $price['final_total_price'];
        $processingCharges = $price['processing_charges'];
        $gst = $price['gst'];
        $discount = $price['discount'];
        $actual_price = $price['actual_price'];
        //if application_id with same application_id and type is Stall Booking exist then update the invoice else create new invoice
        $invoice = Invoice::where('application_id', $application->id)
            ->where('type', 'Stall Booking')
            ->first();
        if ($invoice) {
            // Update existing invoice
            $invoice->amount = $amount;
            $invoice->pending_amount = 0;
            $invoice->price = $actual_price;
            $invoice->processing_charges = $processingCharges;
            $invoice->gst = $gst;
            $invoice->total_final_price = $amount;
            $invoice->currency = $currencyType;
            $invoice->payment_status = 'unpaid';
            $invoice->payment_due_date = now()->addDays(5);
            $invoice->discount_per = 0;
        } else {
            // Create new invoice
            $invoice = new Invoice();
            $invoice->application_id = $application->id;
            $invoice->type = 'Stall Booking';
            $invoice->amount = $amount;
            $invoice->pending_amount = 0;
            $invoice->price = $actual_price;
            $invoice->processing_charges = $processingCharges;
            $invoice->gst = $gst;
            $invoice->total_final_price = $amount;
            $invoice->currency = $currencyType;
            $invoice->payment_status = 'unpaid';
            $invoice->payment_due_date = now()->addDays(5);
            $invoice->discount_per = 0;
            $invoice->application_no = $application->application_id;
            do {
                $randomNumber = mt_rand(10000, 99999);
                $invoiceNo = 'SEC-INV-' . $randomNumber;
            } while (Invoice::where('invoice_no', $invoiceNo)->exists());

            $invoice->invoice_no = $invoiceNo;
        }

        $invoice->save();
        $to= $application->eventContact->email;
        $application_id = $application->application_id;

        //send email to applicant with approval
        //send a post request to send email with email_type as submission and to as applicant email
        $recipients = is_array($to) ? $to : [$to];
        // Add admin emails from config/constants
        $adminEmails = config('constants.admin_emails', []);
        foreach ($adminEmails as $adminEmail) {
            if (!in_array($adminEmail, $recipients)) {
            $recipients[] = $adminEmail;
            }
        }
        Mail::to($recipients[0])->bcc(array_slice($recipients, 1))->queue(new InvoiceMail($application_id));

        //return success message with approved application id
        //return json response with success message
        return response()->json(['message' => 'Application Approved and Invoice Generated for', 'application_id' => $application->id, 'company_name' => $application->company_name]);
        //send email to applicant with approval

        //send email after approval to billing person with payment link

        //return redirect back with success message with applicant id is approved and invoice is generated with
        return redirect()->back()->with('success', 'Application Approved and Invoice Generated');
    }

    public function sponsorship_approve(Request $request)
    {
        //check user is logged in or not
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect('/login');
        }

        //log the request
        Log::info('Approve Sponsor Application Request Validated', $request->all());
        //validate the id from request from application model exist or not
        $request->validate([
            'id' => 'required|exists:applications,id',
            'sponsorship_id' => 'required|exists:sponsorships,id',
        ]);

        //log validated data
        Log::info('Approve Sponsor Application Request Validated', $request->all());



        $id = $request->input('id');


        $application = Application::find($id);
        $nos = 1;

        $price = ExhibitorPriceCalculator::calculatePrice($application->interested_sqm, $application->stall_category, $nos, 0);

        $application->submission_status = 'approved';
        $application->approved_date = now();
        //is_pavilion from request isPavilion
        $application->is_pavilion = $request->isPavilion;
        //allocated_sqm from request allocated_sqm
        $application->allocated_sqm = $request->allocateSqm;
        $application->save();
        $eventContact = EventContact::find($application->event_contact_id);
        //calculatePrice
        $price = ExhibitorPriceCalculator::calculatePrice($application->interested_sqm, $application->stall_category, $nos, 0);        //create new invoice for the application
        $amount = $price['final_total_price'];
        $processingCharges = $price['processing_charges'];
        $gst = $price['gst'];
        $discount = $price['discount'];
        $actual_price = $price['actual_price'];


        //if application_id with same application_id and type is Stall Booking exist then update the invoice else create new invoice


        $invoice = Invoice::where('application_id', $application->id)
            ->where('type', 'Stall Booking')
            ->first();

        if ($invoice) {
            // Update existing invoice
            $invoice->amount = $amount;
            $invoice->pending_amount = 0;
            $invoice->price = $actual_price;
            $invoice->processing_charges = $processingCharges;
            $invoice->gst = $gst;
            $invoice->total_final_price = $amount;
            $invoice->currency = 'INR';
            $invoice->payment_status = 'unpaid';
            $invoice->payment_due_date = now()->addDays(5);
            $invoice->discount_per = 0;
        } else {
            // Create new invoice
            $invoice = new Invoice();
            $invoice->application_id = $application->id;
            $invoice->type = 'Stall Booking';
            $invoice->amount = $amount;
            $invoice->pending_amount = 0;
            $invoice->price = $actual_price;
            $invoice->processing_charges = $processingCharges;
            $invoice->gst = $gst;
            $invoice->total_final_price = $amount;
            $invoice->currency = 'INR';
            $invoice->payment_status = 'unpaid';
            $invoice->payment_due_date = now()->addDays(5);
            $invoice->discount_per = 0;
            $invoice->application_no = $application->application_id;
            do {
                $randomNumber = mt_rand(10000, 99999);
                $invoiceNo = 'SEC-INV' . $randomNumber;
            } while (Invoice::where('invoice_no', $invoiceNo)->exists());

            $invoice->invoice_no = $invoiceNo;
        }

        $invoice->save();

        //return success message with approved application id
        //return json response with success message
        return response()->json(['message' => 'Application Approved and Invoice Generated', 'application_id' => $application->id, 'company_name' => $application->company_name]);
        //send email to applicant with approval

        //send email after approval to billing person with payment link

        //return redirect back with success message with applicant id is approved and invoice is generated with
        return redirect()->back()->with('success', 'Application Approved and Invoice Generated');
    }

    //Reject the application with application id and updating application_status to rejected
    public function reject(Request $request)
    {
        //check user is logged in or not
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect('/login');
        }


        Log::info('Reject Application Request', $request->all());

        $id = $request->input('id');

        $application = Application::find($id);
        $application->submission_status = 'rejected';
        $application->rejection_reason = $request->reason;
        $application->rejected_date = now();
        $application->save();

        //return success message with rejected application id
        return response()->json(['message' => 'Application Rejected', 'application_id' => $application->id, 'company_name' => $application->company_name]);
    }

    public function sponsorship_reject(Request $request)
    {
        //check user is logged in or not
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect('/login');
        }

        $request->validate([
            'id' => 'required|exists:applications,id',
            'sponsorship_id' => 'required|exists:sponsorships,id',
            'reason' => 'string|nullable',
        ]);


        Log::info('Reject Application Request', $request->all());

        $id = $request->input('id');

        $application = Application::find($id);

        $sponsorship = Sponsorship::find($request->sponsorship_id);
        //check if the application is already approved
        if ($sponsorship->status == 'approved') {
            return response()->json(['message' => 'Application Already Approved', 'application_id' => $application->id, 'company_name' => $application->company_name]);
        }
        $sponsorship->status = 'rejected';
        $sponsorship->approval_date = now();
        $sponsorship->save();

        $application->submission_status = 'rejected';
        $application->rejection_reason = $request->reason;
        $application->rejected_date = now();
        $application->save();

        //return success message with rejected application id
        return response()->json(['message' => 'Application Rejected', 'application_id' => $application->id, 'company_name' => $application->company_name]);
    }


    //sponsor application list
    public function sponsorApplicationList($status = null)
    {
        //check user is logged in or not
        if (!auth()->check()) {
            return redirect('/login');
        }
        $slug = 'Sponsor Application List';
        //check status and query the application with status
        if ($status) {
            if ($status == 'in-progress') {
                $status = 'initiated';
            }
            $slug = $status . ' - Sponsor Application List ';

            $applications = Application::with('eventContact', 'sponsorship')->whereHas('sponsorship', function ($query) use ($status) {
                $query->where('status', $status);
            })->get();

        } else {
            $applications = Application::with('eventContact', 'sponsorship')->whereHas('sponsorship')->get();

        }

        //dd($applications , Application::first()->sponsorship()->count());
        //$applications = Application::with('eventContact')->whereHas('sponsorships')->get();
        return view('dashboard.sponsorship-list', compact('applications', 'slug'));
    }

    //application info by id
    public function applicationView(Request $request)
    {
        //check if the user is logged in or not
        if (!auth()->check()) {
            return redirect('/login');
        }
        $this->__construct();
        //from the auth user take the application id and get the details of the application
        //get the application id from the request
        $applicationId = $request->application_id;
       // dd($applicationId);
        $productCategories = ProductCategory::select('id', 'name')->get();

        //get application details from application model
        $application = Application::where('application_id', $applicationId)->first();
        //if not application return to route dashboard.admin
        if (!$application) {
            return redirect()->route('dashboard.admin');
        }
        $app_id = $application->id;
        //get invoice details from invoice model
        $invoice = Invoice::where('application_id', $applicationId)->first();
        //billing details from billing detail model
        $billingDetails = BillingDetail::where('application_id', $app_id)->first();

       // dd($billingDetails);
        //event contact details from event contact model
        $eventContact = EventContact::where('application_id', $app_id)->first();
        $sectors = Sector::all();

        $countries = Country::all();
        $states = State::all();


        return view('admin.application_preview', compact('application', 'invoice', 'billingDetails', 'eventContact', 'productCategories', 'sectors', 'countries', 'states'));
    }



    //verify the membership of the user with application id and semi_member to 1
    public function verifyMembership(Request $request)
    {
        //check if the user is logged in or not
        if (!auth()->check()) {
            return redirect('/login');
        }
        Log::info('Verify Membership Request', $request->all());

        //validate the request
        $request->validate([
            'application_id' => 'required|exists:applications,application_id',
        ]);

        //get the application id from the request
        $id = $request->input('application_id');

        //get the application details from application model
        $application = Application::where('application_id', $id)->first();
        //set the semi_member to 1
        $application->semi_member = 1;
        $application->membership_verified=1;
        $application->save();

        //return success message with verified application id
        return response()->json(['message' => 'Membership Verified', 'application_id' => $application->id, 'company_name' => $application->company_name]);
    }

    //unverify the membership of the user with application id and semi_member to 0
    public function unverifyMembership(Request $request)
    {
        //check if the user is logged in or not
        if (!auth()->check()) {
            return redirect('/login');
        }

        //validate the request
        $request->validate([
            'application_id' => 'required|exists:applications,application_id',
        ]);

        //get the application id from the request
        $id = $request->input('application_id');

        //get the application details from application model
        $application = Application::where('application_id', $id)->first();
        //set the semi_member to 0
        $application->membership_verified = 0;
        $application->save();

        //return success message with unverified application id
        return response()->json(['message' => 'Membership Unverified', 'application_id' => $application->id, 'company_name' => $application->company_name]);
    }


    //copy to delete table
    public function copy(Request $request) {
        // Validate the id from the request to ensure it exists in the applications table
        $request->validate([
            'id' => 'required|exists:applications,application_id',
        ]);

        DB::beginTransaction(); // Start a transaction to ensure data integrity



        try {
            $application = Application::where('application_id', $request->id)->firstOrFail();
            // Step 1: Copy and Delete SecondaryEventContact
            $secondaryContacts = SecondaryEventContact::where('application_id', $application->id)->get();
            foreach ($secondaryContacts as $contact) {
                DeletedSecondaryEventContact::create($contact->toArray());
                $contact->delete();
            }

            // Step 2: Copy and Delete EventContact
            $eventContacts = EventContact::where('application_id', $application->id)->get();
            foreach ($eventContacts as $contact) {
                DeletedEventContact::create($contact->toArray());
                $contact->delete();
            }

            // Step 3: Copy and Delete BillingDetail
            $billingDetails = BillingDetail::where('application_id', $application->id)->get();
            foreach ($billingDetails as $billing) {
                DeletedBillingDetail::create($billing->toArray());
                $billing->delete();
            }

            // Step 4: Copy and Delete Application

            DeletedApplication::create($application->toArray());
            $application->delete();

            DB::commit(); // Commit the transaction if all operations succeed

            return response()->json(['message' => 'Application and related data copied and deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction if any error occurs
            Log::error('Error in copy and delete process: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to complete copy and delete process'], 500);
        }
    }




}
