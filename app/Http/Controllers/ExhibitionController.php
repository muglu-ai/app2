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

        Log::info('Application: ' . $application);

        $stallSize = (int) $application->interested_sqm;

        $count = $this->calculateAllocationBadgeCount($stallSize);

        $badgeCounts = $count['badges'] ?? [];

        // Calculate counts based on badge allocations
        $stallManningCount = isset($count['Exhibitor']) ? (int)$count['Exhibitor'] : 0;
        $complimentaryDelegateCount = 0;
        foreach ($count as $type => $value) {
            if ($type !== 'Exhibitor') {
                $complimentaryDelegateCount += (int)$value;
            }
        }
        $stallManningCount = $count['stallManningCount'];
        $complimentaryDelegateCount = $count['complimentaryDelegateCount'];

        //add new entries in stall manning and complimentary delegate tables
        $exhibitionParticipant = ExhibitionParticipant::updateOrCreate(
            ['application_id' => $application->id],
            [
                'stall_manning_count' => $stallManningCount,
                'complimentary_delegate_count' => $complimentaryDelegateCount
            ]
        );

        // Step 2: Loop through badge types and update/create entries
        foreach ($badgeCounts as $ticketType => $badgeCount) {
            // Find ticket category ID dynamically
            $ticketCategory = TicketCategory::where('ticket_type', $ticketType)->first();

            if ($ticketCategory) {
                ExhibitionParticipantPass::updateOrCreate(
                    [
                        'participant_id' => $exhibitionParticipant->id,
                        'ticket_category_id' => $ticketCategory->id
                    ],
                    [
                        'badge_count' => $badgeCount
                    ]
                );
            }
        }

        return response()->json(['stall_manning_count' => $stallManningCount, 'complimentary_delegate_count' => $complimentaryDelegateCount]);
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
        return $result;
    }
}
