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
            31528861143536,
            20222361098193,
            20220261208896,
            20222361130959,
            14417461161059,
            28820261049290,
            20217461089959,
            20220261112982,
            20222361141091,
            22320260622678,
            28420260627833,
            20220260744031,
            20220260882530,
            20220261156489,
            20220260791645,
            20220261153798,
            20222361086638,
            20220261246105,
            25120261124802,
            22322361205701,
            22320261089605,
            25128860822052,
            20220261015222,
            28417461058339,
            25120260619106,
            30220260846787,
            30228861144826,
            14428860786097,
            20220261156310,
            14422361158934,
            22320260708168,
            14422361197822,
            28822361209112,
            20222361130785,
            20220261135415,
            20220261232999,
            20225161124320,
            20220261167552,
            22320261162431,
            20220261130954,
            20222361137606,
            22322361057131,
            20222360936757,
            20220261093841,
            20220261173966,
            25117261085450,
            22320260839289,
            20220261202876,
            20220261092221,
            20220261156510,
            20225161133853,
            152991750374,
            22320261000789,
            20220261151710,
            30220261115680,
            22320261001833,
            14422361111480,
            22322361155594,
            31128861091489,
            22320261058155,
            30228860884548,
            30228861177344,
            20220261156670,
            20222361039606,
            20222361019387,
            22320261082002,
            20220261111872,
            14422361150427,
            20220261118225,
            20228860848780,
            20217461092838,
            20228861093068,
            22322361158182,
            20228861140595,
            22322361168227,
            22320261168363,
            14420261161782,
            20220261175007,
            34220261152444,
            25120260958827,
            20220261200126,
            20220261251552,
            20220261199721,
            22328860914004,
            20220261064981,
            20220261078733,
            20217460939858,
            22322360965938,
            20220261122317,
            20217461124181,
            38317461148387,
            22320261147720,
            20220261174088,
            22314461006993,
            20228860977630,
            14425561069636,
            25128861163955,
            20217460884252,
            20228861130094,
            14428861125732,
            22328861147765,
            34031161200532,
            20224460967243,
            20217461097272,
            20217460992243,
            14428861159359,
            14415861246480,
            14417461153877,
            22328361106691,
            20228361136902,
            14417461148530,
            22320260955980,
            20217461111068,
            20220261118052,
            22315861259287,
            20225161157487,
            22314461053961,
            20225161208588,
            28328861110751,
            22317461197951,
            20222361207244,
            22317461193246,
            20228861123004,
            22322361218228,
            20225161185658,
            20220261222679,
            25115861162714,
            20222361036545,
            20222361088426,
            20222361121232,
            25122361122896,
            17422361196190,
            14422361214089,
            20222361085545,
            22320261166067,
            20222361068715,
            20222361097054,
            20222361122810,
            20222361172065,
            30222361222188,
            14422361240935,
            20220261134499,
            20222361162903,
            20222361180984,
            20222361189587,
            22322361219271,
            20220261239696,
            20222361052179,
            20222361122298,
            22322361221196,
            22322361258505,
            22320261005638,
            30222361037091,
            17422361083379,
            20222361141931,
            22320261190083,
            25122361203394,
            20222361205347,
            20217261238522,
            25122361244109,
            20222361000414,
            20222361080732,
            22322361103804,
            20222361017483,
            22322361024347,
            22328861070812,
            20222361104193,
            30222361106693,
            22320261188097,
            20225161142382,
            25122361124549,
            20215861193864,
            20222361195035,
            22320261164628,
            20222361094803,
            20222361161075,
            20220261156586,
            25122361233211,
            22320261147811,
            20220261221722,
            20217460951272,
            20217460908756,
            22328861006432,
            20228361230649,
            30218661229304,
            20228861081462,
            20228860917503,
            20222361090797,
            22320260989421,
            30222361228751,
            30222361184901,
            14422361116731,
            14422361143739,
            20228861138761,
            20222361074393,
            22314461165680,
            23722360946874,
            25150561118489,
            30217461177358,
            14422361158441,
            17450561112342,
            28810460880410,
            20222361122367,
            17422361097100,
            30220261036476,
            20231161134504,
            30217261144755,
            20222361053257,
            20220261156841,
            20228361092106,
            20220261156486,
            20228861164405,
            20222361105083,
            20222360981532,
            20220261156416,
            20222361046021,
            30222360977815,
            20222361057631,
            14422361065916,
            28422361139145,
            20222361100804,
            30228360992398,
            22320261152345,
            25122361161734,
            14420261157588,
            20228860828067,
            22325161125861,
            30217461145253,
            20217460886820,
            30222361154587,
            28422361098453,
            20222361121812,
            20222361114600,
            22315861259171,
            20225161137227,
            25128861053375,
            20217461195080,
            20222361148176,
            20220261166769,
            14422361160137,
            22320261055379,
            14422361161044,
            14418661159936,
            20228861163544,
            20222361120489,
            30222361187675,
            14417461106411,
            20222361202444,
            30222361229098,
            20222361090977,
            22320261019331,
            30220261146335,
            20220261220449,
            20220261259313,
            22320261148709,
            20222361116222,
            22317461181927,
            20222361091044,
            25125561091674,
            22317461100606,
            20214461128031,
            30222361222158,
            20222361155723,
            30222361220974,
            22328861188451,
            22317461242475,
            20220261174026,
            22320261165501,
            20228861130244,
            22317461144612,
            14422361125349,
            25120261124285,
            15815861217877,
            20222361156002,
            30220261187678,
            20222361030293,
            20220261259048,
            20220261212395,
            22322361165530,
            22322361193283,
            20225161202262,
            25122361201783,
            20220261244189,
            20222361183220,
            12522361221669,
            46522361106357,
            20220261197270,
            30217461185504,
            20228860786992,
            30228861177348,
            20228860971745,
            22320261145712,
            14420261084871,
            20217261132310,
            20217261225056,
            20217460817832,
            30220260957862,
            20220261118091,
            14422361145131,
            20220261156301,
            22322361165023,
            20222361185975,
            20220260495811,
            20217460760176,
            20217460935656,
            22320260965956,
            20220260992075,
            20220261037992,
            20220261118057,
            20220261118279,
            20220261118248,
            20220261118278,
            20220261118194,
            20220261156557,
            20220261156535,
            20220261156530,
            20220261156866,
            20220261215639,
            20217461208634,
            20217461135057,
            20220261206132,
            25114461121162,
            20220261230076,
            22320261156724,
            20220261156471,
            20222361160449,
            22320261177941,
            14422361159567,
            20228860767346,
            20228860767685,
            22320261004114,
            30222361195209,
            30222361184903,
            28420261187352,
            20222361175286,
            22320261023483,
            20217261196680,
            20220261066111,
            20228860875084,
            19511961178885,
            22320261103898,
            20220261216702,
            20220261118160,
            30220261184877,
            20220261247796,
            22320261106717,
            14414461246198,
            22322361222245,
            22320260963499,
            20228861036004,
            20222360957992,
            22328361075849,
            20217261213205,
            20217261209609,
            14422361080899,
            20217261186140,
            20217261254110,
            20217261203914,
            20217261177330,
            20222361114690,
            27120260471942,
            20222360961144,
            30220261144810,
            14428861125571,
            22322361179087,
            30222361222340,
            20228861114736,
            30220261187812,
            25120261097087,
            20220261266149,
            29333661232465,
            22331161218843,
            20225161149841,
            20222361195002,
            25122361122845,
            20222361116111,
            20220261197323,
            30220261164487,
            22317461205703,
            20225160973154,
            20225161037929,
            30225161079516,
            22325161110906,
            20220261156436,
            30225161187783,
            30228861182641,
            20228861123003,
            25128861123578,
            22320261191800,
            20222361147798,
            20220261161085,
            14422361123527,
            22320261140487,
            20220261222357,
            14422361159320,
            22322361160109,
            20220261241445,
            20214461135113,
            20220260830461,
            30222360759603,
            22320261106291,
            25520261015079,
            22320261107281,
            20220261265821,
            20228860908757,
            30228860993582,
            46528860846739,
            25123860911922,
            27112960703396,
            30222361107163,
            20222360930000,
            30222360973550,
            20222360970779,
            17450561140153,
            20231161021563,
            20220261058188,
            22322361101581,
            20217460907754,
            22322361143756,
            22320260990376,
            30225161193698,
            20220261200433,
            20222361155149,
            20225161203218,
            30222361168964,
            22320261181530,
            25120261124794,
            20228861146791,
            27122361109311,
            25122361083677,
            20222361151227,
            14420261089232,
            20220261261959,
            20217261216665,
            22322361229414,
            20222361195109,
            22322360983922,
            22322361108379,
            22317261058602,
            22322361159265,
            30220260892106,
            22322361185755,
            20222361202313,
            22320261133546,
            14420261162698,
            20220261173521,
            20220261210612,
            22328360975197,
            14422361229909,
            20222361022536,
            22320261099534,
            20220261233875,
            22320261174469,
            30220260960598,
            20225161111162,
            22320261178181,
            20214461174833,
            20228861132161,
            20220261213944,
            22320261177874,
            20222361172063,
            20228861161818,
            28820261114718,
            20222361033597,
            20220261135029,
            26422361077326,
            20222361202001,
            17422361186850,
            20217261247815,
            20222361167470,
            20225161161793,
            22317261181136,
            20222361146398,
            14420261161132,
            20222361132541,
            20222361076498,
            20222361150902,
            20222361122251,
            20220261114651,
            22320261197444,
            20222361121314,
            20214461164210,
            20228861091617,
            25115861124542,
            20214461164229,
            25120261116129,
            20220261268536,
            22317261179808,
            25117261049579,
            22328860837364,
            20220261134981,
            20220261118187,
            20217260988633,
            20228861045926,
            22320261055227,
            22328861144856,
            20222361077208,
            22328861218256,
            20222360886131,
            20215860910051,
            20220261156543,
            20225161017488,
            22315861140422,
            20217461031916,
            30220261145395,
            15820261138407,
            20217261230253,
            14420261161100,
            22320261125472,
            14417261071845,
            20217261135302,
            20220261223655,
            22320260852479,
            30220261101127,
            14422361155968,
            22320261166273,
            22320261174465,
            20220261156385,
            20217461036542,
            20228861174456,
            14420261167842,
            22320260967311,
            14417461159925,
            22328360667031,
            20217261167124,
            20228861173995,
            25128361029852,
            20220261092412,
            30220260882095,
            30220261146509,
            20220261161901,
            20220261191988,
            20220261188500,
            14420261162104,
            14420261186978,
            17428861275425,
            20215860864040,
            20228861075185,
            20217461205338,
            14433661244941,
            20218260944335,
            22328861067323,
            20220261205745,
            22320261164467,
            14428861265696,
            28825161169929,
            22322361218820,
            20215861221154,
            20217460894121,
            20222361134084,
            20220261209157,
            20228361174870,
            30250561223088,
            34017461160013,
            20214461089198,
            20228360832831,
            20213860933367,
            15918661164958,
            22317261089149,
            22322361056285,
            30222361228538,
            22331161219249,
            25122361297623,
            20222360979645,
            20228861201803,
            20228861211440,
            22322361292703,
            20222360751896,
            20222361187313,
            30222361004400,
            14431161247540,
            25131161264795,
            30222361171955,
            30222361210616,
            22322361238727,
            20217460744015,
            17417461221373,
            20228860753120,
            14422360967678,
            30222360929692,
            20217460793274,
            30217461063334,
            20217461125759,
            22328861018295,
            20217461174485,
            20230260943892,
            20225161016817,
            20222360983529,
            20220261170236,
            25120261124899,
            14422361119703,
            20217460959281,
            22334061180192,
            14420261071991,
            20222361093837,
            20220261138722,
            20220261233151,
            20228861087015,
            22320260960705,
            14422361234688,
            29322361233113,
            22330260975222,
            30220261000586,
            29322361142853,
            30222361186827,
            20220261232984,
            20231160882544,
            20234260987316,
            20228361205311,
            25117461090983,
            22320261038120,
            22317461067790,
            22317461069712,
            29331561091962,
            20222361007289,
            20220861057321,
            20222361031557,
            22320261055680,
            20231560951530,
            22328860525731,
            22320261058796,
            46516361014453,
            22316361064364,
            30231761067995,
            25120261033593,
            20220260809474,
            22320260626839,
            20220260982780,
            22320261057847,
            13529960603854,
            22325760859091,
            13592960576764,
            20220261085780,
            20217461236236,
            22310361223183
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
