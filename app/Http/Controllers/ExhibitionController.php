<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use App\Models\ExhibitionParticipant;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\ExhibitionParticipantPass;
use App\Models\TicketCategory;

class ExhibitionController extends Controller
{
    //
    public function handlePaymentSuccess($applicationId)
    {
        $application = Application::where(function ($query) use ($applicationId) {
            $query->where('application_id', $applicationId)
                ->orWhere('id', $applicationId);
        })->first();

        if (!$application) {
            return response()->json(['error' => 'Application not found'], 404);
        }

        $stallSize = (int) $application->interested_sqm;

        $count = $this->calculateAllocationBadgeCount($stallSize);

        //Log::info("Calculated badge count:", $count);

        $badgeCounts = collect($count)->mapWithKeys(function ($badgeCount, $ticketType) {
            return [$ticketType => (int) $badgeCount];
        });
        //Log::info("Badge counts after mapping:", $badgeCounts->toArray());

        // dd($badgeCounts);

        // Calculate counts based on badge allocations
        $stallManningCount = (int) ($badgeCounts['Exhibitor'] ?? 0);

        //dd($stallManningCount);

        $complimentaryDelegateCount = collect($badgeCounts)
            ->except(['Exhibitor'])
            ->sum();

        // Save or update participant data
        $exhibitionParticipant = ExhibitionParticipant::updateOrCreate(
            ['application_id' => $application->id],
            [
                'stall_manning_count' => $stallManningCount,
                'complimentary_delegate_count' => $complimentaryDelegateCount
            ]
        );

        // dd([
        //     'ExhibitionParticipant created/updated',
        //     'id' => $exhibitionParticipant->id,
        //     'stall_manning_count' => $stallManningCount,
        //     'complimentary_delegate_count' => $complimentaryDelegateCount
        // ]);

        // Process badge pass counts
        foreach ($badgeCounts as $ticketType => $badgeCount) {

            if ($badgeCount <= 0) continue;

            $ticketCategory = TicketCategory::where('ticket_type', $ticketType)->first();

            if (!$ticketCategory) {
                Log::warning("TicketCategory not found for ticket_type: $ticketType");
                continue;
            }

            //dd("Creating/Updating ExhibitionParticipantPass for participant_id: {$exhibitionParticipant->id}, ticket_category_id: {$ticketCategory->id}, badge_count: $badgeCount");


            ExhibitionParticipantPass::updateOrCreate(
                [
                    'participant_id' => $exhibitionParticipant->id,
                    'ticket_category_id' => $ticketCategory->id
                ],
                [
                    'badge_count' => $badgeCount
                ]
            );
            //dd("ExhibitionParticipantPass created/updated for participant_id: {$exhibitionParticipant->id}, ticket_category_id: {$ticketCategory->id}, badge_count: $badgeCount");
        }


        return response()->json([
            'stall_manning_count' => $stallManningCount,
            'complimentary_delegate_count' => $complimentaryDelegateCount
        ]);
    }

    public function calculateAllocationBadgeCount($stallSize)
    {
        // Define pass allocation based on stall size
        $allocation = DB::table('stall_pass_allocations')
            ->where('min_sqm', '<=', $stallSize)
            ->where('max_sqm', '>=', $stallSize)
            ->first();

        if (!$allocation) {
            return [];
        }

        // dd($allocation);

        // Find the correct pass count based on stall size
        $badgeAllocations = DB::table('stall_pass_badges')
            ->join('ticket_categories', 'ticket_categories.id', '=', 'stall_pass_badges.ticket_category_id')
            ->where('stall_pass_badges.allocation_id', $allocation->id)
            ->select('ticket_categories.ticket_type', 'stall_pass_badges.badge_count')
            ->get();




        $result = [];
        foreach ($badgeAllocations as $row) {
            $result[$row->ticket_type] = $row->badge_count;
        }

        // dd($result);
        return $result;
    }
}
