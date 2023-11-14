<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BoltUndeliveredReasonMap;
use App\Http\Models\ShipmentsJourney;
use App\BoltUndeliveredReasonMapCount;
use App\Http\Controllers\ShipmentsJourneyController;

class UndeliveredReasonController extends Controller
{
    static public function add($shipment_id, $delivery_note_id, $reason_id)
    {
        $status = BoltUndeliveredReasonMap::where('reason_id', $reason_id)->first();
        $shipment = BoltUndeliveredReasonMapCount::where('shipment_id', $shipment_id);
        $journey_status = null;
        $remarks = null;
        
        if ($shipment->exists()) {
            $shipment = $shipment->latest()->first();
            $count = $shipment->count;
        } else {
            $count = 0;
        }
        
        $delivery_note = BoltUndeliveredReasonMapCount::where('delivery_note_id', $delivery_note_id);

        if (!$delivery_note->exists()) {
            $count++;
        }

        $journey_status = ($count <= 1) ? $status->status_attempt_count_1 : $status->status_attempt_count_2;
        $remarks = in_array($status->reason_id, [17, 18, 27, 35]) ? $status->remarks : '-';
        
        if (!in_array($reason_id, [14, 23, 25])) { 
            BoltUndeliveredReasonMapCount::updateOrCreate(
                [
                    'shipment_id' => $shipment_id,
                ],
                [
                    'delivery_note_id' => $delivery_note_id,
                    'reason_id' => $reason_id,
                    'count' => $count,
                ]
            );
        }
        return $journey_status;
    }
}