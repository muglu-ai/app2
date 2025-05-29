<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendee;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Mail\AttendeeConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Exports\AttendeesExport;
use Maatwebsite\Excel\Facades\Excel;

class AttendeeController extends Controller
{
    //generate unique id  if exist then generate again
    public function generateUniqueId()
    {
        $prefix = config('constants.visitor_registration_unique_id.prefix', 'SM_VST_');
        $uniqueId = uniqid($prefix, true);
        // Check if the ID already exists in the database
        // Assuming you have a Visitor model and a visitors table
        while (Attendee::where('unique_id', $uniqueId)->exists()) {
            $uniqueId = uniqid( $prefix, true);
        }
        return $uniqueId;
    }
    //
    public function showForm()
    {
        $maxAttendees = config('constants.max_attendees');
        $natureOfBusiness = config('constants.sectors');
        $natureOfBusiness = array_map(function ($sector) {
            return ['name' => $sector];
        }, $natureOfBusiness);

        $productCategories = config('constants.product_categories');
        $jobFunctions = config('constants.job_functions');

        return view('attendee.register', compact('maxAttendees', 'natureOfBusiness', 'productCategories', 'jobFunctions'));
    }


    public function visitor_reg(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'attendees' => 'required|array|min:1', // Example for attendees
            'captcha' => 'required|captcha', // Validate captcha
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('captcha')) {
            return redirect()->back()->withErrors(['captcha' => 'Please enter the correct captcha.'])->withInput();
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $attendees = $request->input('attendees');

        // Google reCAPTCHA check
        // $recaptchaResponse = $request->input('g-recaptcha-response');
        // $recaptchaSecret = "6LdNTRorAAAAAGmpwzLuEPV5syp42NDJwkBM4pF4";
        // $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';

        // $recaptchaResponse = file_get_contents($recaptchaUrl . '?secret=' . $recaptchaSecret . '&response=' . $recaptchaResponse);
        // $recaptchaResponseKeys = json_decode($recaptchaResponse, true);

        // if (!$recaptchaResponseKeys['success']) {
        //     return redirect()->back()->withErrors(['recaptcha' => 'reCAPTCHA verification failed.'])->withInput();
        // }

        $maxAttendees = 1;
        if (count($attendees) > $maxAttendees) {
            return redirect()->back()->withErrors(['attendees' => 'You can only register a maximum of ' . $maxAttendees . ' attendee(s).'])->withInput();
        }

        foreach ($attendees as $index => $attendee) {
            $validator = Validator::make($attendee, [
                'title' => 'nullable|string|max:10',
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'designation' => 'required|string|max:100',
                'company' => 'required|string|max:150',
                'address' => 'nullable|string|max:255',
                'country' => 'required|string|max:100',
                'state' => 'nullable|string|max:100',
                'city' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'mobile' => 'required|string|max:20',
                'email' => [
                    'required',
                    'email',
                    'max:100',
                    function ($attribute, $value, $fail) {
                        $tables = ['attendees', 'users', 'complimentary_delegates', 'stall_manning'];
                        foreach ($tables as $table) {
                            if (\DB::table($table)->where('email', $value)->exists()) {
                                $fail('The email has already been taken.');
                            }
                        }
                    },
                ],
                'purpose' => 'required|array|min:1',
                'products' => 'nullable|array',
                'business_nature' => 'nullable|string|max:150',
                'job_function' => 'nullable|string|max:150',
                'consent' => 'required',
                'source' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                // Retain all submitted data using withInput
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Generate unique ID
            do {
                $unique = config('constants.visitor_registration_unique_id.prefix', 'BTS25VI_');
                $uniqueId = $unique . strtoupper(Str::random(6));
            } while (Attendee::where('unique_id', $uniqueId)->exists());

            // Generate QR
            $qrCodePath = public_path('qrcodes/' . $uniqueId . '.png');
            \QrCode::size(200)->format('png')->generate($uniqueId, $qrCodePath);
            $qrCodePath = config('app.url') . str_replace(public_path(), '', $qrCodePath);

            // Save attendee
            Attendee::create([
                'unique_id' => $uniqueId,
                'status' => 'pending',
                'badge_category' => 'Visitor',
                'title' => $attendee['title'] ?? null,
                'first_name' => $attendee['first_name'],
                'last_name' => $attendee['last_name'],
                'designation' => $attendee['designation'],
                'company' => $attendee['company'],
                'address' => $attendee['address'] ?? null,
                'country' => $attendee['country'],
                'state' => $attendee['state'] ?? null,
                'city' => $attendee['city'] ?? null,
                'postal_code' => $attendee['postal_code'] ?? null,
                'mobile' => $attendee['mobile'],
                'email' => $attendee['email'],
                'purpose' => $attendee['purpose'],
                'products' => $attendee['products'] ?? [],
                'business_nature' => $attendee['business_nature'] ?? null,
                'job_function' => $attendee['job_function'] ?? null,
                'consent' => $attendee['consent'] === 'on' ? true : false,
                'qr_code_path' => $qrCodePath,
            ]);

            // Send email
            $data = [
                'unique_id' => $uniqueId,
                'email' => $attendee['email'],
                'qr_code_path' => $qrCodePath,
                'name' => $attendee['first_name'] . ' ' . $attendee['last_name'],
                'ticket_type' => 'Visitor',
            ];
            \Mail::to($attendee['email'])
                ->bcc(config('constants.visitor_emails', []))
                ->queue(new \App\Mail\AttendeeConfirmationMail($data));
        }

        return redirect()->route('visitor_thankyou', ['id' => $uniqueId]);
    }


    public function thankyou($id)
    {
        $attendee = Attendee::where('unique_id', $id)->firstOrFail();

        return view('attendee.thankyou', [
            'attendee' => $attendee,
            'qrCode' => $attendee->qr_code_path,
        ]);
    }

    ///list of all attendees to admin
    public function listAttendees(Request $request)
    {
        //if not logged in then return to login page
        $user = auth()->user();
        //if not user is logged in then redirect to login page
        if (!auth()->check()) {
            return redirect('/login');
        }

        $attendees = Attendee::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $attendees->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('company', 'like', "%$search%")
                    ->orWhere('unique_id', 'like', "%$search%");
            });
        }

        $attendees = $attendees->paginate(10);
        $slug = "Attendee List";

        return view('attendee.attendee_list', compact('attendees', 'slug'));
    }
    public function export()
    {
        $filename = 'attendees_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new AttendeesExport, $filename);
    }


}
