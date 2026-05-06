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
            12527162577199,
            22327162526078,
            27128862557298,
            14427162519920,
            20217462502350,
            20227162441778,
            14427162322364,
            20227162426302,
            14427162557035,
            341027162388305,
            22327162504480,
            22327162526566,
            20227162324879,
            14427162509396,
            22328862236537,
            22327162537996,
            15817462568940,
            14427162538503,
            20228862234845,
            20220262484516,
            22322362483539,
            22328862456617,
            22350362484431,
            22317962480577,
            17428862417119,
            20225162442018,
            20222362442407,
            14420262443547,
            22317262432502,
            22320262432265,
            22320262409152,
            20228862267938,
            28820262346340,
            28317462349154,
            13517262257372,
            22322162219380,
            27122362221536,
            20220262483049,
            20217462285509,
            20228862536193,
            20220262528632,
            20225162078379,
            38325162558746,
            20224462488880,
            14425162565335,
            20217462518982,
            28417462512341,
            20214462542090,
            22314462586156,
            22320262414588,
            20215962539061,
            20225162115494,
            30217462294491,
            38314462513335,
            20228862539045,
            20214462515825,
            29327162555688,
            20215862494339,
            20228862558621,
            20227162446075,
            22317462442377,
            22317462513618,
            20228862538509,
            31517462524139,
            20214462526799,
            22320262492953,
            22314462583888,
            17420262419090,
            20215862498304,
            22320262429787,
            14420262517696,
            22320262517900,
            20220262582561,
            20220262481598,
            20220262541747,
            22315862576470,
            20227162536909,
            20228862538590,
            15820262465406,
            14420262509077,
            14422362451966,
            14422362557389,
            23720262538245,
            20228862296051,
            22320262481774,
            17425162586352,
            23725162574870,
            23720262504963,
            20220262581189,
            31820262506287,
            20218662478746,
            223661662513262,
            20228862551689,
            20228862479386,
            20217462395896,
            22314462591524,
            20225162527751,
            28420262540027,
            22317462539884,
            20215862336115,
            29317462391397,
            20227162236295,
            22328862487317,
            22325162573659,
            14425162583435,
            20220262494529,
            20228362566190,
            20225162506150,
            20225162519221,
            20225162542105,
            20220262505532,
            22327162506825,
            14434062569218,
            20217462563273,
            14434062559107,
            20220262592697,
            20228862527877,
            14420262552115,
            22317462554596,
            27128362361910,
            14420262257237,
            223687862326272,
            22322362504592,
            20228362521396,
            20228362536658,
            20228362536900,
            30220262514454,
            20226762567863,
            14420262542794,
            22320262529430,
            25128862477148,
            31528862442918,
            30225162574562,
            20228362567793,
            23720262504870,
            20217462189228,
            20222362329189,
            20214462502161,
            22314462458162,
            20225162434628,
            20220262336259,
            25117462477169,
            15825162540622,
            22317462544908,
            30217462558620,
            14425162579072,
            14420262559291,
            14417462472032,
            20214462538016,
            14428862440198,
            25115862477144,
            20222362527343,
            20225162409274,
            22314462578095,
            20214462527981,
            20222362536798,
            20228862346506,
            14422362585345,
            25122362344567,
            22318362294073,
            20222362442423,
            38322362511606,
            20222362536173,
            20222362542420,
            20222362059877,
            20231162452414,
            20222362502138,
            20222362528616,
            25122362534721,
            22322362577824,
            22322362586183,
            22322362597704,
            28822362455078,
            38322362497017,
            22322362574797,
            22322362574083,
            14422362590454,
            20222362496776,
            14422362591697,
            28822362527912,
            30231162549329,
            20217262566507,
            14422362580383,
            14422362592870,
            20217262336378,
            20217262485901,
            30217262517198,
            20222362534364,
            20217262558184,
            20222362557855,
            20217262564709,
            20217262567719,
            20217262567011,
            20217262591166,
            20222362469318,
            22322362586953,
            22322362597368,
            22317262504607,
            20222362518489,
            14417262525069,
            22322362528485,
            17422362543482,
            20217262568314,
            22320262385823,
            20222362536624,
            23715862574618,
            22322362595910,
            14422362592784,
            20222362296341,
            20217262567363,
            23731162574498,
            22320262490421,
            38320262510553,
            23720262537744,
            20231862579311,
            174624462350676,
            20220262579189,
            20246662034437,
            25117462357970,
            29320262329840,
            341020262505483,
            30220262544105,
            20222362115507,
            27120262242602,
            22310262346341,
            22320262392728,
            20222362501787,
            22320262539681,
            27122362538601,
            20222362558614,
            20220262566880,
            20222362536199,
            25120262399558,
            20225162501840,
            25122362478536,
            23720262504948,
            23720262461439,
            14420262509741,
            20220262568734,
            20220262607440,
            23725162537844,
            28428862508675,
            25122362584634,
            31517462553601,
            22322362577887,
            23722362537715,
            20222362524587,
            20222362473710,
            23720262504967,
            30220262513975,
            20228862538805,
            20217262502637,
            22325162574339,
            14417262514541,
            22320262535896,
            20222362491309,
            20222362517934,
            22328362462313,
            22322362595840,
            29314462551943,
            22317262485016,
            22328362479394,
            17414462499525,
            28328862482355,
            28322362524690,
            22320262565876,
            20228862504706,
            22320262560284,
            20225162501921,
            20222362573429,
            20222362441771,
            20225162434661,
            25125162583030,
            38328862514838,
            20222362462353,
            25122362501650,
            20222362506343,
            14420262479472,
            20220262534535,
            20220262519773,
            20217262595378,
            22314462575242,
            20222362527457,
            22320262488798,
            22320262464806,
            20222362566269,
            28820262425593,
            20220262439845,
            14415862471735,
            20215862104758,
            22317462173208,
            14422362499234,
            14420262481348,
            25120262477160,
            20220262444449,
            20225562391231,
            20228362568092,
            22325162595230,
            23722362537764,
            22325162575372,
            20222362536135,
            14433662543292,
            20222362524365,
            14420262562762,
            14415862521012,
            22315862534267,
            20215862565498,
            22315862567637,
            14415862554587,
            20228362566571,
            20222362536202,
            20217262514792,
            20217262583708,
            20228362568526,
            20220262335665,
            20228862393049,
            20228362539120,
            22328362464536,
            22328362491843,
            20222362517537,
            14428862424274,
            20225162529606,
            30225562519824,
            20220262595040,
            23720262504960,
            38325162583988,
            38328362486553,
            20215862568081,
            14420262520942,
            30222362581875,
            20222362491938,
            20217462442397,
            23714462575182,
            22325162575809,
            20228362536485,
            20220262592091,
            20220262558993,
            14420262509999,
            22317462557488,
            28320262552160,
            20220262321861,
            341028362461776,
            20225162528728,
            22325162505848,
            20217462340229,
            20217262528146,
            22320262553888,
            22328362455316,
            20225162595009,
            22320262552250,
            22328362480544,
            20228362596183,
            22320262488825,
            22320262511376,
            23720262504881,
            23728362462110,
            23720262538071,
            20222362442385,
            20217262555379,
            20220262484342,
            20214462495218,
            22320262515567,
            14417262473402,
            10120262477416,
            20217462421741,
            22320262478646,
            17420262484302,
            17420262527610,
            20214462537803,
            20225162489367,
            20220262580225,
            20220262558010,
            20225162592985,
            14415862579032,
            14422362509571,
            22314462539414,
            20222362402086,
            20228862539699,
            20222362536333,
            20225162598284,
            23720262461679,
            30228862552986,
            25120262477155,
            20220262595718,
            20220262536619,
            17420262559603,
            20222362529952,
            20228862501613,
            20225162528618,
            20222362529604,
            27122362519162,
            22320262529273,
            14422362517452,
            30222362574132,
            20214462336088,
            20228862551865,
            27120262221990,
            27120262430206,
            20220262324454,
            23727162537808,
            22310662516585,
            27120262216430,
            28422362424190,
            27128362460903,
            20228362521429,
            20228362544174,
            20220262557080,
            25120262529629,
            22320262465354,
            14420262190987,
            20220262526410,
            20220262482763,
            14428362473772,
            22328362455207,
            17420262273773,
            20222362557487,
            27120262546710,
            22317262460966,
            20220262494695,
            25122662309078,
            22320262451145,
            22320262468847,
            38320262468715,
            20217462487784
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
