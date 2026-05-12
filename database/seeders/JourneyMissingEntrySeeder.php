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
            20220262805028,
            22325162718634,
            14415862799024,
            25150562799263,
            30250562764283,
            22331162731555,
            20220262722418,
            22325162773503,
            22327162773666,
            20220262761620,
            20220262764730,
            22314462778160,
            20220262756996,
            20220262763186,
            20220262763085,
            20220262763148,
            22317462778794,
            22317462774083,
            22314462745144,
            22322362746188,
            22325162747155,
            22322362746918,
            20220262750774,
            17417462751883,
            28828862738567,
            28817462753730,
            17422362778071,
            17415862777920,
            20220262743423,
            20220262761683,
            20220262745929,
            20220262757020,
            22314462748044,
            22314462748129,
            20220262762093,
            20220262768702,
            20220262737017,
            20220262713905,
            20220262713883,
            20220262714290,
            20220262713149,
            20217462726280,
            20220262643439,
            20217262717650,
            20225162726057,
            22317462619791,
            22328862642840,
            20220262696290,
            20217462695690,
            20217462696000,
            20214462695918,
            20217462673776,
            20220262678331,
            20220262727290,
            20228862735347,
            20217462668809,
            20217262695899,
            20217262695888,
            22325162738721,
            22328862685267,
            22317462689025,
            20227162705884,
            14418662745199,
            14422362739628,
            22320262714029,
            14414462724064,
            14420262727036,
            22320262715533,
            31517462733752,
            20215862734724,
            20220262738000,
            38320262731943,
            38314462733670,
            38317462729711,
            14422362705566,
            20214462714923,
            25122362739550,
            38325162735650,
            14420262729608,
            30220262726660,
            30220262700543,
            30228862713160,
            28420262718225,
            28428862718228,
            28428862718258,
            28420262719779,
            27122362721434,
            20225162703371,
            23722362714072,
            23715862714347,
            23720262714274,
            20228362660873,
            20220262567886,
            20217262697457,
            20228862699025,
            20228362661197,
            20228362667584,
            20214462669992,
            20222362704337,
            20217462609663,
            20222362661157,
            20214462669413,
            20222362669114,
            20217462660749,
            22320262693601,
            20222362672214,
            20220262693073,
            29317462700730,
            20225162701188,
            25127162664233,
            20220262701620,
            22320262694646,
            25128862703909,
            22322362709515,
            20220262678828,
            22320262697067,
            22328362702258,
            14410662703725,
            14420262703529,
            22320262667738,
            14420262664737,
            14420262664703,
            14422362681733,
            14420262684414,
            14428862697148,
            14422362709322,
            14420262664636,
            14428862685724,
            20217462684790,
            20217462553650,
            25117462700565,
            14422362709318,
            20222362698810,
            22320262681814,
            20225162682183,
            20214462682226,
            22320262670396,
            22320262670840,
            20217462591213,
            25128862693527,
            38320262702292,
            38320262698818,
            14420262673688,
            14420262674077,
            28820262691408,
            20227162692681,
            14422362677851,
            20217262669546,
            17427162676682,
            17420262674958,
            17420262621635,
            22320262670335,
            22328362666426,
            30225162671476,
            17420262670005,
            28420262679413,
            30217462675911,
            14420262665645,
            14422362664653,
            23717262666914,
            23717262667135,
            23717262668121,
            23720262666916,
            23722362668125,
            23720262668127,
            20214462644361,
            20217462617913,
            20228862610642,
            20228362641254,
            20222362654137,
            20231162644309,
            20222362616265,
            20217462624528,
            20222362640696,
            20221062645900,
            14446662629305,
            22320262648909,
            22317462655840,
            22320262610648,
            22328862578010,
            14417462640182,
            20217462624536,
            22328362624162,
            14410162654838,
            20228862629265,
            20217462643471,
            22314462623370,
            20217462628209,
            20220262597819,
            14422362654820,
            22320262644717,
            20222362656735,
            20222362641442,
            28846662653136,
            22326462621864,
            14420262653561,
            15817462657424,
            25122362620359,
            20220262634230,
            25122362636583,
            22320262637530,
            30220262637387,
            31520262628947,
            25128362621337,
            25120262621373,
            30217262622899,
            30220262638397,
            30220262639105,
            27120262638944,
            28422362634269,
            23728862622534,
            23722362622164,
            23720262622500,
            23731962622140,
            20222362568063,
            20222362567930,
            20222362566606,
            20233962536426,
            20217462335685,
            20222362336494,
            20231162536413,
            20220262336287,
            20217462566702,
            22317462576479,
            223661262578460,
            20222362579894,
            20217462582194,
            22328362586281,
            20217462587912,
            22328862576338,
            14428862595149,
            22320262574081,
            17420262579638,
            14420262580603,
            27120262573169,
            16128862581324,
            14420262541723,
            20222362518772,
            14420262520972,
            25120262458761,
            27120262512668,
            20218162491760,
            22317462485846,
            22320262457186,
            14417462473313,
            14422362485433,
            20228862471241,
            20217462421719,
            20246062330539,
            14417462312949,
            14417462313027,
            20231162000788
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
