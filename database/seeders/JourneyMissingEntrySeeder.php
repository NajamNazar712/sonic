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
            22325162662273,
            22326762661313,
            20220262696118,
            20220262701416,
            22325162678359,
            14425162663521,
            17417462672741,
            23734062666909,
            21022362611344,
            22315862659962,
            22315862661041,
            28425162662022,
            20217262610746,
            20220262656716,
            20220262644638,
            20220262654601,
            20220262625129,
            20214462610615,
            20214462610630,
            20220262610572,
            20217262638930,
            20220262641705,
            20220262654197,
            14414462638311,
            20220262610371,
            20225162611832,
            22322362653188,
            22325162621804,
            14425162654917,
            14434062632696,
            22320262612694,
            14422362645063,
            14422362646097,
            14422362635924,
            14422362641345,
            14417462629489,
            14450762651730,
            14414462644397,
            20220262543426,
            14415862641627,
            22317462597867,
            22320262377370,
            14422362651888,
            14427162630859,
            22322362567143,
            14415862653348,
            20217262642679,
            14428862650071,
            20220262655475,
            22325162626074,
            22322362648185,
            25125162638481,
            14425162637520,
            14415862634164,
            20220262650722,
            17428862631294,
            14415862627337,
            20220262657834,
            22328862644228,
            22331162626526,
            26722362650308,
            14425162647830,
            20217262559712,
            20220262653385,
            22322362648047,
            14422362620577,
            17420262620767,
            14422362655010,
            22327162625045,
            25115862613930,
            27115862610397,
            25122362621290,
            30222362640462,
            25122362621527,
            25127162621366,
            25114462621552,
            25117462621313,
            14422362639734,
            12527162628651,
            23726762623130,
            23728862623124,
            23722362623574,
            20220262587888,
            28422362615842,
            20231162597478,
            20220262573070,
            20225162573154,
            20225162567872,
            20215862594601,
            20225162573067,
            20214462575678,
            20228862574801,
            20222362596764,
            20227162567257,
            20227162567018,
            20228862567612,
            20222362567540,
            20217462567929,
            20218662566570,
            20214462587944,
            20222362536489,
            20222362536637,
            20222362536629,
            20222362568069,
            20222362567243,
            20215862567448,
            20222362566469,
            20222362536313,
            20215862335698,
            20215862536806,
            20222362567397,
            20217462566204,
            20226762567021,
            20214462566686,
            20222362567715,
            20222362566743,
            20215862575262,
            20222362567393,
            20228862567788,
            20217462567464,
            20214462582264,
            20222362595318,
            20222362583688,
            22315862595949,
            20222362608494,
            20222362527175,
            22318662573102,
            22328862575813,
            22333662513325,
            22317262577991,
            22318662578113,
            22328862574803,
            20220262594805,
            22317262596949,
            22322362576896,
            22317462597863,
            20215862568951,
            22325162576133,
            14422362591666,
            22328862575651,
            14417462602954,
            14422362602732,
            14414462585364,
            14428362246094,
            17427162520355,
            22322362597358,
            14427162586359,
            22314462595090,
            25120262599689,
            14414462580519,
            20220262591933,
            30227162584737,
            20220262571219,
            202246862444466,
            20228862538809,
            20222362538594,
            20220262539080,
            20222362567815,
            20228362538874,
            22331162537000,
            20222362336445,
            20228862536476,
            20228362558150,
            20222362566537,
            20222362573431,
            22317262528750,
            20218662558423,
            22328362538165,
            22328362537719,
            22328362533701,
            20222362551256,
            22315962557322,
            20222362520214,
            22318662565101,
            22320262539331,
            14417262569303,
            14422362542551,
            22320262555712,
            25122362557573,
            20220262496833,
            20220262564213,
            20222362502633,
            22317262558897,
            38322362430961,
            17422362552133,
            28814462541203,
            14428362541719,
            14422362558007,
            17420262540762,
            17420262560053,
            17420262540329,
            17420262540647,
            22317262539616,
            27117262554906,
            27117262554909,
            30220262543150,
            28420262547630,
            23720262537840,
            23717262538068,
            23715862537819,
            11122362540773,
            20214462527975,
            20225162502156,
            20225162488158,
            20225162489500,
            20222362504712,
            22328862529463,
            22325162527349,
            22320262517971,
            20222362470790,
            20222362480654,
            22318662508198,
            22320262533893,
            20220262528635,
            20225162523101,
            25120262486636,
            25120262477150,
            14420262520979,
            22320262325109,
            25122362522648,
            25128362522557,
            20222362504351,
            22320262504370,
            22317462521543,
            22322362528197,
            14425462372051,
            25120262472879,
            25128362534727,
            25117462534705,
            14428862528687,
            20220262505315,
            14420262528837,
            17420262507471,
            14420262509245,
            14420262509329,
            17420262529509,
            14420262509161,
            25120262527200,
            17420262506440,
            27117262515690,
            23717262504902,
            23720262504968,
            23728362504890,
            20214462483984,
            20213862453423,
            22325162454263,
            22311162460985,
            22320262461965,
            20222362482847,
            20225162489328,
            22320262464523,
            20220262483008,
            20218662462945,
            20222362483839,
            20217462488470,
            14425562488345,
            14422362436196,
            25120262472734,
            30228862488754,
            38320262486215,
            23720262461842,
            28420262456687,
            20222362421442,
            25122362434981,
            28820262428022,
            20228862368001,
            20214462416515,
            22317462379888,
            30217462397658,
            38322362389904,
            17422362374390,
            22317462210335,
            20228862258753,
            14428862347558,
            10122362339149,
            20220262292855,
            22325462291632,
            20217462316993,
            22320262311419,
            27128362314656,
            20217462281298,
            14411162272093,
            27115862271759,
            20225562233734,
            20217462199545,
            20222362121044,
            20222362087696,
            30222361977563,
            20222361775160,
            14422361679850,
            20222362567645,
            20222362567722
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
