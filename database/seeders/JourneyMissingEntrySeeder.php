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
            20220263032999,
            20220263061078,
            20220263065679,
            20220263032239,
            20220263036356,
            20220263036387,
            20220263045999,
            22322363032372,
            22322363032394,
            22322363032748,
            20220263043131,
            22315863042487,
            20220263065778,
            20220262948246,
            20220262948355,
            20220263053006,
            22322363039871,
            22325163036182,
            22315863036184,
            20220263056498,
            20220262993221,
            14422363039839,
            20220263032690,
            20220263051215,
            26722363048821,
            20220263031150,
            14422363053147,
            20220263031270,
            20220263031480,
            20220263041319,
            14422363028556,
            19514463045845,
            14414463020458,
            14414463017981,
            14414463017980,
            14422363019696,
            14414463018018,
            23717463030789,
            23728862999115,
            20220263019799,
            20220263022862,
            20220263007963,
            20220263023566,
            20220263020157,
            20225162899293,
            20217262950909,
            20225163000624,
            22317462957785,
            22325163027251,
            22327163001874,
            22325162999247,
            20214462977219,
            20220263001712,
            20220262999295,
            20220262729176,
            20220262777176,
            20220262999850,
            20220262967635,
            20214462967654,
            20220262998799,
            20220262948452,
            20220262948467,
            20220262948381,
            25125163024219,
            20214462948317,
            20220263001109,
            22322362999305,
            22322362999174,
            22327162999903,
            20220263016089,
            28817463015773,
            22322363015147,
            14417463023203,
            17220263004690,
            20220263018805,
            20225162976005,
            20220263004591,
            20225162722197,
            26722363025054,
            20220263016087,
            15815863025384,
            15825163025569,
            25122363015170,
            17422363007014,
            38317463003056,
            22328863020568,
            20217263007619,
            22325163000989,
            22327163001187,
            17425163009789,
            28822363010653,
            27122362943760,
            19525163002143,
            14414462998316,
            27122363013104,
            14414462995689,
            23722362968667,
            23722362968754,
            23725162968722,
            16117462999691,
            20220262973373,
            20220262970147,
            20222362927862,
            20228362969959,
            20220262938776,
            20234462950118,
            20220262919674,
            20222362992222,
            20222362977076,
            20222362989986,
            20222362984284,
            20220262993963,
            20225162967204,
            20228862877411,
            22317262997107,
            22322362996911,
            22322362996948,
            22322362997000,
            22320262950188,
            22320262970505,
            22320262994590,
            22322362987439,
            22322362985676,
            20217462975627,
            22320262994586,
            22322362970949,
            22317462968278,
            22320262988991,
            22317262988386,
            22320262992132,
            22320262983929,
            22320262989498,
            20220262970750,
            20215862986274,
            20220262986271,
            22317262993345,
            20217262986251,
            20225162986286,
            22317462972326,
            20220262983453,
            20217262986625,
            20222362987299,
            20222362987006,
            22320262993681,
            20222362985530,
            20227162993565,
            22320262986148,
            22322362968949,
            22325162968109,
            22315862991852,
            22320262969806,
            22325162968103,
            22322362969793,
            22322362969927,
            22322362968965,
            22322362968118,
            22320262982466,
            22314462972044,
            14422362986562,
            14420262986568,
            14414462987003,
            14428862987414,
            14422362986461,
            22314462971729,
            22314462972024,
            20220262976403,
            20220262976502,
            20220262976303,
            20220262976166,
            20220262976123,
            20220262976252,
            20220262976414,
            20220262976407,
            20220262976042,
            20220262976439,
            20220262976426,
            20220262976318,
            20220262976422,
            20220262976294,
            20220262976298,
            14420262981813,
            14422362988124,
            20220262976048,
            20220262976069,
            20220262976170,
            20220262976128,
            20220262976358,
            20220262976458,
            20220262976418,
            20220262976220,
            20220262976231,
            20220262976435,
            20220262976211,
            20220262976470,
            20220262976054,
            20220262976174,
            20220262976086,
            20220262976200,
            20220262976328,
            20220262976507,
            20220262976349,
            20220262976437,
            20220262976063,
            14425162991377,
            20220262976049,
            20220262976379,
            25115862985762,
            14420262989692,
            14422362983677,
            14422362981545,
            28820262897513,
            20214462974727,
            14422362982904,
            14422362987720,
            14417462922474,
            20217462984386,
            25122362990035,
            14420262974066,
            17420262986258,
            26722362987252,
            20220262983563,
            30231162970703,
            20220262967882,
            11020262863399,
            15817462982441,
            27122362910460,
            27122362896274,
            30225162981909,
            28420262983805,
            28422362983795,
            14427162946258,
            14428862957164,
            10125162985695,
            12520262971902,
            27122362959251,
            20220262415901,
            20220262917218,
            20228862936021,
            20222362947856,
            20215862958284,
            20214462958250,
            20215862923798,
            20225562947008,
            20214462878038,
            20214462959096,
            20222362953274,
            20222362948631,
            20228862948724,
            20217462695779,
            20222362948624,
            20222362695591,
            20227162910676,
            20228862958544,
            20222362934710,
            20222362936071,
            22320262960287,
            22320262693606,
            20214462905444,
            20233662900870,
            20225162919045,
            20220262920447,
            20222362934195,
            22320262900836,
            22320262929777,
            20222362954429,
            22320262934878,
            22322362935358,
            22328862959905,
            22322362935628,
            20222362932183,
            20228862947142,
            25125162960317,
            25112262960316,
            25114462960313,
            25120262960284,
            20217462965468,
            22322362934967,
            22322362934929,
            22320262935738,
            22317462934859,
            20212262954421,
            25127162957714,
            14420262960379,
            22320262962703,
            20222362954940,
            20228362927895,
            14420262952210,
            14422362967447,
            20222362960025,
            22320262952561,
            22320262921569,
            14415862954733,
            14431162953717,
            14420262953707,
            14420262952237,
            14420262951030,
            14425162949573,
            17422362960597,
            25120262957722,
            14420262958348,
            22320262958812,
            14428862944561,
            14420262944448,
            14420262944444,
            25127162916133,
            26715862938794,
            14420262946996,
            14422362935522,
            20220262952374,
            30222362885764,
            30220262946531,
            30215862946518,
            30220262953527,
            30214462943636,
            30220262949419,
            30220262944247,
            30220262946535,
            30220262946525,
            30220262906416,
            27120262799304,
            28414462950776,
            28420262943372,
            28415862942941,
            17420262888951,
            19520262950136,
            17420262880174,
            34227162925434,
            34222362908922,
            34234062936299,
            30220262942727,
            30220262942444,
            20217462943042,
            20228362934881,
            27114462939618,
            12522362939262,
            20214462891121,
            22320262923304,
            20220262881549,
            20228862894233,
            20222362908293,
            20222362896584,
            20222362926684,
            20225162926672,
            20222362916680,
            20214462890503,
            22320262927036,
            22314462897155,
            22320262882652,
            20217462835965,
            20222362888089,
            20222362877247,
            22320262883862,
            22320262913866,
            20220262920610,
            20225162891116,
            20215762906367,
            20222362894390,
            20214462894247,
            20217462893577,
            20226762887683,
            20228862867459,
            20222362855035,
            20225162894337,
            20222362890123,
            20214462894189,
            20228862888154,
            20220262923108,
            20222362878432,
            20228862878231,
            20228862695395,
            20220262695573,
            20220262695476,
            20222362695443,
            22320262879158,
            22328362881579,
            20222362911872,
            22320262887674,
            22328362886023,
            22322362890111,
            22320262888237,
            223138162877214,
            22328862893696,
            25120262919184,
            25122362927435,
            25122362927464,
            25122362927454,
            14420262896771,
            22320262920388,
            22320262920398,
            22320262920739,
            14420262884493,
            14420262896978,
            14422362896475,
            14420262899942,
            20222362920773,
            20222362901265,
            22320262921636,
            22320262921661,
            20222362929283,
            14428862896523,
            20214462920590,
            22328362887340,
            14422362921206,
            14422362917465,
            20228362914137,
            14420262921233,
            15820262927059,
            14417262921617,
            20220262918493,
            20222362903350,
            14422362916057,
            20220262912820,
            14420262921119,
            20220262728498,
            28817462894134,
            14420262919869,
            14422362882400,
            20222362728493,
            20222362896309,
            22320262898755,
            22317262903213,
            22320262909211,
            14422362887105,
            17420262896279,
            17420262894906,
            20228862915246,
            14420262884656,
            38315862912864,
            38320262866489,
            17420262893371,
            14420262909153,
            14420262884438,
            14422362883439,
            14422362883288,
            22320262885547,
            14420262884585,
            11014462907344,
            14420262884040,
            14420262884613,
            17420262921853,
            22320262890864,
            29328862881294,
            29328362881267,
            25115862914330,
            14420262883978,
            14420262883981,
            30220262852299,
            30220262906819,
            20220262877271,
            13520262890166,
            30214462881978,
            30220262906848,
            24120262849183,
            34028362895374,
            27120262881659,
            23722362835609,
            22328362876861,
            22320262875318,
            28428362882923,
            20215862880808,
            20222362873192,
            20214462865008,
            20225162845511,
            20228462835999,
            20222362845441,
            202661262848266,
            20217262872033,
            20222362871954,
            20220262849378,
            22322362874135,
            22320262872780,
            20222362818082,
            14417462854870,
            20220262851368,
            20222362852980,
            22350562783522,
            14420262866226,
            25125562843854,
            25117262873306,
            15822362874379,
            28822362845567,
            30220262828624,
            14420262850159,
            22320262874006,
            20220262853888,
            20228362844516,
            20220262819689,
            20217462784760,
            20225162835459,
            20228362819631,
            20220262842539,
            20250362839012,
            20225162762130,
            22328362819395,
            20220262824131,
            14428862838996,
            25120262829300,
            27125162772243,
            14422362823125,
            14431162823079,
            14422362823588,
            30220262823636,
            30220262827022,
            20222362805999,
            20222362815273,
            20250362815351,
            20222362786690,
            20218662765054,
            20225162762935,
            20225162762544,
            20225162762862,
            20222362797974,
            22320262785407,
            22320262756271,
            22320262766080,
            22320262787743,
            14420262801223,
            20228862755089,
            20222362744890,
            14420262809585,
            20222362791955,
            15820262815884,
            28820262783345,
            30220262789822,
            30222362767347,
            17417462750156,
            17428862787150,
            20228362746183,
            22328362763429,
            22352562755064,
            22320262666814,
            22317462746209,
            22317262750978,
            22328362747734,
            22328862746740,
            30215862765598,
            20222362713829,
            20228862713901,
            20222362718093,
            20222362717550,
            14422362730563,
            14415862725166,
            14428362735489,
            17420262733821,
            202341062691932,
            20228462639410,
            20216562689752,
            20217462689730,
            22317462645030,
            223200662666781,
            25116562710719,
            14428362628986,
            17420262694619,
            25120262621442,
            20222362575261,
            30217462594820,
            20215262539055,
            14417262562888,
            20220262517884,
            20222362455163,
            22328362457183,
            20217462476395,
            20222362360241,
            17420262440869,
            20217462229819
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
