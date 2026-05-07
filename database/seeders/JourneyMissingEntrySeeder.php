<?php

namespace Database\Seeders;

use App\Http\Controllers\Admins\ShipmentChargesController;
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
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admins\AdminFinanceController;

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
            22315862659900,
            20220262648357,
            20220262575669,
            22322362626304,
            14415862637984,
            22314462623693,
            14415862631346,
            14414462631449,
            20220262580878,
            14414462641619,
            22315862533433,
            20220262559714,
            27117462610308,
            20220262617161,
            20226762501834,
            20222362609036,
            20228862577819,
            20228862577894,
            20217462567638,
            20217462567400,
            20215862568076,
            20227162567884,
            20217462568155,
            20222362566839,
            20215862567025,
            20222362566912,
            20215862336466,
            20217462588317,
            20217462536780,
            20228862584297,
            20217462576798,
            20222362576791,
            20217462566100,
            20220262591310,
            20220262518014,
            20220262596644,
            22317462594654,
            22327162576782,
            22315862555123,
            20215862582263,
            20220262586935,
            14415862592878,
            14414462592829,
            20215862578230,
            14415862589872,
            15815862578043,
            20228862551253,
            20234062579121,
            22325162576328,
            14425162580455,
            25128862534508,
            20220262573454,
            28420262583329,
            20228862560472,
            20214462541112,
            20222362538576,
            20245562539036,
            20217462538549,
            20228062568096,
            20215862491778,
            20220262335864,
            20233662536387,
            20222362559385,
            20214462541724,
            22328862541477,
            14422362559237,
            22317262538878,
            22320262538505,
            14422362559266,
            14410662569254,
            22322362324056,
            25128862555876,
            20222362501542,
            20215862529797,
            14420262542938,
            20220262568109,
            28428862555699,
            23720262537787,
            22328862534021,
            20215862523436,
            22350362517398,
            22320262515987,
            20217462518999,
            20214462524373,
            22328862507456,
            14415862522908,
            22316162479845,
            14420262520987,
            14425162515224,
            14422362528592,
            14428862509095,
            15820262508048,
            17420262508368,
            20217262460964,
            20220262455696,
            20215862411808,
            22320262499068,
            17415862417400,
            22317462479420,
            17422362424106,
            22320262483376,
            22320262452718,
            14420262473592,
            22328362489670,
            20222362498642,
            14420262473742,
            15820262499999,
            25120262483784,
            25128362465586,
            28417462456545,
            22320262451617,
            22317462422064,
            14420262442315,
            20220262389835,
            25120262430813,
            25117462430772,
            29320262429940,
            17422362414716,
            30222362397578,
            23720262392275,
            22317462378776,
            22320262371001,
            11020262376071,
            20222162242652,
            14428862273037,
            20222362078424,
            20225562172742,
            25120262117904,
            20225762086301
                ];
        if ($shipmentId) {
            $shipmentId = Shipment::whereIn('tracking_number', $shipmentId)->where('shipper_status_id',65)->get();
            echo count($shipmentId);
        
            foreach ($shipmentId as $shipment) {
                $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 12)->latest()->select('status_reason_id', 'remarks')->first();
                $shipment_status_reason = $journey->status_reason_id;
                $remarks =  ((! $journey->remarks) ? $journey->remarks : '');
                Shipment::where('id', $shipment->id)->update(['shipper_status_id' => 20, 'consignee_status_id' => 20]);
                NotificationsController::send(15, 0, $shipment->id);
                NotificationsController::send(16, 0, $shipment->id);

                if ($shipment->shipment_type == 1) {
                    if ($shipment->booking_type_id != 4) {
                        ShipmentChargesController::return($shipment->id);
                        if ($shipment->packaging_material_request != 1) {
                            AdminFinanceController::add_payment($shipment->id, 1);
                        }
                    } else {
                        ShipmentChargesController::walk_in_return($shipment->id);
                        $shipment->walk_in_status = 2;
                        $shipment->save();
                        AdminFinanceController::done_payment($shipment->id, 1);
                    }
                }

                ShipmentsJourneyController::add($shipment->id, 20, 20, $shipment_status_reason, $remarks, null, 346, null, null, 1, null, null, null, null, null);


                // $shipment->shipper_status_id = 13;
                //     $shipment->consignee_status_id = 13;
                //     $shipment->save();
                // ShipmentsJourneyController::add($shipment->id, 13, 13, NULL, NULL, NULL, 346);
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
