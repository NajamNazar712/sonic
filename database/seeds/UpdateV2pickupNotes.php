<?php

use Illuminate\Database\Seeder;
use App\Http\Models\V2Pickup\V2PickupNote;
use App\Http\Models\V2Pickup\V2PickupNoteRequest;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
use App\Http\Models\ShipmentsJourney;

class UpdateV2pickupNotes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pickup_notes = V2PickupNote::get();
        if($pickup_notes){
            foreach ($pickup_notes as $pickup_note){
                $total_shipments = 0;
                $total_arrived_shipments = 0;
                $total_scanned_by_rider_shipments = 0;

                $pickup_request_ids = V2PickupNoteRequest::where('pickup_note_id', $pickup_note->id)->pluck('pickup_request_id')->toArray();
                if(count($pickup_request_ids) > 0){
                    $pickup_requests = V2PickupRequest::whereIn('id', $pickup_request_ids);
                    if($pickup_requests->exists()){
                        $pickup_requests = $pickup_requests->get();
                        foreach ($pickup_requests as $pickup_request){
                            $total_shipments = $total_shipments + $pickup_request->booked;
                            $total_arrived_shipments = $total_arrived_shipments + $pickup_request->received;
                        }
                    }

                    $pickup_request_shipment_ids = V2PickupRequestShipment::whereIn('pickup_request_id', $pickup_request_ids)->pluck('shipment_id')->toArray();

                    if(count($pickup_request_shipment_ids) > 0){
                        $pickup_request_chunk_shipment_ids = array_chunk($pickup_request_shipment_ids, 100);
                        foreach ($pickup_request_chunk_shipment_ids as $chunk_shipment_ids){
                            $shipments_journey = ShipmentsJourney::whereIn('shipment_id', $chunk_shipment_ids)->where('shipper_status_id', 53)->get()->groupBy('shipment_id');

                            if($shipments_journey){
                                foreach ($shipments_journey as $journey){
                                    $total_scanned_by_rider_shipments++;
                                }
                            }
                        }
                    }

                    $pickup_note->shipments = $total_shipments;
                    $pickup_note->arrived_shipments = $total_arrived_shipments;
                    $pickup_note->shipments_scanned_by_rider = $total_scanned_by_rider_shipments;
                    $pickup_note->save();
                }
            }
        }
    }
}
