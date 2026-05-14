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
            20220262820462,
            20220262819687,
            20220262837084,
            20220262838839,
            20220262826299,
            20220262817978,
            20220262834258,
            20220262828142,
            22322362840170,
            22326762793861,
            20220262829273,
            20220262838975,
            20220262839264,
            20220262762190,
            20220262762151,
            20220262763040,
            20220262763010,
            20220262762412,
            20220262762365,
            20220262763006,
            20220262762301,
            20220262762308,
            20220262762128,
            22322362817240,
            22322362817221,
            22315862820436,
            20220262829304,
            20220262679774,
            22325162818320,
            22325162817770,
            20220262820300,
            20220262822935,
            20220262844280,
            20220262782606,
            22326762840800,
            38325162805654,
            20220262832071,
            20220262735441,
            20220262839625,
            14422362837973,
            14414462838977,
            14422362823088,
            20220262817653,
            14422362772767,
            27114462829769,
            29322362832664,
            12514462821297,
            11022362782558,
            11022362782723,
            20220262829457,
            23728862818685,
            23722362818768,
            23714462818684,
            23728862818775,
            20220262786572,
            20220262782698,
            20220262784762,
            20220262784893,
            20220262761077,
            20220262785122,
            20220262786575,
            20220262802362,
            20220262762802,
            20220262762852,
            20220262762529,
            20217262784531,
            20220262792286,
            20217262792239,
            20220262802976,
            20220262799651,
            20217262802941,
            20220262809022,
            20220262784560,
            20220262786853,
            20220262808334,
            22322362806736,
            22325162800392,
            22322362286853,
            22322362756033,
            22322362756160,
            22322362784910,
            22314462782696,
            22325162756196,
            22333662713363,
            22325162738270,
            22328862723472,
            22314462787993,
            20220262767103,
            20220262798425,
            14422362800703,
            22322362782943,
            22325162784020,
            22322362783258,
            20220262796967,
            22322362806545,
            15815862815447,
            22317462720538,
            20220262782200,
            20220262792016,
            28822362797260,
            25122362807902,
            15814462787193,
            20220262805783,
            22325162785361,
            27122362763409,
            28828862791341,
            20220262807860,
            11020262783148,
            28420262798118,
            20220262766306,
            20220262751568,
            34015862798375,
            20220262781623,
            34022362788778,
            20225162750981,
            20220262722450,
            20220262746767,
            20220262745089,
            20220262747402,
            20214462610564,
            22318662661281,
            22317262773207,
            20225162761614,
            20217462766734,
            20225162713408,
            22320262778189,
            22320262752530,
            20228862763036,
            22314462781132,
            20217462763173,
            20222362695745,
            20215862763042,
            20217262763183,
            20222362695751,
            20220262760019,
            22320262731544,
            22322362748239,
            22322362747479,
            22320262748249,
            22328862747446,
            22320262748060,
            22317462746153,
            22320262722341,
            22317462714608,
            14428862753080,
            20217462758860,
            17425162781199,
            14422362780041,
            14422362759120,
            20220262755317,
            14422362765299,
            20225162543776,
            20225162759476,
            20222362752374,
            20217462670144,
            25120262767546,
            22328862771604,
            22320262739480,
            20220262777661,
            28822362755911,
            25122362779658,
            25120262779685,
            22320262756280,
            25120262773739,
            14420262756665,
            14417262756576,
            20222362747880,
            20225162739990,
            14417262767461,
            20222362760838,
            17414462749191,
            14417462744701,
            14420262646446,
            17420262750157,
            30214462756264,
            20220262767023,
            11020262707898,
            27120262747199,
            27125162757652,
            30222362746641,
            30220262765856,
            30228862765725,
            20214462739704,
            20225162654208,
            20220262654214,
            20220262733280,
            20220262733310,
            20231162713141,
            20220262713098,
            20217462737011,
            20220262660433,
            20214462724942,
            20228362623045,
            20222362716259,
            20225162713028,
            20214462622999,
            20214462713081,
            20222362714876,
            20228362689051,
            20225162713051,
            20220262643430,
            20222362739311,
            22320262717882,
            20228862695494,
            20227162696416,
            20222362695624,
            20228862695872,
            20222362743270,
            22322362617655,
            20222362696163,
            22320262736041,
            22320262736047,
            20222362695816,
            20222362696003,
            20214462695950,
            20220262696235,
            20214462695991,
            22328362711629,
            22320262731807,
            20222362720019,
            20228862695913,
            20220262695835,
            20222362695827,
            14427162719272,
            14420262743140,
            22317462744692,
            20217262706057,
            20225162740012,
            14420262730121,
            20225162647956,
            14422362730545,
            22317462722862,
            22320262714061,
            22328362714683,
            25120262732559,
            22320262744096,
            22320262718180,
            17420262728602,
            14417262719268,
            14420262718247,
            14420262718288,
            15817462743448,
            20222362735555,
            20222362702277,
            15822362743659,
            14420262719234,
            22320262723593,
            14425162724630,
            14420262728245,
            14420262710484,
            38328362715665,
            38317262731923,
            38320262729709,
            25120262736854,
            20218662728228,
            25122362722134,
            28320262721533,
            28320262721547,
            22320262715395,
            14420262729577,
            27122362724298,
            27120262665294,
            18820262694576,
            30220262718378,
            11117462718172,
            23720262714367,
            23722362714379,
            23718662714338,
            23720262714085,
            23720262714368,
            34022362639493,
            34022362618378,
            34027162639698,
            20222362669181,
            20225162611145,
            20214462672146,
            20215862701839,
            20222362669640,
            20215862703834,
            20217462652103,
            20214462590241,
            20222362613597,
            20217462613573,
            20228862687311,
            20215862669420,
            20214462611136,
            20220262658457,
            20217462669170,
            20217462667222,
            20228862672340,
            29320262699937,
            29320262699401,
            29322362700710,
            29320262697221,
            20222362703626,
            20217462703645,
            20222362703720,
            20222362690286,
            22328862668762,
            20222362681095,
            20220262620689,
            22328362658505,
            22317262670754,
            22317462667910,
            22320262661200,
            22325162669094,
            22328862658477,
            22322362667591,
            22328362661310,
            25114462711123,
            25125562698709,
            20214462678916,
            20222362696303,
            20222362696330,
            20222362696317,
            20227162696318,
            22320262697119,
            20217462673124,
            22320262697088,
            22320262701481,
            14417262703387,
            14420262686107,
            14420262690589,
            14420262663060,
            20222362697163,
            20217462698916,
            22320262704983,
            14422362673742,
            30220262660452,
            14428362675442,
            22328362668560,
            20222362677245,
            20225162680865,
            28320262649046,
            28828362691343,
            14422362687709,
            20220262693041,
            20231162703420,
            20228362699328,
            25120262701455,
            25122362691311,
            17417262692862,
            17420262676669,
            17420262674150,
            27117462633613,
            30217462650390,
            27120262648222,
            27120262665325,
            27120262682291,
            22320262659713,
            28422362663185,
            20220262653904,
            20217462658149,
            20227162649669,
            20215862589062,
            20215862555588,
            20228362652101,
            20222362650222,
            20215862646810,
            20222362645950,
            20228362613634,
            20214462610637,
            20228862610621,
            20220962627454,
            20225162628078,
            20220262582922,
            20222362639819,
            20215862658152,
            20228362608230,
            22328362644041,
            22320262609649,
            14428362657882,
            20222362614723,
            14422362644519,
            20228362612531,
            22328362611023,
            20227162654505,
            15822362640289,
            22328362625241,
            38320262644111,
            27128362610378,
            30220262634372,
            30222362631543,
            30220262622756,
            30222362622553,
            25120262621305,
            14420262622097,
            14417462639310,
            23728362622348,
            20210662567849,
            20222362553478,
            22328362464415,
            20217462568184,
            20220262336471,
            20228862336008,
            20228862536472,
            20228862567960,
            22320262595248,
            20222362597157,
            17420262594848,
            20217462589913,
            20218662566258,
            14428862483386,
            20226462539059,
            20220262539079,
            20217262335882,
            20228862536386,
            20220262335830,
            20228862335664,
            20225462484701,
            22320262513351,
            22328362535873,
            22317462540176,
            22316162554123,
            15822362569327,
            27128862555229,
            20222362525764,
            22328362513249,
            25128362512663,
            38322362495685,
            20222362418868,
            22317462432623,
            20222362426389,
            25120262431962,
            20220262203277,
            22320262409768,
            20217462318618,
            20222362206832,
            17420262289681,
            20222362228093,
            22322362123409,
            202352262115753,
            27120262068661,
            28418261926430,
            20222362703833
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
