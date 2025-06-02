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
            ->whereIn('submission_status', ['approved', 'submitted'])
            ->whereHas('invoice', function ($query) {
                $query->where('type', 'Stall Booking')->where('payment_status', 'paid');
            })->first();

        //dd($application);

        if (!$application) {
            return redirect()->route('application.exhibitor');
        }

        $application_id = $application->id;

        // Check if the application who has different ticket types stored in the exhibition_participant_passes table





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

        // count of different ticket types in exhibition_participant_passes


        return [
            'complimentary_delegates' => $complimentaryDelegates,
            'stall_manning' => $stallManning,
        ];
    }



    public function list(Request $request, $type)
    {
        $this->__Construct();
        $count = $this->checkCount();

        $application = Application::where('user_id', auth()->user()->id)
            ->whereIn('submission_status', ['approved', 'submitted'])
            ->whereHas('invoice', function ($query) {
                $query->where('type', 'Stall Booking')->where('payment_status', 'paid');
            })->first();

        if (!$application) {
            return redirect('/dashboard');
        }

        $sortField = $request->input('sort', 'first_name');
        $sortDirection = $request->input('direction', 'asc');
        $perPage = $request->input('per_page', 10);

        // Normalize type for DB queries (spaces, case, etc.)
        $type = urldecode($type);
        $ticket_id = null;

        $count = $this->checkCount();
        $used = $this->usedcount();

        if ($type === 'complimentary') {
            $slug = 'Complimentary Delegates';
            $data = DB::table('complimentary_delegates')
                ->where('exhibition_participant_id', $this->checkCount()['exhibition_participant_id'])
                ->orderBy($sortField, $sortDirection)
                ->paginate($perPage);
        } elseif ($type === 'stall_manning') {
            $slug = 'Stall Manning';
            $data = DB::table('stall_manning')
                ->where('exhibition_participant_id', $this->checkCount()['exhibition_participant_id'])
                ->orderBy($sortField, $sortDirection)
                ->paginate($perPage);
        } else {

            // Try to match ticket_type in exhibition_participant_passes -> ticket_categories
            $participant = $application->exhibitionParticipant;
            if (!$participant) {
                return response()->json(['error' => 'No participant found'], 404);
            }
            $ticketCategory = DB::table('ticket_categories')->where('ticket_type', $type)->first();
            if (!$ticketCategory) {
                return response()->json(['error' => 'Invalid type'], 400);
            }
            $pass = DB::table('exhibition_participant_passes')
                ->where('participant_id', $participant->id)
                ->where('ticket_category_id', $ticketCategory->id)
                ->first();
            if (!$pass) {
                return response()->json(['error' => 'No passes found for this category'], 404);
            }

            // Store allocated and used counts for this badge category
            $count = $pass->count ?? $pass->badge_count ?? 0;
            $used = DB::table('complimentary_delegates')
                ->where('exhibition_participant_id', $participant->id)
                ->where('ticket_category_id', $ticketCategory->id)
                ->count();

            // Fetch all complimentary_delegates with this participant and ticket_category_id
            $delegates = DB::table('complimentary_delegates')
                ->where('exhibition_participant_id', $participant->id)
                ->where('ticket_category_id', $ticketCategory->id)
                ->orderBy($sortField, $sortDirection)
                ->paginate($perPage);


            $slug = $type . ' Attendees';
            $data = $delegates;
            $ticket_id = $ticketCategory->id;
        }

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        // dd($count, $used);

        return view(
            'exhibitor.delegates_list',
            compact(
                'data',
                'slug',
                'count',
                'used',
                'ticket_id',
            )
        );
    }


    //invite delegates to the event
    public function invite(Request $request)
    {

        try {
            // Validate request and return JSON error messages
            $validatedData = $request->validate([
                'invite_type' => 'required',
                'email' => 'required|email|unique:complimentary_delegates|unique:stall_manning',
            ]);

            $count = $this->checkCount();
            $participantId = $count['exhibition_participant_id'];
            $inviteType = $request->invite_type;

            // Handle 'delegate' (Complimentary Delegate)
            if ($inviteType === 'delegate') {
                $current = DB::table('complimentary_delegates')
                    ->where('exhibition_participant_id', $participantId)
                    ->count();

                if ($current >= $count['complimentary_delegate_count']) {
                    return response()->json(['error' => 'You have reached the maximum limit of complimentary delegates'], 422);
                }

                $ticketCategoryId = DB::table('ticket_categories')->where('ticket_type', 'delegate')->value('id');
                $token = Str::random(32);

                DB::table('complimentary_delegates')->insert([
                    'email' => $request->email,
                    'exhibition_participant_id' => $participantId,
                    'ticket_category_id' => $ticketCategoryId,
                    'token' => $token,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $companyName = Application::whereHas('exhibitionParticipant', function ($query) use ($participantId) {
                    $query->where('id', $participantId);
                })->value('company_name');

                Mail::to($request->email)->send(new InviteMail($companyName, 'delegate', $token));

                return response()->json(['message' => 'Invitation sent successfully!']);
            }

            // Handle 'exhibitor' (Stall Manning)
            if ($inviteType === 'exhibitor') {
                $current = DB::table('stall_manning')
                    ->where('exhibition_participant_id', $participantId)
                    ->count();

                if ($current >= $count['stall_manning_count']) {
                    return response()->json(['error' => 'You have reached the maximum limit of stall manning'], 422);
                }

                $token = Str::random(32);

                DB::table('stall_manning')->insert([
                    'email' => $request->email,
                    'exhibition_participant_id' => $participantId,
                    'token' => $token,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $companyName = Application::whereHas('exhibitionParticipant', function ($query) use ($participantId) {
                    $query->where('id', $participantId);
                })->value('company_name');

                Mail::to($request->email)->send(new InviteMail($companyName, 'exhibitor', $token));

                return response()->json(['message' => 'Invitation sent successfully!']);
            }

            // Handle custom badge categories (ticket_category_id)
            if (is_numeric($inviteType)) {
                $ticket_category_id = (int)$inviteType;
                $ticketCategory = DB::table('ticket_categories')->where('id', $ticket_category_id)->first();
                if (!$ticketCategory) {
                    return response()->json(['error' => 'Invalid badge category'], 422);
                }

                // Get allowed count for this ticket_category_id from exhibition_participant_passes
                $allowed = DB::table('exhibition_participant_passes')
                    ->where('participant_id', $participantId)
                    ->where('ticket_category_id', $ticket_category_id)
                    ->value('badge_count');

                if (!$allowed) {
                    return response()->json(['error' => 'No allocation found for this badge category'], 422);
                }

                $current = DB::table('complimentary_delegates')
                    ->where('exhibition_participant_id', $participantId)
                    ->where('ticket_category_id', $ticket_category_id)
                    ->count();

                if ($current >= $allowed) {
                    return response()->json(['error' => 'You have reached the maximum limit for this badge category'], 422);
                }

                $token = Str::random(32);

                DB::table('complimentary_delegates')->insert([
                    'email' => $request->email,
                    'exhibition_participant_id' => $participantId,
                    'ticket_category_id' => $ticket_category_id,
                    'token' => $token,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $companyName = Application::whereHas('exhibitionParticipant', function ($query) use ($participantId) {
                    $query->where('id', $participantId);
                })->value('company_name');

                Mail::to($request->email)->send(new InviteMail($companyName, $ticketCategory->ticket_type, $token));

                return response()->json(['message' => 'Invitation sent for custom badge category!']);
            }

            return response()->json(['error' => 'Invalid request'], 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Invite error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    public function invited2($token = null)
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
        if ($token === 'success') {
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

        $notFound = false;
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
            // Validate request and return JSON error messages
            $validatedData = $request->validate([
                'invite_type' => 'required',
                'email' => 'required|email|unique:complimentary_delegates|unique:stall_manning',
                'name' => 'required',
                'phone' => 'required',
                'jobTitle' => 'required',
            ]);

            $count = $this->checkCount();
            $participantId = $count['exhibition_participant_id'];

            if ($request->invite_type == 'delegate') {
                $countComplimentaryDelegates = DB::table('complimentary_delegates')
                    ->where('exhibition_participant_id', $participantId)
                    ->count();

                if ($countComplimentaryDelegates >= $count['complimentary_delegate_count']) {
                    return response()->json(['error' => 'You have reached the maximum limit of complimentary delegates'], 422);
                }

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

            // Handle custom badge categories (dynamic ticket_category_id based on invite_type)
            // If invite_type is not 'delegate' or 'exhibitor', treat it as a custom badge type (ticket_type string)
            if (!in_array($request->invite_type, ['delegate', 'exhibitor'])) {
                // Find ticket_category_id by matching invite_type to ticket_categories.ticket_type
                $ticketCategory = DB::table('ticket_categories')->where('id', $request->invite_type)->first();
                if (!$ticketCategory) {
                    return response()->json(['error' => 'Invalid badge category'], 422);
                }
                $ticket_category_id = $ticketCategory->id;
                // Get allowed count for this ticket_category_id from exhibition_participant_passes
                $allowed = DB::table('exhibition_participant_passes')
                    ->where('participant_id', $participantId)
                    ->where('ticket_category_id', $ticket_category_id)
                    ->value('badge_count');

                if (!$allowed) {
                    return response()->json(['error' => 'No allocation found for this badge category'], 422);
                }

                $current = DB::table('complimentary_delegates')
                    ->where('exhibition_participant_id', $participantId)
                    ->where('ticket_category_id', $ticket_category_id)
                    ->count();

                if ($current >= $allowed) {
                    return response()->json(['error' => 'You have reached the maximum limit for this badge category'], 422);
                }

                $phone = $request->phone;


                DB::table('complimentary_delegates')->insert([
                    'email' => $request->email,
                    'exhibition_participant_id' => $participantId,
                    'ticket_category_id' => $ticket_category_id,
                    'first_name' => $request->name,
                    'mobile' => $request->phone,
                    'job_title' => $request->jobTitle,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return response()->json(['message' => 'Attendee added to custom badge category successfully!']);
            }

            return response()->json(['error' => 'Invalid request'], 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
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
        if (!$application) {
            return redirect('/dashboard');
        }
        //find the invoices of the user
        $invoices = Invoice::where('application_id', $application->id)
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        //if invoices is empty then redirect to /dashbaord
        if ($invoices->isEmpty()) {
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
