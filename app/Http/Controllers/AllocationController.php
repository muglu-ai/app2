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
    public function showAllAllocations()
    {
        $slug = "Complimentary Ticket Allocations";
        $applications = Application::with(['exhibitionParticipant.exhibitionParticipantPasses.ticketCategory'])
            ->whereHas('exhibitionParticipant')
            ->get()
            ->map(function ($application) {
                $participant = $application->exhibitionParticipant;
                $badge_allocations = [];

                if ($participant) {
                    foreach ($participant->exhibitionParticipantPasses as $pass) {
                        $badge_allocations[] = [
                            'ticket_type' => $pass->ticketCategory->ticket_type ?? 'Unknown',
                            'badge_count' => $pass->badge_count,
                        ];
                    }
                }

                if (empty($badge_allocations)) {
                    return null; // Exclude applications with no badge allocations
                }

                return (object)[
                    'id' => $application->id,
                    'company_name' => $application->company_name,
                    'badge_allocations' => $badge_allocations,
                ];
            })
            ->filter() // Remove null values
            ->values(); // Reindex the array

        $badgeAllocations = $applications;
        //merge ticket_type and badge_count into a badgeAllocations array
        // $badgeAllocations = $applications->pluck('exhibitionParticipant.exhibitionParticipantPasses')->flatten(1)


        // dd($applications);

        return view('exhibitor.allocations', compact('applications', 'slug', 'badgeAllocations'));
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
