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
  25125161825285,22322361829514,22322361860132,22322361866686,22315861872033,27128861859306,27128861823445,20220261792444,20220261785476,20217261808039,20226761808020,20220261816239,20225161808056,20220261776951,20222361781357,20220261778260,20217261808017,20222361805332,20222361779144,20220261785463,20225161786355,20225161822029,20228861619579,20220261816102,22322361781111,22322361814318,22322361781082,20220261759479,14425161812672,20220261784866,20220261707311,20225161570963,20222361571113,20217261784824,11025161762478,22322361814663,22322361814531,25122361779546,22317461794848,22314461803730,14422361812368,25125161788184,14422361803150,14422361805652,14414461812107,14422361812080,22328861782168,22317461786692,22317461782716,14422361812993,14422361803813,14422361815993,22325161816811,22322361771961,22317461814564,14422361805646,20214461801949,20220261741057,14414461812397,22314461806499,22322361782804,22317461787205,22317461783866,22322361782741,22322361783212,22322361782730,22350561806727,22322361783188,22322361783075,22322361782713,14422361815907,14422361818925,20220261782546,14422361789462,22322361763089,22322361814942,20225161609510,22314461766630,22325161784005,14428861807205,26717461803334,26722361803427,22325161797338,25127161792719,25114461795690,20220261513000,20220261779582,29322361783005,29322361783235,14422361814585,14417461815378,38314461762445,17417461785321,28822361793135,28822361799165,22322361733952,20220261784284,17422361787715,31122361764847,20222161778347,30222361769739,34025161747388,20220261775179,20225161775125,20228861775172,20217461779412,20225161749246,20222361754348,20226761749278,20220261753107,20222361576465,20228861775280,20215861777989,20225161576134,20225161778005,20222361729885,20225161779585,20214461733498,20220261739147,20225161763484,20225161775228,20217261728242,20217461765406,20225161761044,22320261750720,22331161750781,20225161778732,20222361638197,20214461769537,20222361747209,20222361773115,22328861750719,20217261772534,22317261772003,22320261773439,22320261764589,20220261687096,25127161756385,25120261748153,25122361756391,25117261773420,25122361759784,25122361748167,22328861768736,25117261756995,25120261757010,25122361775681,20222361764825,20222361771068,22314461753008,22322361773184,22320261754594,25122361734773,25122361734787,25122361761630,22328861754606,20222361772257,20222361772247,20228361718034,20225161760272,20228361768253,20228361768273,20220261760774,20220261746986,20220261745003,20220261739878,20220261726939,20222361753741,20220261753739,20220261724016,20220261723125,20220261717050,20220261701948,20220261699213,20220261699250,20217461729695,20217461713922,22322361770625,22322361770615,20217461711536,20214461753524,20215861599149,20215861729680,20220261756202,22325161545093,20225161714858,20220261756201,20225161714879,22317461758384,20225161724938,20222361699174,20228861729385,20222361736570,20225161735135,20222361727836,20222361727834,20228861662917,20222361736957,20222361672057,20222361711942,20222361735885,20222361730232,28822361748241,25115861728771,20222361559824,20222361722444,20228861694556,20220261705087,20222361720681,20228861723635,20220261719933,17425161765412,25122361772877,20222361721707,20228861746523,20222361724646,20228361722469,29314461751085,29322361751326,20228861744176,20222361482105,20222361482168,30231161761118,30220261750610,30231161760852,20222361727827,30217261763377,30222361761243,30225161753438,27120261756930,31817261765007,30220261746819,30233661761511,20220261724749,20225161759913,20214461745802,20220261743215,15922361759651,25120261746560,25122361717919,25122361728485,22310661749654,22328861721980,14428861745495,14428861739576,14420261739600,14428861741313,22328861724104,14428861740373,30222361656847,20220261730609,22317261748191,14422361740681,14428861740452,25117461678742,20214461707549,20222361707406,20228861707467,20228861707643,20222361707483,20214461707509,20222361707435,20228361707376,20222361707634,20217461707476,20222361707538,20215861707696,20228861707679,20222361707605,20220261736173,15817461743795,28320261746199,30225561698814,17420261724622,17422361738388,30225161732283,30222361701457,18625161566723,20228861671924,20222361660917,20222361720584,20222361692168,20217461713747,20222361692047,20222361700349,20215861684870,20222361719431,20222361719391,20222361699542,20222361713429,20222361676172,20222361715248,20222361611838,20222361694271,22320261681958,20222361713232,20222361695767,20222361706549,22322361707518,22320261699252,20222361693079,20222361694595,25122361708746,25120261678497,20222361706226,22320261719549,22313861688576,14420261708486,14417261703863,14420261703896,20222361707815,20228861707752,20220261708297,20217461707825,20220261707998,20222361707827,20228361707996,20214461708008,20222361707862,14420261703029,22322361714501,22322361670778,20217461707914,20220261708205,20220261708277,20222361707881,20222361707938,20228861707919,20222361707855,22320261655682,20222361696955,20225161690875,14420261710858,20222361711638,25120261695511,20228861593833,25128861703325,20222361682790,25122361713739,26718661686608,14422361707097,17420261710807,17420261711369,26720261702870,17428361705321,20228361675078,20220261680865,20217461694579,28320261698815,28828361701860,28820261701864,20220261689113,20220261677148,14420261695737,31517461688624,30225161684620,27120261595631,27122361652828,20220261683984,20217461644653,20215861609382,20228861621108,22320261646949,22317461646953,22322361648286,22320261582204,22320261645888,22320261646459,22320261582968,20214461571515,20222361571443,14420261679045,14422361672845,22315861643126,14422361675593,14417261669407,25120261675321,11020261576097,11020261589353,14420261669632,14422361672043,22328361679387,25120261652476,14420261669475,22320261608252,20222361627600,20222361653418,25120261678319,25120261659840,25120261659948,20217461656486,20222361571488,20228861571538,20222361571273,20222361571452,20220261648543,25131161680165,20220261653924,25120261642510,20220261675626,25120261642625,14420261671968,25120261642076,14422361659051,14422361659082,14422361636125,28828361660870,17420261655660,17420261649156,17420261668746,30220261640539,30225161655874,30220261657169,30228861641430,30222361656034,152991756635,34017461584132,20228361590446,20222361591340,20217461591350,20222361635535,20228861629217,20222361591204,20222361593806,20222361636952,20228861622943,20220261616828,20228861631708,20222361582396,22320261610937,20217261639295,22320261620234,22322361620246,22324361597744,20222361627395,20222361626679,22322361584934,22328361642435,22328361590593,14415861612444,22320261596243,20220261603641,20220261590188,17420261589332,30213861572206,30228861572819,28322361574948,26417261516923,20217461543050,20228861532293,20222361559018,22324361576358,25122361527741,20228861571594,14420261572948,20220261530787,20220261579307,28820261557700,30220261530481,30220261532142,30220261555965,30220261556001,20222361505394,20217261505913,20222361531091,22328861437937,22320261520142,17420261525609,20222361441380,20222361462086,29320261467516,20222361424315,20228361494449,22328861487732,20222361497669,20217461434165,14422361494698,14422361463205,20217461423244,20222361440930,20222361425129,20222361419780,20222361418943,20222361406547,20222361440008,20222361465523,20222361435060,20222361344370,20222361373965,20222361415929,20222361376721,20222361394010,17420261432165,30222361440226,30217461438127,20222361419264,22322361398460,20222361412955,22328861313005,14417461247543,26717460890261
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
