<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ExhibitionParticipant;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    //
    //construct function to check if user is logged in
    public function __construct()
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return redirect('/login');
        }
    }
    public function exhibitorDashboard()
    {
        //fetch user type and send to that dashboard

        $user = auth()->user();
        //if not user is logged in then redirect to login page
        if (!auth()->check()) {
            return redirect('/login');
        }
        if ($user->role == 'exhibitor') {



            $application = Application::where('user_id', auth()->user()->id)
                ->whereIn('submission_status', ['approved', 'submitted'])
                ->whereHas('invoice', function ($query) {
                    $query->where('type', 'Stall Booking')->where('payment_status', 'paid');
                })->first();

            //if application is null redirect to event list  name event.list
            if (!$application) {
                return redirect()->route('event.list');
            }
            //get the no of exhibitors and delegate from the exhibitionParticipation table who's id is application id with same user id
            //get the application id from the application table where user id is same as the logged in user id
            // $applicationId = Application::where('user_id', auth()->id())->value('id');
            //get the application
            $application = Application::where('user_id', auth()->id())->first();

            // dd($application->id);

            //get the exhibitor and delegate count from the exhibitionParticipation table where application id is same as the application id
            $exhibitionParticipant = ExhibitionParticipant::where('application_id', $application->id)->first();
            // dd($exhibitionParticipant);

            return view('dashboard.index', compact('exhibitionParticipant', 'application'));
            return view('dashboard.index');
        } elseif ($user->role == 'admin') {
            $analytics = app('analytics');
            $submittedApplications = $analytics['applicationsByStatus']['submitted'] ?? 0;
            $approvedApplications = $analytics['applicationsByStatus']['approved'] ?? 0;
            $rejectedApplications = $analytics['applicationsByStatus']['rejected'] ?? 0;
            $inProgressApplications = $analytics['applicationsByStatus']['in progress'] ?? 0;
            $totalApplications = $submittedApplications + $approvedApplications + $rejectedApplications + $inProgressApplications;

            return view('dashboard.admin', compact('analytics'));
        }








        return view('exhibitor.dashboard');
    }
    public function exhibitorDashboard_new()
    {
        //fetch user type and send to that dashboard

        $user = auth()->user();
        //if not user is logged in then redirect to login page
        if (!auth()->check()) {
            return redirect('/login');
        }
        if ($user->role == 'exhibitor') {
            $application = Application::where('user_id', auth()->user()->id)
                ->where('submission_status', 'approved')
                ->whereHas('invoices.payments', function ($query) {
                    $query->where('status', 'successful');
                })
                ->first();

            //if application is null redirect to event list  name event.list
            if (!$application) {
                return redirect()->route('event.list');
            }
            //get the no of exhibitors and delegate from the exhibitionParticipation table who's id is application id with same user id
            //get the application id from the application table where user id is same as the logged in user id
            $applicationId = Application::where('user_id', auth()->id())->value('id');
            //get the application
            $application = Application::where('user_id', auth()->id())->first();
            //get the exhibitor and delegate count from the exhibitionParticipation table where application id is same as the application id
            $exhibitionParticipant = ExhibitionParticipant::where('application_id', $applicationId)->first();

            return view('dashboard.index', compact('exhibitionParticipant', 'application'));
            return view('dashboard.index');
        } elseif ($user->role == 'admin') {
            $analytics = app('analytics');
            $submittedApplications = $analytics['applicationsByStatus']['submitted'] ?? 0;
            $approvedApplications = $analytics['applicationsByStatus']['approved'] ?? 0;
            $rejectedApplications = $analytics['applicationsByStatus']['rejected'] ?? 0;
            $inProgressApplications = $analytics['applicationsByStatus']['in progress'] ?? 0;
            $totalApplications = $submittedApplications + $approvedApplications + $rejectedApplications + $inProgressApplications;

            // Fetch applications grouped by billing country, excluding applications in sponsorships
            try {
                $applicationsByCountry = DB::table('applications as a')
                    ->join('countries as c', 'a.billing_country_id', '=', 'c.id')
                    ->leftJoin('sponsorships as s', 'a.id', '=', 's.application_id')
                    ->select(
                        'c.name as country_name',
                        DB::raw('COUNT(a.id) as total_companies'),
                        DB::raw('SUM(CAST(a.interested_sqm AS UNSIGNED)) as total_sqm')
                    )
                    ->where('a.submission_status', 'submitted')
                    ->whereNull('s.application_id')
                    ->groupBy('c.id')
                    ->having('total_sqm', '>', 0)
                    ->orderByDesc('total_companies')
                    ->get();
            } catch (\Exception $e) {
                // Log the error for debugging
                Log::error('Error fetching applications by country: ' . $e->getMessage());
                // Optionally, set a fallback value or handle as needed
                $applicationsByCountry = collect();
                // Optionally, show a user-friendly error message
                // return back()->with('error', 'Could not fetch applications by country.');
            }

            // Count total unique countries with submitted applications (excluding sponsorships)
            try {
                $totalCountries = DB::table('applications as a')
                    ->leftJoin('sponsorships as s', 'a.id', '=', 's.application_id') // Ensure exclusion
                    ->where('a.submission_status', 'submitted')
                    ->whereNull('s.application_id') // Exclude applications in sponsorships
                    ->distinct()
                    ->count('a.billing_country_id');
            } catch (\Exception $e) {
                // Log the error for debugging
                Log::error('Error counting total countries: ' . $e->getMessage());
                // Set a fallback value
                $totalCountries = 0;
                // Optionally, show a user-friendly error message
                // return back()->with('error', 'Could not count total countries.');
            }

            // Get India vs. International count and total sqm (excluding sponsorships)
            try {
                $indiaInternationalStats = DB::table('applications as a')
                    ->join('countries as c', 'a.billing_country_id', '=', 'c.id') // Use billing_country_id
                    ->leftJoin('sponsorships as s', 'a.id', '=', 's.application_id') // Exclude sponsored applications
                    ->selectRaw("
            COUNT(DISTINCT CASE WHEN c.name = 'India' THEN a.id END) AS india_count,
            SUM(CASE WHEN c.name = 'India' THEN CAST(a.interested_sqm AS UNSIGNED) ELSE 0 END) AS india_sqm,
            COUNT(DISTINCT CASE WHEN c.name != 'India' THEN a.id END) AS international_count,
            SUM(CASE WHEN c.name != 'India' THEN CAST(a.interested_sqm AS UNSIGNED) ELSE 0 END) AS international_sqm
        ")
                    ->where('a.submission_status', 'submitted')
                    ->whereNull('s.application_id') // Exclude applications in sponsorships
                    ->whereRaw("CAST(a.interested_sqm AS UNSIGNED) > 0") // Exclude zero sqm values
                    ->first();
            } catch (\Exception $e) {
                // Log the error for debugging
                Log::error('Error fetching India/International stats: ' . $e->getMessage());
                // Set a fallback value
                $indiaInternationalStats = (object)[
                    'india_count' => 0,
                    'india_sqm' => 0,
                    'international_count' => 0,
                    'international_sqm' => 0,
                ];
                // Optionally, show a user-friendly error message
                // return back()->with('error', 'Could not fetch India/International stats.');
            }

            try {
                $approvedApplicationsByCountry = DB::table('applications as a')
                    ->join('countries as c', 'a.billing_country_id', '=', 'c.id') // Use billing_country_id
                    ->leftJoin('sponsorships as s', 'a.id', '=', 's.application_id') // Exclude applications in sponsorships
                    ->select(
                        'c.name as country_name',
                        DB::raw('COUNT(a.id) as total_companies'),
                        DB::raw('SUM(CAST(a.allocated_sqm AS UNSIGNED)) as total_sqm')
                    )
                    ->where('a.submission_status', 'approved') // Only approved applications
                    ->whereNull('s.application_id') // Exclude applications in sponsorships
                    ->groupBy('c.id')
                    ->having('total_sqm', '>', 0)
                    ->orderByDesc('total_companies')
                    ->get();
            } catch (\Exception $e) {
                // Log the error for debugging
                Log::error('Error fetching approved applications by country: ' . $e->getMessage());
                // Set a fallback value
                $approvedApplicationsByCountry = collect();
                // Optionally, show a user-friendly error message
                // return back()->with('error', 'Could not fetch approved applications by country.');
            }

            // Count total unique countries with approved applications (excluding sponsorships)
            try {
                $totalApprovedCountries = DB::table('applications as a')
                    ->leftJoin('sponsorships as s', 'a.id', '=', 's.application_id') // Ensure exclusion
                    ->where('a.submission_status', 'approved') // Only approved applications
                    ->whereNull('s.application_id') // Exclude applications in sponsorships
                    ->distinct()
                    ->count('a.billing_country_id');

                // If the result is empty or null, set to 0
                if (empty($totalApprovedCountries)) {
                    $totalApprovedCountries = 0;
                }
            } catch (\Exception $e) {
                // Log the error for debugging
                Log::error('Error counting total approved countries: ' . $e->getMessage());
                // Set a fallback value
                $totalApprovedCountries = 0;
                // Optionally, show a user-friendly error message
                // return back()->with('error', 'Could not count total approved countries.');
            }

            // Get India vs. International count and total sqm (excluding sponsorships)
            try {
                $approvedIndiaInternationalStats = DB::table('applications as a')
                    ->join('countries as c', 'a.billing_country_id', '=', 'c.id') // Use billing_country_id
                    ->leftJoin('sponsorships as s', 'a.id', '=', 's.application_id') // Exclude sponsored applications
                    ->selectRaw("
            COUNT(DISTINCT CASE WHEN c.name = 'India' THEN a.id END) AS india_count,
            SUM(CASE WHEN c.name = 'India' THEN CAST(a.allocated_sqm AS UNSIGNED) ELSE 0 END) AS india_sqm,
            COUNT(DISTINCT CASE WHEN c.name != 'India' THEN a.id END) AS international_count,
            SUM(CASE WHEN c.name != 'India' THEN CAST(a.allocated_sqm AS UNSIGNED) ELSE 0 END) AS international_sqm
        ")
                    ->where('a.submission_status', 'approved') // Only approved applications
                    ->whereNull('s.application_id') // Exclude applications in sponsorships
                    ->whereRaw("CAST(a.allocated_sqm AS UNSIGNED) > 0") // Exclude zero sqm values
                    ->first();
            } catch (\Exception $e) {
                // Log the error for debugging
                Log::error('Error fetching approved India/International stats: ' . $e->getMessage());
                // Set a fallback value
                $approvedIndiaInternationalStats = (object)[
                    'india_count' => 0,
                    'india_sqm' => 0,
                    'international_count' => 0,
                    'international_sqm' => 0,
                ];
                // Optionally, show a user-friendly error message
                // return back()->with('error', 'Could not fetch approved India/International stats.');
            }

            return view('dashboard.admin_new', compact('analytics', 'applicationsByCountry', 'totalCountries', 'indiaInternationalStats', 'approvedApplicationsByCountry', 'totalApprovedCountries', 'approvedIndiaInternationalStats'));
        }








        return view('exhibitor.dashboard');
    }

    //applicant details
    public function applicantDetails()
    {
        $this->__construct();

        return view('admin.application-view');
    }

    //invoice details for admin from Invoice model
    public function invoiceDetails()
    {
        $this->__construct();
        $slug = 'Invoices';
        $invoices = Invoice::with(['application', 'payments', 'billingDetails'])->get();


        return view('dashboard.invoice-list', compact('invoices', 'slug'));
    }
}
