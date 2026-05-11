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
            22314462755221,
            14422362771529,
            14425162771575,
            22325162777049,
            14415862756693,
            22315862776492,
            20220262713096,
            20220262713030,
            20220262727126,
            22325162660947,
            22325162617083,
            20220262695801,
            20220262696391,
            20220262726734,
            20220262726754,
            22322362739975,
            22327162737526,
            22322362738341,
            20220262717837,
            20220262695988,
            20220262726687,
            20220262695819,
            20220262695865,
            22325162624622,
            22322362712830,
            22325162711678,
            22322362712790,
            22325162714324,
            20220262695576,
            20220262695798,
            20220262720060,
            14422362719206,
            22322362726799,
            14427162737713,
            14422362730820,
            22322362700096,
            14425162719342,
            14422362719070,
            14427162718284,
            14417462686833,
            14422362729250,
            38322362715481,
            38314462715497,
            28817462676852,
            22322362732397,
            14425162724814,
            20220262733673,
            20220262732924,
            30214462719201,
            30222362729025,
            23714462714353,
            23714462714079,
            23714462714292,
            20220262624899,
            20220262613567,
            20220262686762,
            20215862666839,
            20220262661153,
            20220262669178,
            20220262669600,
            22333662701487,
            22314462686461,
            20217262680891,
            20220262673128,
            20220262703711,
            20220262700311,
            20220262696053,
            22314462654093,
            22322362667874,
            22314462663448,
            22314462666829,
            22322362663451,
            22317462670723,
            22322362667590,
            22325162663367,
            22317462666213,
            22322362694393,
            20220262681203,
            20217462684681,
            20220262687037,
            25114462703872,
            20220262696234,
            20220262696261,
            20220262696446,
            20220262696278,
            20220262696438,
            20220262696332,
            20220262696122,
            20220262696282,
            20228862696186,
            20220262696288,
            22325162704388,
            20220262696281,
            14414462674390,
            14422362696491,
            14422362693013,
            14450562663318,
            22322362704767,
            14425162701135,
            25114462693221,
            20217262679749,
            14417462709247,
            14422362674069,
            14428862667429,
            22320262705117,
            14422362672177,
            22325162697768,
            22317462685469,
            22320262659909,
            14431162701273,
            22320262684978,
            22322362692613,
            22320262670560,
            22320262670842,
            20220262696159,
            20220262696291,
            17414462700823,
            17420262703846,
            14428862675191,
            14428862674631,
            17417462679242,
            17417462703749,
            14422362687471,
            20220262704362,
            17417462705776,
            20220262696728,
            20220262700962,
            14425162674211,
            20220262664441,
            20220262663597,
            17422362694413,
            14422362649843,
            17414462651104,
            17414462674799,
            17420262668162,
            15814462668982,
            22314462687456,
            20220262664440,
            20220262665304,
            17422362670014,
            20220262609288,
            19522362687022,
            14417462665684,
            14422362665714,
            14417462665685,
            31117462675180,
            30222362697470,
            30222362675904,
            14422362663700,
            14427162662601,
            14422362664330,
            14422362666574,
            14422362664267,
            14425162663540,
            14425162665727,
            14431162663637,
            14422362662246,
            14422362662229,
            14414462664153,
            14422362663642,
            14428862662412,
            14415862662811,
            14422362664649,
            14428862663714,
            14414462664199,
            14422362663489,
            27115862623722,
            23722362666900,
            23722362666598,
            23722362666938,
            23715862668116,
            20220262640255,
            22317462661028,
            22317462659936,
            20217462629495,
            20222362629063,
            20222362590212,
            20214462641286,
            20222362642274,
            20214462624512,
            20228862624522,
            20217262654282,
            20228862654185,
            20217462624499,
            20222362624533,
            20228862616474,
            20222362556869,
            20225162627353,
            22320262640476,
            20222362642698,
            20220262609667,
            20228862623672,
            20220262639828,
            20222362633486,
            20222362640714,
            20220262654255,
            20225162658242,
            20222362639428,
            20213862645994,
            22320262641000,
            20217462609392,
            20217462624586,
            22320262644045,
            22320262643518,
            22320262653900,
            22320262648851,
            22320262648941,
            22328862648064,
            22318662653610,
            22325162655911,
            20228862574369,
            20222362624540,
            20214462645919,
            20222362646828,
            22320262610379,
            20228862616277,
            20222362645942,
            20228862616262,
            22317262621770,
            14428862654803,
            20225162600552,
            20215862612480,
            20217962596717,
            14422362640749,
            20217462648292,
            14422362635831,
            14422362654967,
            14422362652848,
            22320262636865,
            14428862641645,
            14417262641630,
            14422362648101,
            20220262645601,
            14428862630883,
            14428862630886,
            14422362641654,
            20222362645441,
            14431162651900,
            14417262650605,
            14425162641452,
            22322362614632,
            14426762649584,
            14415862651899,
            14417262650589,
            14428862650583,
            14417262653387,
            14422362641296,
            14415862649961,
            20228862560841,
            22320262656074,
            22322362650735,
            14417462654837,
            14420262643765,
            14422362641691,
            20222362627067,
            20222362627209,
            17422362649320,
            20217462490270,
            28822362653506,
            14417262646031,
            14417262626973,
            25120262567672,
            25122362656760,
            20222362642288,
            14425162646041,
            28814462610032,
            25120262657202,
            14422362642694,
            14428862635905,
            14422362646040,
            28828862628901,
            20220262590677,
            28827162641356,
            17420262648389,
            17420262620655,
            25117262646344,
            25118662650010,
            28822362622013,
            17420262645262,
            22317462617584,
            20222362645197,
            30217462611073,
            22320262622315,
            38322362647084,
            27120262609375,
            27120262624158,
            25117462621525,
            25120262621321,
            30220262631493,
            30220262640509,
            25114462621425,
            25122362621526,
            25114462621486,
            25117262621412,
            25120262621434,
            25120262621444,
            14450362607901,
            30222362622490,
            30225162622462,
            25122362621377,
            25122362621436,
            25128862621427,
            27114462613636,
            30220262622884,
            30228862622633,
            27114462622172,
            27120262637357,
            28328862640148,
            30222362639078,
            30222362638703,
            30228862638337,
            30217462629570,
            30222362631229,
            18617462623747,
            27128362613013,
            23720262622504,
            23717262622116,
            23720262622247,
            23728862622266,
            23715962623571,
            23722362623465,
            23728862623469,
            23728862623141,
            27120262581973,
            27120262576690,
            20217462573049,
            20220262567912,
            20220262574768,
            20225162588878,
            20225562574787,
            20225162567199,
            20220262573899,
            20217462564995,
            20225162568180,
            20225162567840,
            20218662566628,
            20225162566498,
            20225162567534,
            20222362566064,
            20225162567369,
            20226762566376,
            20225162567619,
            20222362566038,
            20210162566445,
            20220262567017,
            20222362568121,
            20225162568122,
            20222362567287,
            20225162567804,
            20225162566954,
            20225162567211,
            20214462567453,
            20214462566199,
            20214462567161,
            20225162568043,
            20225162567212,
            20222362566518,
            20222362536490,
            20228862536960,
            20220262336329,
            20225162536892,
            20225162566410,
            20225162567528,
            20225162566884,
            20225162566213,
            20225162566107,
            20222362567435,
            20225162567935,
            20222362566947,
            20228862536563,
            20225162536932,
            20222362461730,
            20225162536171,
            20250362536536,
            20220262336480,
            20222362536342,
            20225162335647,
            20213862536301,
            20222362536139,
            20225162567214,
            20233662567532,
            20225162567544,
            20214462567135,
            20225162538889,
            20222362567933,
            20222362567470,
            20225162566931,
            20225162567700,
            22320262595247,
            22320262590546,
            22320262595953,
            22328862586025,
            20233662580228,
            22328862574297,
            22322362575984,
            22314462537526,
            22325162573956,
            20225562594790,
            20225162568962,
            20231562597181,
            22320262576934,
            20222362608662,
            14420262592536,
            14420262591700,
            14428862585370,
            14422362592236,
            14422362592887,
            20222362568893,
            14422362583139,
            14422362600464,
            14425562608111,
            20217462534927,
            15820262592640,
            22320262574098,
            22320262577367,
            31528862608103,
            14420262596864,
            14415862592905,
            14428862592799,
            14420262592983,
            25120262595996,
            14422362565142,
            25127162607396,
            22320262577134,
            22320262577112,
            15820262576821,
            14420262590160,
            15820262576825,
            22315862584138,
            14420262579304,
            14417462591964,
            14420262580422,
            14417262579281,
            14417262590480,
            30220262574941,
            30220262581238,
            30220262574596,
            30220262585394,
            30220262585845,
            28420262528418,
            34020262515065,
            10120262589380,
            23720262574616,
            23720262574642,
            20220262484476,
            202207062515529,
            20220262539065,
            20228862538876,
            20228862540191,
            202668062513306,
            20222362567874,
            20224462486011,
            20217462566404,
            20225162336075,
            20225162536510,
            20250362536894,
            20220262336345,
            20225162335660,
            20225162536905,
            20225162536922,
            20222362536898,
            20220262537569,
            20222362550871,
            20222362546112,
            22328362528571,
            22320262569310,
            202632362569855,
            14420262559125,
            14428862557891,
            20222362463093,
            30228862552531,
            14420262557902,
            14420262558517,
            22320262538880,
            22320262541117,
            14410162565998,
            14422362559228,
            14425562558461,
            20222362537171,
            14420262542988,
            14428362542533,
            14420262299186,
            17420262554344,
            20220262537367,
            14420262566694,
            14420262566150,
            14420262566011,
            14420262566847,
            14422362537355,
            28327162566819,
            30231162552784,
            30220262552771,
            27150362513281,
            30220262552748,
            30220262553072,
            30220262543133,
            23720262537810,
            25450362483780,
            46550362521656,
            20220262501844,
            22328862503313,
            20220262480720,
            20222362442394,
            20228862528717,
            22320262524541,
            20225162509504,
            38320262499584,
            14428862528793,
            14420262521002,
            14420262522931,
            22328362514063,
            14420262514890,
            14420262526501,
            25120262534265,
            14420262528522,
            17431162506428,
            17420262506422,
            14420262471710,
            17428862506411,
            30220262517323,
            30220262517272,
            34015962514449,
            31828862493330,
            12528862467363,
            23720262504975,
            20222362493717,
            20228862500193,
            20222362491897,
            20220262491923,
            20222362497436,
            20225162456544,
            20225562463489,
            20222362425063,
            20228862455827,
            20222362455085,
            20225562467464,
            14428362457577,
            14420262458973,
            22328362491046,
            14422362473820,
            14428862473207,
            14422362474607,
            14422362473194,
            28820262470078,
            14420262478901,
            14428362473746,
            15828862498720,
            17420262488808,
            17420262491748,
            17422362480357,
            17422362480624,
            17422362480368,
            17420262480598,
            17422362480589,
            17422362480465,
            17422362480611,
            23720262462028,
            23722362462136,
            20217462451826,
            14425662441024,
            22320262419405,
            14411162431271,
            26714462429652,
            17422362442904,
            20228862391255,
            20222362416235,
            20222362392007,
            20228862393329,
            22328862411919,
            22320262366520,
            17420262393993,
            23720262392126,
            23720262392134,
            17422362340521,
            17425162360030,
            30217462368410,
            20228862296186,
            20222362348188,
            20217462298162,
            14422362348478,
            14411162333772,
            20222362340017,
            20228862250411,
            14417462314777,
            17420262291637,
            17420262291671,
            28822362241464,
            20217462241422,
            20211762273178,
            20228862263327,
            27128362230606,
            20222362191807,
            20222362144320,
            14422362171160,
            20222362036119,
            20220261871178,
            20220261539673,
            15820261027101,
            20220262727870
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
