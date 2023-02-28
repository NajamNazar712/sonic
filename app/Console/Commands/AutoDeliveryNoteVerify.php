<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\GlobalSettingsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class AutoDeliveryNoteVerify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:deliverynoteverification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Delivery Note Verification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = Carbon::yesterday();
        $excluded_hubs = array();
        $setting = GlobalSettings::where('type', 'delivery_note_auto_verification');

        if($setting->exists()){
            $setting = $setting->first();

            if($setting->setting_value){
                $excluded_hubs_setting = GlobalSettings::where('type', 'delivery_note_auto_verification_exclude_hubs');

                if ($excluded_hubs_setting->exists()) {
                    $excluded_hubs_setting = $excluded_hubs_setting->first();
                    $excluded_hubs = array_map('intval', explode(',', $excluded_hubs_setting->text));
                }

                $delivery_notes = DeliveryNote::whereDate('created_at', $date)->where(['status' => 0, 'pending_status' => 1])->whereNotIn('hub_id', $excluded_hubs);

                if($delivery_notes->exists()){
                    $delivery_notes = $delivery_notes->get();
                    foreach ($delivery_notes as $delivery_note) {

                        $shipment_ids = $delivery_note->delivery_note_shipments()->pluck('shipment_id');
                        $shipments = Shipment::whereIn('id', $shipment_ids)->whereIn('booking_type_id', [2,3,4,5]);
                        if($shipments->exists()){
                            continue;
                        }
                        $delivered_shipments = Shipment::whereIn('id', $shipment_ids)->where('shipper_status_id', 14);
                        if($delivered_shipments->exists()){
                            continue;
                        }

                        $shipments = Shipment::whereIn('id', $shipment_ids)->where('shipper_status_id', '!=',14)->select('id', 'shipper_status_id')->get();

                        foreach ($shipments as $shipment){
                                $shipments_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->latest('id')->first();

                                ShipmentsJourneyController::add($shipments_journey->shipment_id, $shipments_journey->shipper_status_id, $shipments_journey->consignee_status_id, $shipments_journey->status_reason_id, $shipments_journey->remarks, $shipments_journey->user_id, 346, $shipments_journey->reference_1_id, $shipments_journey->reference_2_id, 1, $shipments_journey->received_or_refused_by, $shipments_journey->rider_id);
                        }
                        $current_time = Carbon::now();
                        DeliveryNote::where('id', $delivery_note->id)->update(['verified_by' => 346, 'status' => 1, 'last_updated_at' => $current_time, 'status_verified_at' => $current_time]);
                    }
                }
            }
        }
    }
}
