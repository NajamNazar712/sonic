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
            20214462296157,
            20214462329533,
            20214462296340,
            20222362342717,
            22314462396643,
            14422362406780,
            22322362423355,
            22314462418423,
            22314462420269,
            14414462428733,
            27114462244526,
            23714462296659,
            22314462423452,
            20214462296188,
            14414462438548,
            20214462312725,
            20222362329226,
            20214462328725,
            20214462329667,
            20214462362574,
            22320262264443,
            28314462290088,
            20217462276739,
            14414462406895,
            23714462295505,
            22314462345921,
            20220262371385,
            20220262340470,
            20220262217552,
            25120262002292,
            18814462393905,
            20214462250400,
            22314462439357,
            20214462249122,
            14422362279935,
            14423362279080,
            20211962349592,
            14445962314767,
            22320262277847,
            14411962169241,
            20217462235356,
            22317462323636,
            20212862146170,
            14425162399155,
            20213462115563,
            20226462086299,
            20214462186469,
            25117462245823,
            20225162370769,
            14427162314075,
            20225162078375,
            25128361868987,
            27125162214420,
            20216962086314,
            20243862174434,
            20211061788212,
            30220262185148,
            14428862225276,
            14418662334485,
            14425162377860,
            22328862422077,
            20218662273484,
            14420262334484,
            14420262304632,
            22328862253298,
            14428862280758,
            17428862423759,
            20225562172742,
            20225162096323,
            22317462243580,
            20225162078378,
            17420262248594,
            23728862392302,
            14420262334596,
            14420262335560,
            20228862087706,
            22315862246371,
            20220262397817,
            14428862279030,
            14417462279097,
            14417462280904,
            25115862408979,
            20228861962919,
            14415862367195,
            20213562074847,
            30220262335049,
            202355862148323,
            14429362438734,
            20229362233577,
            22320262340274,
            20220261604878,
            14417462312670,
            14417462280878,
            30228862368730,
            20214462161182,
            17415862419669,
            20218662278205,
            20215862410540,
            22315862418647,
            14415862229806,
            14415862402661,
            14416162398172,
            22328862441118,
            20217462213319,
            27118362260436,
            20217461939263,
            20213862210663,
            20215862251135,
            17428862422613,
            20217462350999,
            20220262416512,
            17420262256784,
            14434062318375,
            22327162386176,
            20215862294288,
            22327162420253,
            14427162308920,
            22327162343266,
            22315862407775,
            20227162322337,
            20214462351587,
            20228862297400,
            22317462387247,
            22317462421234,
            17425162424885,
            14425162444112,
            20225162329197,
            20227162298624,
            23720262328094,
            23720262295534,
            23720262249834,
            23720262249827,
            25128862290574,
            22328862368611,
            22325162421804,
            20215862370166,
            22311061831868,
            20214462187868,
            30215862238858,
            30215862224862,
            23715862244275,
            30220262330840,
            23720262328088,
            20215862143043,
            23715862215218,
            20215862208488,
            14415862379366,
            14417462305574,
            20215862295234,
            20215862284637,
            20215862393332,
            20220262168320,
            22328862243530,
            20220262421737,
            14420262378779,
            14434062278313,
            20217462270386,
            20217462225398,
            22325162421180,
            22328862399520,
            20217462208082,
            30228862338654,
            20222362144260,
            20222362217831,
            28822362305633,
            20222362368837,
            17422362371438,
            20222362410841,
            20217262168597,
            20222362270331,
            20222362294275
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
