<?php

namespace App\Jobs;

use App\Http\Controllers\GeoCodesController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Shipment;
use App\Models\GeoCodeApiCount;
use App\Models\GeoCodesAssignDestinationHubs;
use App\Models\GeoCodesAssignSubSegments;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ShipmentGeoCodesTPL implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $shipment_ids;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shipment_ids)
    {
        $this->queue = 'shipment_geo_codes_tpl';
        $this->shipment_ids = $shipment_ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        return true;
        // try {
        //     $shipment_ids = $this->shipment_ids;

        //     // 1) Check if geocodes enabled
        //     $geo_codes_enabled = GlobalSettings::where('type','geo_codes_enabled')->value('setting_value');
        //     if (!$geo_codes_enabled) {
        //         return;
        //     }

        //     // 2) Get overall limit
        //     $geo_codes_max_limit = GlobalSettings::where('type','geo_codes_max_limit')->value('setting_value');

        //     // 3) Get current usage count
        //     $api_count = GeoCodeApiCount::value('api_count');

        //     if (!$api_count) $api_count = 0;

        //     // 4) Remaining API hits available
        //     $remaining_hits = $geo_codes_max_limit - $api_count;

        //     // If no API hits remaining → DO NOT PROCESS
        //     if ($remaining_hits <= 0) {
        //         Log::channel('cronJobLog')->warning("GeoCodesAPI: LIMIT reached. No more API hits allowed.");
        //         return;
        //     }

        //     // 5) Assigned hub + segment filters
        //     $assigned_destination = GeoCodesAssignDestinationHubs::pluck('destination_hub_id')->toArray();
        //     $assigned_sub_segments = GeoCodesAssignSubSegments::pluck('sub_segment_id')->toArray();


        //     if (empty($assigned_destination) || empty($assigned_sub_segments)) {
        //         Log::channel('cronJobLog')->warning("GeoCodesAPI: Skipped — Destination hubs or sub-segments not assigned.");
        //         return;
        //     }

        //     $shipments = Shipment::with(['consignee_city','user'])
        //         ->whereIn('id', $shipment_ids)
        //         ->get();

        //     $shipment_id_array = [];

        //     foreach ($shipments as $shipment) {

        //         $hub_id = optional($shipment->consignee_city)->hub_id;
        //         $seg_id = optional($shipment->user)->sub_segment_id;

        //         if (in_array($hub_id, $assigned_destination) && in_array($seg_id, $assigned_sub_segments)) {
        //             $shipment_id_array[] = $shipment->id;
        //         }
        //     }
        //     // LIMIT APPLY HERE
        //     // if shipments > remaining API hits → trim array
        //     if (count($shipment_id_array) > $remaining_hits) {
        //         $shipment_id_array = array_slice($shipment_id_array, 0, $remaining_hits);
        //     }

        //     if (!empty($shipment_id_array)) {
        //         GeoCodesController::tpl_geo_codes($shipment_id_array);
        //     }

        // } catch (\Throwable $th) {
        //     Log::channel('cronJobLog')->error("geo_codes_job: ".$th->getMessage(), [
        //         'line' => $th->getLine(),
        //         'file' => $th->getFile(),
        //     ]);
        // }
    }
}
