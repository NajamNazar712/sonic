<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Webhook\ShipmentStatusWebhookController;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\ConsigneeUser;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipperShipmentsSubscription;
use Illuminate\Console\Command;

class InsertDeliveryJourney extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delivery:insert 
                            {tracking_numbers : Comma-separated tracking numbers}
                            {delivery_note_id : Delivery Note ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert for the jounrey of delivery based on tracking number,';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $trackingNumbers = explode(',', $this->argument('tracking_numbers'));
        $deliveryNoteId  = $this->argument('delivery_note_id');


        $this->info("Processing Delivery journey insert...");
        $this->info("Tracking Numbers: " . implode(', ', $trackingNumbers));

        // Fetch Shipments
        $shipments = Shipment::whereIn('tracking_number', $trackingNumbers)
            ->where('shipper_status_id', 14)
            ->get();
        foreach ($shipments as $shipment) {
            $deliveryNoteId = DeliveryNoteShipment::where('shipment_id', $shipment->id)->latest()->first();
            $verification = 1;
            $shipment_journey = new ShipmentsJourney();
            $status_id = (!in_array($shipment->shipper_status_id, [14]) ? '14' : $shipment->shipper_status_id);
            $shipment_journey->shipment_id = $shipment->id;
            $shipment_journey->verification = $verification;
            $shipment_journey->created_at = $deliveryNoteId->updated_at ?? $shipment->updated_at;
            $shipment_journey->updated_at = $deliveryNoteId->updated_at ?? $shipment->updated_at;
            $shipment_journey->shipper_status_id = $status_id;
            $shipment_journey->consignee_status_id = $status_id;
            $shipment_journey->status_reason_id = null;
            $shipment_journey->city_id =  $shipment->destination_city_id;
            $shipment_journey->remarks =  null;
            $shipment_journey->user_id = null;
            $shipment_journey->admin_id = 346;
            $shipment_journey->rider_id = null;
            $shipment_journey->reference_1_id = null;
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
            $this->info("Inserted Delivery Journey for Shipment {$shipment->tracking_number}");
        }
        return Command::SUCCESS;
    }
}
