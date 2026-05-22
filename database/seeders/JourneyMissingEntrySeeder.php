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
            20220263146058,20220263100815,22325163150524,20220263137525,22322363107043,20220263140814,20220262862429,22331163108187,20220262864952,22331163107739,22325163123995,22325163021985,14414463107664,22315863110187,22328863101036,22327163101278,22325163067790,22322363068634,22325163068501,20233263091844,20222363086721,20220262862842,20220262863489,22322363075285,20220263070637,20220263048761,20220263065562,20220263066280,20220263067537,22320263021952,22320263078379,20217262977468,14422363064958,14422363097690,20222362947921,22317463066790,20222362947874,20227162947973,20217462947905,20220262947896,20220262947924,20217462948048,14422363088056,14422363091953,14425163086527,14422363088118,20222363075212,22331163070733,20220262993652,14422363095207,22322363067148,20220263073480,14417463091194,22320263072409,20228863075241,20222363078257,22322363065438,22322363065084,20217263068677,22325163066871,22315863065393,22322363066132,14425163087562,22322363066679,22328863066872,14426763090388,14428863087109,14422363090469,14422363090304,14422363092974,20217263082640,14431163072509,20222362989786,20217263074079,25122362997434,28822363084862,22314463070528,22322363074961,14414463091360,28817463086997,17420263071343,20220263088678,17428863062715,20220263075321,16122363068123,20214463049126,20227163050820,202341063042552,20220263051292,20217263038764,20228363036502,20225162998271,20222363052832,20220262972868,20222363056913,22328863025098,22327163030874,20228863049199,20228862806836,20222362777752,20220263056015,20217463055834,22320263057960,22320263059251,20225163041838,20214463041885,20227162948276,20225162948344,14420263055963,22320263064999,22320263064275,22317263049685,22317263061325,14422363053081,14425163056072,22328863042584,14420263064977,20222363044555,38334063038822,20214462968579,22317263036329,14420263007326,14417463043968,20220263057513,14417463032246,25122363062550,14425163054316,25117263056520,17420263054249,20220263030347,27122363044307,14420263019619,14422363021684,14420263017963,14422362965667,20222363043172,144139062992128,27125163046763,23725163000604,23722362999103,20220263023610,20222363011817,20222363023221,20222362950864,20222363017538,20222362950856,14428863021615,20227162977211,20222363001759,20228862977323,20222362979961,20228862777187,20228862728917,20222363026213,20222362948472,20228362948429,20222362948375,25127163024218,25120263021311,20222362948326,20215862948518,20215863016118,20222363014099,22320262998775,20222363026962,20222362986811,25128863024825,25128863024835,25117463024851,20231163016162,14428863021594,22328363000898,22320263013916,14417463015422,14422363011887,14425163007721,20222363014839,20228363012707,20222363027674,20222362988603,20228363012967,20228863017637,20222363017066,20222363015824,20222362997524,20222363015206,22320263007445,20228862872970,20222362721332,25120262879207,25120263015722,25120263015921,28820263016168,22327163007609,22328363007616,22317263001322,22328363001277,22328363001275,22328363001276,17417263020556,27125162999822,14427163019732,27120262953791,27120262971051,30220263001089,19517263017914,28422363008798,14425162967064,23720262968726,23725162968668,23720262968648,16114462999701,20217462962777,20228362967214,20217462990549,22320262996891,22328362967231,20220262957869,22328362995441,20222362992003,22328862972574,14417462965551,20220262976285,20220262976319,14417262989504,14420262989806,20222362981406,14420262989299,25122362987730,14420262982580,14417262973222,30228362959150,14417462977872,27120262978318,28414462982785,27125162954535,34020262956941,22328362935530,20233662934714,20217462966367,20222362948575,22331862960617,20250562934704,20222362933909,22320262929903,22320262961176,14417262955486,14415862953678,14422362962376,20220262952665,20228362960890,14428362958071,25128362952751,20220262933578,28422362950757,20233662919531,20213462912246,20220262865926,20220262878785,20220262901571,20217462894844,20217262695343,20217262695338,20228862695433,20222362695449,20220262873105,25115862927476,14427162902565,14420262915701,14420262900121,14422362915139,14422362926817,20222362922267,29320262877303,12520262893200,23720262835877,20246062845429,20222362639416,22317462866365,25117262871360,30235462836308,20222362837095,20222362771312,20218862794492,202341062694529,20218662782145,20228362720027,20222362645985,22320262654714,20226662539390
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
