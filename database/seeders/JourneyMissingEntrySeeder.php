<?php

namespace Database\Seeders;

use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\Webhook\ShipmentStatusWebhookController;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\ConsigneeUser;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipperShipmentsSubscription;
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
        //Only use for the marked as delivered....
        $shipmentId = [
            20222357305047,
            22322358205523,
            20217258124407,
            20220258234882,
            20217258194207,
            22322358196098,
            20222357972810,
            20222358109927,
            22317458137738,
            20217458122177,
            20222358099505,
            20220258116476,
            20220258218808,
            22311058204160,
            20220258072020,
            20220258114680,
            20228358073195,
            22322358206701,
            20227158109651,
            22322358200748,
            22322358212905,
            20222358159097,
            20231558018438,
            20222358111788,
            20225158135313,
            20222358134858,
            22328858192949,
            20228858110604,
            20220258234653,
            22328858194079,
            22315858213354,
            22317458122276,
            20217458118510,
            22322358158708,
            20222358118375,
            22322358175660,
            20222357972795,
            22322358156115,
            20220257649968,
            20220257719296,
            20220258115280,
            20228857827986,
            22328857989704,
            20220258124264,
            22325158109282,
            22317458071053,
            22314458179467,
            20228357984273,
            22322358090586,
            20222358098993,
            22325158117033,
            22327158071090,
            20225157982564,
            22317458068027,
            20217458061565,
            22322358169769,
            22322357927731,
            22322358205331,
            20215858061425,
            22322358216196,
            22330258193432,
            20217457806245,
            20214458156523,
            22331557918011,
            20228457786384,
            22315958036882,
            20222358248538,
            20222358223110,
            20222358278990,
            20222358241397,
            20220258071863,
            22315858303872,
            20220258285634,
            22317458255322,
            20220258085954,
            22325158272961,
            20225158277186,
            20225158215008,
            22325158176669,
            22315858206731,
            20222358153461,
            22314458101415,
            20222358069944,
            22314458087867,
            22325158251564,
            20220258234742,
            20225158207293,
            20228858215928,
            22325158206736,
            22311058206705,
            22325158135311,
            22328458179928,
            20222358219748,
            22325158239862,
            22328858212199,
            22328858170686,
            22328858274553,
            22325158227242,
            22318658203393,
            22328858262449,
            22327157761109,
            22331558272990,
            20217258260128,
            20217258206428,
            20217258206804,
            22322358215088,
            20217458227653,
            22320258144467,
            22320258212952,
            22325158285936,
            22325158304737,
            22325158325120,
            22325158288215,
            20225158241430,
            20227158181978,
            22315857745445,
            22325158314856,
            22314458355535,
            22325158317119,
            22322357817865,
            20222358206808,
            22322358207359,
            22328858210686,
            22314458314580,
            20220258285745,
            20222357840608,
            22328458219566,
            22328858252389,
            22328858206403,
            20220258285763,
            22317458246227,
            22327158177069
        ];
        if ($shipmentId) {
            $shipmentId = Shipment::whereIn('tracking_number', $shipmentId)->where('shipper_status_id',13)->get();
            echo count($shipmentId);
        
            foreach ($shipmentId as $shipment) {
                $shipment->shipper_status_id = 13;
                    $shipment->consignee_status_id = 13;
                    $shipment->save();
                ShipmentsJourneyController::add($shipment->id, 13, 13, NULL, NULL, NULL, 346);
                // return  $shipment;
                // if ($shipment->shipper_status_id === 5) {
                //     // $shipment->created_at = $shipment->updated_at;
                //     $shipment->shipper_status_id = 14;
                //     $shipment->consignee_status_id = 14;
                //     $shipment->save();
                // }
                // $deliveryNoteId = DeliveryNoteShipment::where('shipment_id', $shipment->id)->latest()->first();

                // if (in_array($shipment->shipper_status_id, [13, 14])) {
                //     $charges = $shipment->weight_charges + $shipment->fuel_surcharge;
                //     $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                //     $zone = Zone::find($shipment->pickup_address->city->zone_id);
                //     if ($zone->gst == '0.16') {
                //         $addgst = 16.0;
                //     } elseif ($zone->gst == '0.13') {
                //         $addgst = 13.0;
                //     } else {
                //         $addgst = $zone->gst;
                //     }

                //     $gst = ROUND($charges * $zone->gst, 2, PHP_ROUND_HALF_DOWN);
                //     $payable = $shipment->amount - $gst;
                //     if ($pending_payment->exists()) {
                //         $pending_payment = $pending_payment->first();

                //         $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
                //         $pending_payment->delivered_shipments = $pending_payment->delivered_shipments + 1;

                //         $pending_payment->save();
                //     } else {
                //         $pending_payment = new PendingPayment();

                //         $pending_payment->user_id = $shipment->user_id;
                //         $pending_payment->total_shipments = 1;
                //         $pending_payment->delivered_shipments = 1;
                //         $pending_payment->returned_shipments = 0;
                //         $pending_payment->adjusted_shipments = 0;

                //         $pending_payment->save();
                //     }
                //     $pending_payment_shipment = new PendingPaymentShipment();
                //     $pending_payment_shipment->pending_payment_id = $pending_payment->id;
                //     $pending_payment_shipment->created_at = $deliveryNoteId->updated_at;
                //     $pending_payment_shipment->updated_at = $deliveryNoteId->updated_at;
                //     $pending_payment_shipment->shipment_id = $shipment->id;
                //     $pending_payment_shipment->type = 0;
                //     $pending_payment_shipment->amount = $shipment->amount;
                //     $pending_payment_shipment->charges = $charges;
                //     $pending_payment_shipment->gst = $addgst;
                //     $pending_payment_shipment->payable = $payable;
                //     $pending_payment_shipment->save();

                //     // $deliveryNoteId->status = 6;
                //     // $deliveryNoteId->save();
                //     ShipmentsJourneyController::add($request->shipment_id, 13, 13, NULL, $remarks, NULL, 346);
                //     // ShipmentsJourneyController::add($shipment->id, $shipment->shipper_status_id, $shipment->shipper_status_id, NULL, NULL, $shipment->user_id, NULL, $deliveryNoteId->delivery_note_id);

                // }
                // $shipmentstatus = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 14)->first();
                // if (!$shipmentstatus) {

                
                // $verification = 1;
                // $shipment_journey = new ShipmentsJourney();
                // $status_id = (!in_array($shipment->shipper_status_id, [14]) ? '14' : $shipment->shipper_status_id);
                // $shipment_journey->shipment_id = $shipment->id;
                // $shipment_journey->verification = $verification;
                // $shipment_journey->created_at = $deliveryNoteId->updated_at ?? $shipment->updated_at;
                // $shipment_journey->updated_at = $deliveryNoteId->updated_at ?? $shipment->updated_at;
                // $shipment_journey->shipper_status_id = $status_id;
                // $shipment_journey->consignee_status_id = $status_id;
                // $shipment_journey->status_reason_id = null;
                // $shipment_journey->city_id =  202;
                // $shipment_journey->remarks =  null;
                // $shipment_journey->user_id = null;
                // $shipment_journey->admin_id = 346;
                // $shipment_journey->rider_id = null;
                // $shipment_journey->reference_1_id = null;
                // $shipment_journey->reference_2_id = null;
                // $shipment_journey->received_or_refused_by = null;
                // $shipment_journey->relation = null;
                // $shipment_journey->cnic = null;
                // $shipment_journey->save();

                // if ($shipment->shipper_status_id != 1) {
                //     ShipmentStatusWebhookController::webhook_subscription($shipment->id, $shipment->shipper_status_id, null);
                // }
                // if ($verification == 1) {
                //     $shipment_subscription = ShipperShipmentsSubscription::where('shipment_id', $shipment->id);
                //     if ($shipment_subscription->exists()) {
                //         $shipment_subscription = $shipment_subscription->first();
                //         NotificationsController::app_notification(7, $shipment_subscription->shipper_id, 3, $shipment->id, $shipment->shipper_status_id);
                //     }
                //     $consignee_user = ConsigneeUser::where('phone_number_1', $shipment->consignee_phone_number_1)
                //         ->orwhere('phone_number_2', $shipment->consignee_phone_number_1);
                //     if ($consignee_user->exists()) {
                //         $consignee_user = $consignee_user->first();
                //         $consignee_id = $consignee_user->id;
                //         NotificationsController::app_notification(8, $consignee_id, 4, $shipment->id, $shipment->shipper_status_id);
                //     }
                //     ShipperShipmentsSubscription::where('shipment_id', $shipment->id)->delete();
                // }
                // }
            }
        }
    }
}
