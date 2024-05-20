<?php

use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\Webhook\ShipmentStatusWebhookController;
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

class JourneyMissingEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $shipmentId = [
            38330238029350, 38319637994893, 31530237882602, 31524137886238, 31514438055355, 31514438018327, 31514438013068, 27129338019593, 26728237984118, 25118837981839, 25114438066567, 25114438066416, 25114438045894, 25114438033286, 25114438032612, 22333637921700, 22333637902180, 22332138044443, 22330238065952, 22330238049570, 22330238026429, 22330238013924, 22330237994126, 22330237926824, 22329837942098, 22328237762219, 22327537973701, 22327537908420, 22327537878410, 22318337903486, 22314438083845, 22314438076437, 22314438076065, 22314438075134, 22314438071697, 22314438070370, 22314438068766, 22314438066848, 22314438063946, 22314438059394, 22314438057162, 22314438057155, 22314438054735, 22314438053847, 22314438052976, 22314438051020, 22314438049628, 22314438049586, 22314438049507, 22314438049019, 22314438042268, 22314438042102, 22314438031662, 22314438029392, 22314438029290, 22314438028773, 22314438024085, 22314438023505, 22314438020398, 22314438020376, 22314438017899, 22314438017531, 22314438017291, 22314438017101, 22314438015821, 22314438015590, 22314438013884, 22314438012620, 22314438011434, 22314438008806, 22314438007384, 22314438007331, 22314438006736, 22314438005589, 22314438001620, 22314438000552, 22314437996681, 22314437995123, 22314437994225, 22314437993604, 22314437975969, 22314437971616, 22314437971505, 22314437963897, 22314437958008, 22314437952678, 22314437951519, 22314437944315, 22314437944308, 22314437906069, 22314437869080, 22314437858154, 22312138039765, 22312138034281, 22311438025264, 20233637950363, 20232137989116, 20232137981023, 20232137823200, 20230238014973, 20230238000503, 20230237961949, 20230237954641, 20230237949203, 20230237937522, 20230237904904, 20230237900632, 20230237683541, 20229337942404, 20229337918374, 20229337918227, 20229337894209, 20229337893952, 20229337873872, 20229337842640, 20228237942554, 20224137948342, 20224137931009, 20222337784602, 20218837942064, 20218837941995, 20218837844401, 20214438022160, 20214438022070, 20214438014802, 20214438014487, 20214438014449, 20214438009839, 20214438008582, 20214438008530, 20214438007795, 20214438004481, 20214438003106, 20214438003042, 20214438002928, 20214438000137, 20214437998426, 20214437996213, 20214437989617, 20214437988819, 20214437985978, 20214437984509, 20214437983302, 20214437982940, 20214437981571, 20214437972636, 20214437971728, 20214437970744, 20214437968255, 20214437967337, 20214437966137, 20214437965506, 20214437964886, 20214437961583, 20214437959000, 20214437955525, 20214437955413, 20214437955277, 20214437954986, 20214437949689, 20214437948716, 20214437948015, 20214437943680, 20214437943041, 20214437940662, 20214437933691, 20214437933579, 20214437933109, 20214437927351, 20214437912044, 20214437908468, 20214437908317, 20214437890098, 20214437842304, 20214437833428, 20214437803220, 20214437802497, 20214437705383, 20213537869858, 20213537845658, 20213537841624, 20211737941821, 17414438022667, 17414438011462, 17412137706881, 15828238021378, 152991362633, 152991362452, 152991362383, 152991362331, 152991362314, 152991362018, 152991362006, 152991361787, 152991361785, 14440237973175, 14429337994667, 14429337989969, 14429337893392, 14418838028857, 14418838020006, 14418337972264, 14414438073064, 14414438063403, 14414438061058, 14414438059649, 14414438059641, 14414438059590, 12914437885577, 25114437212802, 22333637053580, 22314437235256, 22314437171278, 22314437166500, 22314437117914, 22314437115566, 22314437105701, 22314437063312, 20233637036756, 20214437152264, 20214437145965, 20214437145294, 20214437119497, 20214437108983, 20214437101598, 20214436977117, 17414437172191
        ];
        if ($shipmentId) {
            $shipmentId = Shipment::whereIn('tracking_number', $shipmentId)->get();

            foreach ($shipmentId as $shipment) {

                if ($shipment->shipper_status_id === 5) {
                    // $shipment->created_at = $shipment->updated_at;
                    $shipment->updated_at = $shipment->updated_at;
                    $shipment->shipper_status_id = 14;
                    $shipment->consignee_status_id = 14;
                    $shipment->save();
                }

                if ($shipment->shipper_status_id === 14) {
                    $charges = $shipment->weight_charges + $shipment->fuel_surcharge;
                    $deliveryNoteId = DeliveryNoteShipment::where('shipment_id', $shipment->id)->first();
                    $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                    $zone = Zone::find($shipment->pickup_address->city->zone_id);
                    if ($zone->gst == '0.16') {
                        $addgst = 16.0;
                    } elseif ($zone->gst == '0.13') {
                        $addgst = 13.0;
                    } else {
                        $addgst = $zone->gst;
                    }

                    $gst = ROUND($charges * $zone->gst, 2, PHP_ROUND_HALF_DOWN);
                    $payable = $shipment->amount - $gst;
                    if ($pending_payment->exists()) {
                        $pending_payment = $pending_payment->first();

                        $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
                        $pending_payment->delivered_shipments = $pending_payment->delivered_shipments + 1;

                        // $pending_payment->save();
                    } else {
                        $pending_payment = new PendingPayment();

                        $pending_payment->user_id = $shipment->user_id;
                        $pending_payment->total_shipments = 1;
                        $pending_payment->delivered_shipments = 1;
                        $pending_payment->returned_shipments = 0;
                        $pending_payment->adjusted_shipments = 0;

                        $pending_payment->save();
                    }
                    $pending_payment_shipment = new PendingPaymentShipment();
                    $pending_payment_shipment->pending_payment_id = $pending_payment->id;
                    $pending_payment_shipment->created_at = $shipment->created_at;
                    $pending_payment_shipment->updated_at = $shipment->created_at;
                    $pending_payment_shipment->shipment_id = $shipment->id;
                    $pending_payment_shipment->type = 0;
                    $pending_payment_shipment->amount = $shipment->amount;
                    $pending_payment_shipment->charges = $charges;
                    $pending_payment_shipment->gst = $addgst;
                    $pending_payment_shipment->payable = $payable;
                    $pending_payment_shipment->save();

                    // $deliveryNoteId->status = 6;
                    // $deliveryNoteId->save();
                    // ShipmentsJourneyController::add($shipment->id, $shipment->shipper_status_id, $shipment->shipper_status_id, NULL, NULL, $shipment->user_id, NULL, $deliveryNoteId->delivery_note_id);

                }
                $verification = 1;
                $shipment_journey = new ShipmentsJourney();

                $shipment_journey->shipment_id = $shipment->id;
                $shipment_journey->verification = $verification;
                $shipment_journey->created_at = $shipment->updated_at;
                $shipment_journey->updated_at = $shipment->updated_at;
                $shipment_journey->shipper_status_id = $shipment->shipper_status_id;
                $shipment_journey->consignee_status_id = $shipment->consignee_status_id;
                $shipment_journey->status_reason_id = null;
                $shipment_journey->remarks =  null;
                $shipment_journey->user_id = $shipment->user_id;
                $shipment_journey->admin_id = null;
                $shipment_journey->rider_id = null;
                $shipment_journey->reference_1_id = $deliveryNoteId->delivery_note_id;
                $shipment_journey->reference_2_id = null;
                $shipment_journey->received_or_refused_by = null;
                $shipment_journey->relation = null;
                $shipment_journey->cnic = null;
                $shipment_journey->save();
                if ($shipment->shipper_status_id != 1) {
                    ShipmentStatusWebhookController::webhook_subscription($shipment->id, $shipment->shipper_status_id, null);
                }
                if ($verification == 1) {
                    $shipment_subscription = ShipperShipmentsSubscription::where('shipment_id', $shipment->id);
                    if ($shipment_subscription->exists()) {
                        $shipment_subscription = $shipment_subscription->first();
                        NotificationsController::app_notification(7, $shipment_subscription->shipper_id, 3, $shipment->id, $shipment->shipper_status_id);
                    }
                    $consignee_user = ConsigneeUser::where('phone_number_1', $shipment->consignee_phone_number_1)
                        ->orwhere('phone_number_2', $shipment->consignee_phone_number_1);
                    if ($consignee_user->exists()) {
                        $consignee_user = $consignee_user->first();
                        $consignee_id = $consignee_user->id;
                        NotificationsController::app_notification(8, $consignee_id, 4, $shipment->id, $shipment->shipper_status_id);
                    }
                    ShipperShipmentsSubscription::where('shipment_id', $shipment->id)->delete();
                }
            }
        }
    }
}