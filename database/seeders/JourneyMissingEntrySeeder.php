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
            152991742758,
            30225160957016,
            28825160977879,
            20214461027583,
            17414460983707,
            26717461071825,
            30228860979760,
            22320260966200,
            22328360790493,
            22317461036101,
            14414460967517,
            22315860842895,
            20228860931751,
            22317460918375,
            30227160972020,
            20214460931764,
            25115861033378,
            38328861032219,
            30225560944626,
            20218660799916,
            14415861080155,
            25128861005004,
            20217460959423,
            20214460979491,
            22320260925623,
            20214461023115,
            20231160995117,
            22327160971795,
            20217460944247,
            25127160963705,
            14417461038075,
            20214460930092,
            20228860873000,
            15915861106455,
            22320261002866,
            14415861024220,
            20222360952037,
            20215861032401,
            20215861076562,
            30215861106158,
            14427160846393,
            22315861009699,
            22322360913737,
            30222360956997,
            20222360981634,
            20222360980957,
            20222360995850,
            28822360978023,
            14422361115945,
            17420260834558,
            20222360974361,
            20222360996811,
            20222360960897,
            28420260965357,
            20222360697504,
            20222360914342,
            20222360949565,
            20222361037652,
            20222361010766,
            20222361018205,
            17422360979111,
            22322361073827,
            20222360940217,
            22320260943627,
            20222361036840,
            14422360832632,
            20222360978843,
            20222361027774,
            20220261032415,
            17422361006386,
            25120261001473,
            20225161027464,
            20222360919223,
            14415861066964,
            20220260985708,
            14422360964944,
            22320260999654,
            27117260892193,
            25117261033431,
            20220261078596,
            20222360930099,
            20222360961000,
            20222360869406,
            22327160959968,
            152991749787,
            20222360983632,
            30215860396599,
            152991730698,
            22320260563342,
            22331560641266,
            22320260616959,
            15817261017877,
            20215860849409,
            22320260934297,
            30220260759142,
            20225160880875,
            15822361084400,
            22331161081026,
            30222361105004,
            30222361106700,
            20222361034631,
            14415861115378,
            17428861099346,
            30222361000505,
            11020260909235,
            20228361019793,
            20222360834546,
            20217460919324,
            20220260919099,
            20222361032895,
            20228360983550,
            20220261072104,
            14422360966753,
            20215860953538,
            22320260897908,
            20225160923112,
            20222361003125,
            22320260922204,
            22328860913885,
            20220261071355,
            14420261024307,
            20228861003489,
            31128860998403,
            17420261003912,
            20214460960066,
            22322361109259,
            20214460975255,
            30217460870926,
            30222361105418,
            30217460971050,
            20222360979514,
            20222360978878,
            20220260961931,
            14417260945280,
            28828360792502,
            20222361021188,
            30215860998128,
            25122361028279,
            20220260791542,
            25128860540462,
            22328860585276,
            14420260711199,
            20217460775819,
            30220260822732,
            20217460825857,
            14420260812192,
            34028860867366,
            14417460952459,
            14417460950521,
            20228860526463,
            22320260599304,
            28422360671951,
            20220260693594,
            20222360709727,
            22320260750586,
            22320260748380,
            22320260760424,
            28820260831924,
            14420260854640,
            17420260864263,
            20220260879412,
            22320260921383,
            20222360926378,
            20222360927598,
            28822360935603,
            20220261029980,
            25120261035071,
            20214460870789,
            22322361111105,
            20214461027452,
            16117260975453,
            14422361038085,
            20215861030265,
            22317461017966,
            22317460949243,
            25127161052978,
            20222361022251,
            25122361021579,
            20233660888411,
            38320261016636,
            27122361063887,
            20218660872863,
            17425160959923,
            30227160939925,
            22320261006900,
            22328861081307,
            25128861035080,
            14415860919565,
            22315860962960,
            20220261017775,
            15920260928723,
            20220261065241,
            22328861012147,
            20227160803480,
            20214460983870,
            26717461074429,
            20214460979496,
            20222361018738,
            19522360977974,
            20222360963789,
            14428860917409,
            30222361073627,
            20228860859260,
            20228360996200,
            20217460935159,
            20220261037528,
            22328360883502,
            30217460960605,
            20217460942892,
            22317460895463,
            17428861097089,
            30222361068453,
            20222360950840,
            22317460832775,
            22320260790352,
            22328861040229,
            20222360931774,
            25127161051988,
            22328360801552,
            30227160992501,
            19528360895011,
            14422361071777,
            20222360970845,
            20222360992622,
            30217460972060,
            22320260908744,
            20220260973105,
            20228860864076,
            22328861029289,
            30217460977912,
            22322361030056,
            20228860807008,
            30228861041167,
            30228860971018,
            20222360872867,
            20222360999299,
            22322361080843,
            30228860947131,
            14422360965552,
            22317461003362,
            30227161004393,
            20222361036176,
            20222360959602,
            20215860855352,
            30222360973085,
            22320260990267,
            22320260863036,
            25131161087084,
            152991749657,
            20220261017015,
            22325160930175,
            25120261028309,
            25120261035324,
            28822361004429,
            20220260996709,
            20225161028347,
            28420260845814,
            25120261035313,
            30227160973654,
            28427160933190,
            20222361034694,
            14420261034447,
            20222360981092,
            14420261022182,
            28828860930632,
            20220261036645,
            20222361057254,
            22320260995414,
            22322360919827,
            20228860948785,
            25128861033240,
            14415861024738,
            30215860929774,
            22320260912737,
            30222361070052,
            20220260826960,
            22322361109074,
            14428360893323,
            34220260980621,
            13420260874858,
            20225161075978,
            20225161078736,
            20228860785672,
            20217460690605,
            20228860886975,
            20214460981621,
            30222360977838,
            20222360960814,
            14420260852846,
            20222360839321,
            14415860966204,
            152991746884,
            20222361037595,
            20231160741497,
            22320260717445,
            14422360748516,
            22325160871454,
            22322360979322,
            20222360996927,
            30222360999184,
            14414461037279,
            20220261077533,
            30215861059023,
            20214460960220,
            30215861028696,
            30214460957361,
            20222360958398,
            22320260939598,
            22320260832758,
            20228361062370,
            30225160970978,
            20220261016901,
            20225160930030,
            20225161003107,
            14420260968043,
            22320260967241,
            20222360931868,
            20222360640117,
            14422361034127,
            30225161061472,
            25117261028265,
            20222360994969,
            14422360945182,
            20217460714386,
            22320260823067,
            14426761020157,
            30214461067988,
            20220261003045,
            27114460980774,
            20220260349253,
            22322360854587,
            25120261028350,
            26728860805592,
            20217461019791,
            30214461001280,
            22320260912622,
            30222361111698,
            20222360988474,
            22320260921370,
            22320260634362,
            20225161027560,
            14420260815834,
            20215860959882,
            20222360995131,
            152991748669,
            20222360932185,
            20215860993193,
            17420260926079,
            14420261022396,
            20214461035595,
            20225161062425,
            22322361117976,
            20222360961812,
            22320260911634,
            20220261077273,
            20220261017586,
            30222361064964,
            14422361032441,
            22322360912141,
            20217260985713,
            25117260912452,
            22325161087877,
            20220260908052,
            14414460987402,
            20214460831880,
            22314460955401,
            14417460963248,
            17417461101221,
            20220261117576,
            20220260988613,
            22328861003499,
            20220261071210,
            25120260998508,
            20220260864619,
            22317260995424,
            22320260890904,
            152991748161,
            30227160971037,
            22320260545518,
            22320260583819,
            20217460856899,
            20220260960915,
            20228860962574,
            20228861005133,
            14431160968665,
            16117461091472,
            22317461118233,
            14417460781496,
            20215861071275,
            20220261143953,
            30217460919997,
            22328861071296,
            22317461071310,
            20228861037588,
            20214460977512,
            22328860999962,
            30214461012266,
            20228860966812,
            22320260923656,
            30224461000629,
            20214461080642,
            22314461062687,
            20210661023610,
            22317461018379,
            20250561025972,
            20228860947493,
            17425161089306,
            20215860523424,
            22324460915564,
            22320260823162,
            20214460955809,
            34230260587984,
            20217460922440,
            20217460835061,
            14450560950445,
            28817461004976,
            22320260930483,
            20220260960225,
            30228860745476,
            31146660779625,
            20228860818823,
            30219260890884,
            14423760919481,
            14422360933022,
            30222360934170,
            223621160664358,
            271663360664300,
            20222360896772,
            20228860987273,
            20231560883565,
            20220260984093,
            30220260843006,
            14420260858875,
            27122360935348,
            14428860969320,
            25120261035078
        ];
        if ($shipmentId) {
            $shipmentId = Shipment::whereIn('tracking_number', $shipmentId)->get();
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
