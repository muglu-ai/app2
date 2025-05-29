<?php

namespace App\Http\Controllers;

use App\Mail\InviteMail;
use App\Models\Application;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ExhibitorController extends Controller
{

    public function __construct()
    {
        if (auth()->check() && auth()->user()->role == 'exhibitor') {
            return redirect('/login');
        }
    }
    //Show List of Complimentary Exhibitors


    //check whether the has application and check count from exhibition_participants table
    public function checkCount()
    {
        $application = Application::where('user_id', auth()->user()->id)
            ->where('submission_status', 'approved')
            ->whereHas('invoices.payments', function ($query) {
                $query->where('status', 'successful');
            })
            ->first();

        if (!$application) {
            return redirect()->route('application.exhibitor');
        }

        $application_id = $application->id;



        //$exhibitionParticipantCount = $application->exhibitionParticipant()->count();
        $count = [
            'stall_manning_count' => $application->exhibitionParticipant->stall_manning_count,
            'complimentary_delegate_count' => $application->exhibitionParticipant->complimentary_delegate_count,
            'application' => $application_id,
            'exhibition_participant_id' => $application->exhibitionParticipant->id,
        ];

        return $count;

    }

    //get the count of filled complimentary and delegate count from the exhibition_participants table, complimentary_delegates table and complimentary_delegates with the exhibition_participant_id
    public function usedcount()
    {
        $this->__Construct();
        $count = $this->checkCount();
        $complimentaryDelegates = DB::table('complimentary_delegates')
            ->where('exhibition_participant_id', $count['exhibition_participant_id'])
            ->count();

        $stallManning = DB::table('stall_manning')
            ->where('exhibition_participant_id', $count['exhibition_participant_id'])
            ->count();

        return [
            'complimentary_delegates' => $complimentaryDelegates,
            'stall_manning' => $stallManning,
        ];
    }



    public function list(Request $request, $type)
    {
        $this->__Construct();
        $count =$this->checkCount();

        //get the user application id
        $application = Application::where('user_id', auth()->user()->id)
            ->where('submission_status', 'approved')
            ->whereHas('invoices.payments', function ($query) {
                $query->where('status', 'successful');
            })
            ->first();

        //if no application then redirect to /dashboard
        if(!$application){
            return redirect('/dashboard');
        }


        $sortField = $request->input('sort', 'first_name'); // Default sort by 'name'
        $sortDirection = $request->input('direction', 'asc'); // Default sort 'asc'
        $perPage = $request->input('per_page', 10); // Default 10 items per page


        if ($type == 'complimentary') {
            $slug = 'Complimentary Delegates';
            $data = DB::table('complimentary_delegates')
                ->where('exhibition_participant_id', $this->checkCount()['exhibition_participant_id'])
                ->orderBy($sortField, $sortDirection)
                ->paginate($perPage);
        } elseif ($type == 'stall_manning') {
            $slug = 'Stall Manning';
            $data = DB::table('stall_manning')
                ->where('exhibition_participant_id', $this->checkCount()['exhibition_participant_id'])
                ->orderBy($sortField, $sortDirection)
                ->paginate($perPage);
        } else {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        // Check if it's an API request
        if ($request->wantsJson()) {
            return response()->json($data);
        }
        $count = $this->checkCount();
        $used = $this->usedcount();


        return view('exhibitor.delegates_list', compact('data', 'slug', 'count', 'used'));
    }


    //invite delegates to the event
    public function invite2(Request $request)
    {
        $this->__Construct();
        Log::info($request->all());

        $validatedData = $request->validate([
            'invite_type' => 'required|in:delegate,exhibitor',
            'email' => 'required|email|unique:complimentary_delegates|unique:stall_manning',
        ]);
        // check the count of complimentary_delegates or stall_manning table from exhibition_participants table
        //how many of registered delegates or exhibitors are there and it should not exceed the count of complimentary_delegate_count or stall_manning_count
        $count = $this->checkCount();

        //get the count of complimentary_delegates or stall_manning table from exhibition_participants table and
        //check how many has same exhibition_participant_id

        //if invite_type is delegate
        if($request->invite_type == 'delegate') {
            $countComplimentaryDelegates = DB::table('complimentary_delegates')
                ->where('exhibition_participant_id', $count['exhibition_participant_id'])
                ->count();

            if($countComplimentaryDelegates >= $count['complimentary_delegate_count']) {
                return redirect()->back()->with('error', 'You have reached the maximum limit of complimentary delegates');
            }
            else{
                // insert into complimentary_delegates table with email id and exhibition_participant_id also
                // generate a unique token through which the invitee can fill out the information
                $token = Str::random(32);
                DB::table('complimentary_delegates')->insert([
                    'email' => $request->email,
                    'exhibition_participant_id' => $count['exhibition_participant_id'],
                    'token' => $token,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // generate a unique token through which the invitee can fill out the information
                //Mail::to($request->email)->send(new InviteMail($token));
                return response()->json(['message' => 'Invitation sent successfully!']);
            }
        }
        if($request->invite_type == 'exhibitor') {
            $countStallManning = DB::table('stall_manning')
                ->where('exhibition_participant_id', $count['application'])
                ->count();

            if($countStallManning >= $count['stall_manning_count']) {
                return redirect()->back()->with('error', 'You have reached the maximum limit of stall manning');
            }
            else {
                // insert into stall_manning table with email id and exhibition_participant_id also
                // generate a unique token through which the invitee can fill out the information
                // insert into stall_manning table with email id and exhibition_participant_id also
                $token = Str::random(32);
                DB::table('stall_manning')->insert([
                    'email' => $request->email,
                    'exhibition_participant_id' => $count['application'],
                    'token' => $token,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);



                // generate a unique token through which the invitee can fill out the information
                //Mail::to($request->email)->send(new InviteMail($token));

                return response()->json(['message' => 'Invitation sent successfully!']);




            }
        }
    }

    public function invite(Request $request)
    {
  
        try {
            
            // Validate request and return JSON error messages
            $validatedData = $request->validate([
                'invite_type' => 'required|in:delegate,exhibitor',
                'email' => 'required|email|unique:complimentary_delegates|unique:stall_manning',
            ]);
            
            // Fetch counts
            $count = $this->checkCount();
            $participantId = $count['exhibition_participant_id'];

            

            if ($request->invite_type == 'delegate') {
                $countComplimentaryDelegates = DB::table('complimentary_delegates')
                    ->where('exhibition_participant_id', $participantId)
                    ->count();

                if ($countComplimentaryDelegates >= $count['complimentary_delegate_count']) {
                    return response()->json(['error' => 'You have reached the maximum limit of complimentary delegates'], 422);
                }

                // Generate token and insert
                $token = Str::random(32);
                DB::table('complimentary_delegates')->insert([
                    'email' => $request->email,
                    'exhibition_participant_id' => $participantId,
                    'token' => $token,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                

                // Find the exhibition_participant_id from the complimentary_delegates or stall_manning table
                $exhibitionParticipantId = $participantId;

                

                // Find the company name from the application table using exhibition_participant_id
                $companyName = Application::whereHas('exhibitionParticipant', function ($query) use ($exhibitionParticipantId) {
                    $query->where('id', $exhibitionParticipantId);
                })->value('company_name');
                //send an email to the invitee with the token and link as Route::get('/invited/{token}/', [ExhibitorController::class, 'invited'])->name('exhibition.invited');
               
                Mail::to($request->email)->send(new InviteMail($companyName ,$request->invite_type, $token));


                return response()->json(['message' => 'Invitation sent successfully!']);
            }

            if ($request->invite_type == 'exhibitor') {
                Log::info("Invitation mail sent queue 6");
                $countStallManning = DB::table('stall_manning')
                    ->where('exhibition_participant_id', $participantId)
                    ->count();

                if ($countStallManning >= $count['stall_manning_count']) {
                    return response()->json(['error' => 'You have reached the maximum limit of stall manning'], 422);
                }

                // Generate token and insert
                $token = Str::random(32);
                DB::table('stall_manning')->insert([
                    'email' => $request->email,
                    'exhibition_participant_id' => $participantId,
                    'token' => $token,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Log::info("Invitation mail sent queue 7");
                // Find the exhibition_participant_id from the complimentary_delegates or stall_manning table
                $exhibitionParticipantId = $participantId;


                // Find the company name from the application table using exhibition_participant_id
                $companyName = Application::whereHas('exhibitionParticipant', function ($query) use ($exhibitionParticipantId) {
                    $query->where('id', $exhibitionParticipantId);
                })->value('company_name');
            
                //send an email to the invitee with the token and link as Route::get('/invited/{token}/', [ExhibitorController::class, 'invited'])->name('exhibition.invited');
                Mail::to($request->email)->queue(new InviteMail($companyName ,$request->invite_type, $token));
             

                return response()->json(['message' => 'Invitation sent successfully!']);
            }

            return response()->json(['error' => 'Invalid request'], 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors in JSON format
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Log error and return JSON response
            Log::error('Invite error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    public function invited2($token=null)
    {
        $token = $token ?? request('token');
        //if token is not-found then redirect to /invited/not-found
        if ($token == 'not-found') {
            return redirect()->to('/invited/not-found');
        }
        $complimentaryDelegate = DB::table('complimentary_delegates')
            ->where('token', $token)
            ->first();
        //or stall_manning
        $stallManning = DB::table('stall_manning')
            ->where('token', $token)
            ->first();
        //if not found then set a flag to false
        $notFound = false;
        if (!$complimentaryDelegate && !$stallManning) {
            $notFound = true;
        }
        // Find the exhibition_participant_id from the complimentary_delegates or stall_manning table
       if (!$complimentaryDelegate && !$stallManning) {
           //return to invited/not-found
          // redirect('invited/not-found')
           return redirect()->to('/invited/not-found');
             return response()->json(['error' => 'Invalid token or participant not found'], 404);
       }
        $exhibitionParticipantId = $complimentaryDelegate ? $complimentaryDelegate->exhibition_participant_id : $stallManning->exhibition_participant_id;
        // Find the company name from the application table using exhibition_participant_id
        $companyName = Application::whereHas('exhibitionParticipant', function ($query) use ($exhibitionParticipantId) {
            $query->where('id', $exhibitionParticipantId);
        })->value('company_name');



        return view('exhibitor.invited', compact('notFound', 'companyName'));
    }

    public function invited($token = null)
    {
        $token = $token ?? request('token');

        // Prevent infinite redirection loop by checking explicitly for null or 'not-found'
        if (empty($token) || $token === 'not-found') {
            return response()->view('exhibitor.invited', ['notFound' => true], 404);
        }
        if ( $token === 'success') {
            return response()->view('exhibitor.invited', ['notFound' => false, 'token' => 'success'], 400);
        }

        // Check if the token exists in either table
        $complimentaryDelegate = DB::table('complimentary_delegates')->where('token', $token)->first();
        $stallManning = DB::table('stall_manning')->where('token', $token)->first();

        // If no record is found, show 404 page instead of redirecting
        if (!$complimentaryDelegate && !$stallManning) {
            return response()->view('exhibitor.invited', ['notFound' => true], 404);
        }

        // Determine the exhibition_participant_id
        $exhibitionParticipantId = $complimentaryDelegate->exhibition_participant_id ?? $stallManning->exhibition_participant_id;

        // Find the company name from the application table
        $companyName = Application::whereHas('exhibitionParticipant', function ($query) use ($exhibitionParticipantId) {
            $query->where('id', $exhibitionParticipantId);
        })->value('company_name');

        $notFound = false ;
        return view('exhibitor.invited', compact('companyName', 'notFound', 'token'));
    }

    public function inviteeSubmitted(Request $request)
    {
        //dd($request->all());
        $validatedData = $request->validate([
          'token' => [
                'required',
                function ($attribute, $value, $fail) {
                    $existsInComplimentary = DB::table('complimentary_delegates')->where('token', $value)->exists();
                    $existsInStallManning = DB::table('stall_manning')->where('token', $value)->exists();
                    if (!$existsInComplimentary && !$existsInStallManning) {
                        $fail('The selected token is invalid.');
                    }
                },
            ],
            'name' => 'required',
            'fullPhoneNumber' => 'required',
            'jobTitle' => 'required',
        ]);
        //remove the token from the table and insert the data into the table
        $complimentaryDelegate = DB::table('complimentary_delegates')
            ->where('token', $request->token)
            ->first();

        $stallManning = DB::table('stall_manning')
            ->where('token', $request->token)
            ->first();

        if ($complimentaryDelegate) {
            DB::table('complimentary_delegates')
                ->where('token', $request->token)
                ->update([
                    'first_name' => $request->name,
                    'mobile' => $request->fullPhoneNumber,
                    'job_title' => $request->jobTitle,
                    'token' => null,
                    'updated_at' => now(),
                ]);
        }
        if ($stallManning) {
            DB::table('stall_manning')
                ->where('token', $request->token)
                ->update([
                    'first_name' => $request->name,
                    'mobile' => $request->phone,
                    'job_title' => $request->jobTitle,
                    'token' => null,
                    'updated_at' => now(),
                ]);
        }

        //redirect to the invited with message of successful submission route('exhibition.invited', ['token' => $token]) with token as success
        return redirect()->route('exhibition.invited', ['token' => 'success']);


    }

    public function add(Request $request)
    {

        try {

            /**
             * name: document.getElementById('name').value,
             * email: document.getElementById('email').value,
             * phone: fullPhoneNumber,
             * jobTitle: document.getElementById('jobTitle').value,
             * invite_type : document.getElementById('inviteType2').value
             */

            #Log::info($request->all());
            // Validate request and return JSON error messages
            $validatedData = $request->validate([
                'invite_type' => 'required|in:delegate,exhibitor',
                'email' => 'required|email|unique:complimentary_delegates|unique:stall_manning',
                'name' => 'required',
                'phone' => 'required',
                'jobTitle' => 'required',
            ]);


            // Fetch counts
            $count = $this->checkCount();
            $participantId = $count['exhibition_participant_id'];

            if ($request->invite_type == 'delegate') {
                $countComplimentaryDelegates = DB::table('complimentary_delegates')
                    ->where('exhibition_participant_id', $participantId)
                    ->count();

                if ($countComplimentaryDelegates >= $count['complimentary_delegate_count']) {
                    return response()->json(['error' => 'You have reached the maximum limit of complimentary delegates'], 422);
                }

                // Generate token and insert
                $token = Str::random(32);
                DB::table('complimentary_delegates')->insert([
                    'email' => $request->email,
                    'exhibition_participant_id' => $participantId,
                    'first_name' => $request->name,
                    'mobile' => $request->phone,
                    'job_title' => $request->jobTitle,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return response()->json(['message' => 'Delegate added successfully!']);
            }

            if ($request->invite_type == 'exhibitor') {
                $countStallManning = DB::table('stall_manning')
                    ->where('exhibition_participant_id', $participantId)
                    ->count();

                if ($countStallManning >= $count['stall_manning_count']) {
                    return response()->json(['error' => 'You have reached the maximum limit of stall manning'], 422);
                }

                // Generate token and insert
                $token = Str::random(32);
                DB::table('stall_manning')->insert([
                    'first_name' => $request->name,
                    'mobile' => $request->phone,
                    'job_title' => $request->jobTitle,
                    'email' => $request->email,
                    'exhibition_participant_id' => $participantId,
                    'token' => $token,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return response()->json(['message' => 'Exhibitor Delegate added  successfully!']);
            }

            return response()->json(['error' => 'Invalid request'], 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors in JSON format
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Log error and return JSON response
            Log::error('Invite error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    //list all invoices of the exhibitor
    public function invoices(Request $request)
    {
        $this->__Construct();
//        $this->checkCount();

        $sortField = $request->input('sort', 'created_at'); // Default sort by 'created_at'
        $sortDirection = $request->input('direction', 'desc'); // Default sort 'desc'
        $perPage = $request->input('per_page', 10); // Default 10 items per page

        $user_id = auth()->user()->id;



        //find the application id of the user from the applicatiosn table then find the invoices of the user
        //model is defind already in the application model and invoices model
        //find the invoices of the user from the invoices table

        //find the appliocantion id of the user
        $application = Application::where('user_id', $user_id)->first();
        //if not application then redirect to /dashboard
        if(!$application){
            return redirect('/dashboard');
        }
        //find the invoices of the user
        $invoices = Invoice::where('application_id', $application->id)
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        //if invoices is empty then redirect to /dashbaord
        if($invoices->isEmpty()){
            return redirect('/dashboard');
        }

        $in_id = $invoices->pluck('id');

        //store id in a variable
        $in_id = $in_id[0];




        $payments = Payment::where('invoice_id',  $in_id)->get();

        //from this payment check the status of the payment

        //dd($application->invoices);

        // Check if it's an API request
        if ($request->wantsJson()) {
            return response()->json($invoices);
        }

        return view('applications.invoices', compact('invoices', 'application', 'payments'));
    }



}
