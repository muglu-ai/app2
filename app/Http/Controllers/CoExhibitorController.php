<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CoExhibitor;
use App\Models\User;
use App\Models\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
//use App\Mail\AdminNotificationMail;
use Illuminate\Support\Facades\Hash;
use App\Mail\CoExhibitorApprovalMail;

class CoExhibitorController extends Controller
{

    //user_list for main exhibitor to display list of all co exhibitors
    public function user_list()
    {
        //find co exhibitors for the logged in user or applicationid
        //get the user id
        $userID = auth()->user()->id;
        //get the application id with user_Id
        $application = Application::where('user_id', $userID)->first();
        $application_id = $application->id;

        $coExhibitors = CoExhibitor::where('application_id',$application_id )->get();
        //$application = Application::find(auth()->user()->application_id);

        return view('exhibitor.co_exhibitors', compact('coExhibitors', 'application'));
    }


    public function store(Request $request)
    {

            Log::info('Co-Exhibitor request', $request->all());
        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'co_exhibitor_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
        ]);

        // Store the co-exhibitor with status = pending
        $coExhibitor = CoExhibitor::create([
            'application_id' => $request->application_id,
            'co_exhibitor_name' => $request->co_exhibitor_name,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => 'pending',
        ]);

        // Notify admin
        $adminEmail = "admin@example.com"; // Change to your admin email
        //Mail::to($adminEmail)->send(new AdminNotificationMail($coExhibitor));

        return response()->json(['message' => 'Co-Exhibitor request submitted for approval!'],
            201);
    }


    //list of all co-exhibitors for admin
    public function index()
    {
        $coExhibitors = CoExhibitor::all();

        //return view from admin.co_exhibitors
        return view('admin.co_exhibitors', compact('coExhibitors'));
    }



    public function approve(Request $request, $id)
    {
        $coExhibitor = CoExhibitor::findOrFail($id);

        if ($coExhibitor->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 400);
        }

        // Generate a random password
        $password = substr(md5(uniqid()), 0, 10);

        // Create a new user for co-exhibitor
       try {
            $user = User::create([
                'name' => $coExhibitor->co_exhibitor_name,
                'email' => $coExhibitor->email,
                'password' => Hash::make($password),
                'role' => 'co-exhibitor', // Assuming roles exist
                'email_verified_at' => now(),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json(['error' => 'Email already exists'], 400);
            }
            return response()->json(['error' => 'Failed to create user'], 500);
        }

        // Update co-exhibitor status
        $coExhibitor->update(['status' => 'approved']);
       //also send that you are exhibiting under
        $application = Application::find($coExhibitor->application_id);
        $exhibiting_under = $application->company_name;


        // Send email with login credentials
        Mail::to($coExhibitor->email)->send(new CoExhibitorApprovalMail($coExhibitor, $password, $exhibiting_under));

        return response()->json(['message' => 'Co-Exhibitor Approved & Credentials Sent!']);
    }

    public function reject($id)
    {
        $coExhibitor = CoExhibitor::findOrFail($id);

        if ($coExhibitor->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 400);
        }

        // Update status to rejected
        $coExhibitor->update(['status' => 'rejected']);

        return response()->json(['message' => 'Co-Exhibitor Request Rejected']);
    }

}


