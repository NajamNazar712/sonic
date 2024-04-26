<?php

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\Shipment;
use App\Http\Models\Zone;
use App\Http\Models\ZoneCitiesGst;
use Illuminate\Database\Seeder;

class JourneyMissingEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $shipmentId = [34691375,34690896,34689305,34677630,34571165,34690896,34690896,34691375,34689305];
        
        foreach($shipmentId as $Shipment){
            $shipment = Shipment::find($Shipment);
            if($shipment->shipper_status_id === 14)
            {
                $charges = $shipment->weight_charges + $shipment->fuel_surcharge;
                $deliveryNoteId = DeliveryNoteShipment::where('shipment_id',$Shipment)->first();
                $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                $zone = Zone::find($shipment->pickup_address->city->zone_id);
                $gst = ROUND($charges * $zone->gst, 2, PHP_ROUND_HALF_DOWN);
                $payable = $shipment->amount - $gst;
                if ($pending_payment->exists()) {
                    $pending_payment = $pending_payment->first();
    
                    $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
                    $pending_payment->delivered_shipments = $pending_payment->delivered_shipments + 1;
    
                    $pending_payment->save();
                } else {
                    $pending_payment = new PendingPayment();
    
                    $pending_payment->user_id = $shipment->user_id;
                    $pending_payment->total_shipments = 1;
                    $pending_payment->delivered_shipments = 1;
                    $pending_payment->returned_shipments = 0;
                    $pending_payment->adjusted_shipments = 0;
    
                    $pending_payment->save();
                }
                $pending_payment_shipment = new PendingPaymentShipment();

                $pending_payment_shipment->pending_payment_id = $pending_payment->id;
                $pending_payment_shipment->shipment_id = $Shipment;
                $pending_payment_shipment->type = 0;
                $pending_payment_shipment->amount = $shipment->amount;
                $pending_payment_shipment->charges = $charges;
                $pending_payment_shipment->gst = $zone->gst;
                $pending_payment_shipment->payable = $payable;

                $pending_payment_shipment->save();
                // $deliveryNoteId->status = 6;
                // $deliveryNoteId->save();
                ShipmentsJourneyController::add($shipment->shipment_id, $shipment->shipper_status_id, $shipment->shipper_status_id, NULL, NULL, $shipment->user_id, NULL, $deliveryNoteId->delivery_note_id);

            }
        }
       
    }
}
