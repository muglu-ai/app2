<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ExhibitionParticipant;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $applicationsByCountry = DB::table('applications as a')
                ->join('countries as c', 'a.billing_country_id', '=', 'c.id') // Use billing_country_id
                ->leftJoin('sponsorships as s', 'a.id', '=', 's.application_id') // Check if application exists in sponsorships
                ->select(
                    'c.name as country_name',
                    DB::raw('COUNT(a.id) as total_companies'),
                    DB::raw('SUM(CAST(a.interested_sqm AS UNSIGNED)) as total_sqm')
                )
                ->where('a.submission_status', 'submitted')
                ->whereNull('s.application_id') // Exclude applications present in sponsorships
                ->groupBy('c.id')
                ->having('total_sqm', '>', 0)
                ->orderByDesc('total_companies')
                ->get();

            // Count total unique countries with submitted applications (excluding sponsorships)
            $totalCountries = DB::table('applications as a')
                ->leftJoin('sponsorships as s', 'a.id', '=', 's.application_id') // Ensure exclusion
                ->where('a.submission_status', 'submitted')
                ->whereNull('s.application_id') // Exclude applications in sponsorships
                ->distinct()
                ->count('a.billing_country_id');

            // Get India vs. International count and total sqm (excluding sponsorships)
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

            // Count total unique countries with approved applications (excluding sponsorships)
            $totalApprovedCountries = DB::table('applications as a')
                ->leftJoin('sponsorships as s', 'a.id', '=', 's.application_id') // Ensure exclusion
                ->where('a.submission_status', 'approved') // Only approved applications
                ->whereNull('s.application_id') // Exclude applications in sponsorships
                ->distinct()
                ->count('a.billing_country_id');

            // Get India vs. International count and total sqm (excluding sponsorships)
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

            return view('dashboard.admin_new', compact('analytics', 'applicationsByCountry', 'totalCountries', 'indiaInternationalStats' , 'approvedApplicationsByCountry', 'totalApprovedCountries', 'approvedIndiaInternationalStats'));
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
