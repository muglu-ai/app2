<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\ExhibitionParticipant;
use App\Models\ExhibitionParticipantPass;
use App\Models\TicketCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class AllocationController extends Controller
{
    //
    public function showAllAllocations(Request $request)
    {
        $slug = "Complimentary Ticket Allocations";

        $search = $request->input('search');

        $ticketCategories = TicketCategory::all();
        $ticketTypes = $ticketCategories->pluck('ticket_type', 'id'); // [id => 'VIP']

        $query = Application::with(['exhibitionParticipant.exhibitionParticipantPasses.ticketCategory'])
            ->whereHas('exhibitionParticipant');

        if ($search) {
            $query->where('company_name', 'LIKE', "%{$search}%");
        }

        $applications = $query->paginate(10)->through(function ($application) use ($ticketTypes) {
            $participant = $application->exhibitionParticipant;
            $badgeCounts = [];
            $usedCounts = [];

            foreach ($ticketTypes as $catId => $name) {
                $badgeCounts[$catId] = 0;
                $usedCounts[$catId] = 0;
            }

            if ($participant) {
                foreach ($participant->exhibitionParticipantPasses as $pass) {
                    $categoryId = $pass->ticket_category_id;

                    if (isset($badgeCounts[$categoryId])) {
                        $badgeCounts[$categoryId] += $pass->badge_count;

                        $usedCounts[$categoryId] = DB::table('complimentary_delegates')
                            ->where('exhibition_participant_id', $participant->id)
                            ->where('ticket_category_id', $categoryId)
                            ->count();
                    }
                }
            }

            return (object)[
                'id' => $application->id,
                'company_name' => $application->company_name,
                'exhibition_participant_id' => $participant?->id,
                'badges' => $badgeCounts,
                'used' => $usedCounts,
            ];
        });

        return view('exhibitor.allocations', compact('applications', 'slug', 'ticketTypes', 'search'));
    }


    public function updateAllocations(Request $request, $applicationId)
    {
        $badgeAllocations = $request->input('badge_allocations', []);

        $application = Application::with('exhibitionParticipant')->findOrFail($applicationId);
        $participant = $application->exhibitionParticipant;

        if (!$participant) {
            return back()->with('error', 'Exhibition participant not found.');
        }

        foreach ($badgeAllocations as $ticketCategoryId => $count) {
            $pass = ExhibitionParticipantPass::where('exhibition_participant_id', $participant->id)
                ->where('ticket_category_id', $ticketCategoryId)
                ->first();

            if ($pass) {
                $pass->badge_count = $count;
                $pass->save();
            } else {
                // Optionally create a new one
                ExhibitionParticipantPass::create([
                    'exhibition_participant_id' => $participant->id,
                    'ticket_category_id' => $ticketCategoryId,
                    'badge_count' => $count,
                ]);
            }
        }

        return back()->with('success', 'Badge allocations updated successfully.');
    }


    public function readAllocations($applicationId)
    {
        $application = Application::with('exhibitionParticipant')->findOrFail($applicationId);
        $participant = $application->exhibitionParticipant;

        if (!$participant) {
            return back()->with('error', 'No participant found.');
        }

        // Group delegates by ticket_category_id
        $delegates = DB::table('complimentary_delegates')
            ->where('exhibition_participant_id', $participant->id)
            ->get()
            ->groupBy('ticket_category_id');

        // Get ticket type names
        $categories = TicketCategory::whereIn('id', $delegates->keys())->pluck('ticket_type', 'id');

        return view('exhibitor.read-allocations', compact('application', 'delegates', 'categories'));
    }







    /**
     * Add a new badge category (ExhibitionParticipantPass) for an application.
     */
    public function addBadgeCategory(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'badge_count' => 'required|integer|min:1',
        ]);

        $application = Application::findOrFail($request->application_id);
        $participant = $application->exhibitionParticipant;
        if (!$participant) {
            return back()->with('error', 'Exhibitor participant not found for this application.');
        }

        // Prevent duplicate
        $existing = $participant->exhibitionParticipantPasses()->where('ticket_category_id', $request->ticket_category_id)->first();
        if ($existing) {
            return back()->with('error', 'This badge category already exists for this application.');
        }

        $participant->exhibitionParticipantPasses()->create([
            'ticket_category_id' => $request->ticket_category_id,
            'badge_count' => $request->badge_count,
        ]);

        return back()->with('success', 'Badge category added successfully.');
    }
}
