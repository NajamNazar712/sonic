<?php

namespace App\Console\Commands;

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use Illuminate\Console\Command;

class shipmentInTransitjourney extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'journey:insertIntoTransit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $shipmentId = [
            18820253695214,
            18820253695558,
            18820253695566,
            18820253695581,
            18820253695597,
            18820253695609,
            18820253695616,
            18820253695619,
            18820253700503,
            18820253700509,
            18820253700514,
            18820253700563,
            18820253700568,
            18820253700572,
            18820253700578,
            18820253700584,
            18820253700587,
            18820253702454,
            18820253704618,
            18820253711455,
            18820253711468,
            18820253711474,
            18820253714994,
            18820253714998,
            18820253715181,
            18820253715190,
            18820253720270,
            18820253720954,
            18820253721009
        ];
        if ($shipmentId) {
            $shipmentId = Shipment::whereIn('tracking_number', $shipmentId)->get();
            echo count($shipmentId);
            foreach($shipmentId as $shipment){
                $cargo = CargoManifestBagShipments::where('shipment_id',$shipment->id)->first();
                $shipment->shipper_status_id = 3;
                $shipment->consignee_status_id = 3;
                $shipment->save();
                $shipment_journey = new ShipmentsJourney();
                $shipment_journey->shipment_id = $shipment->id;
                $shipment_journey->verification = 1;
                $shipment_journey->created_at = $cargo->bag->updated_at;
                $shipment_journey->updated_at = $cargo->bag->updated_at;
                $shipment_journey->shipper_status_id = 3;
                $shipment_journey->consignee_status_id = 3;
                $shipment_journey->status_reason_id = null;
                $shipment_journey->city_id =  $cargo->bag->origin_hub_id;
                $shipment_journey->remarks =  null;
                $shipment_journey->user_id = null;
                $shipment_journey->admin_id = 346;
                $shipment_journey->rider_id = null;
                $shipment_journey->reference_1_id = $cargo->bag->id;
                $shipment_journey->reference_2_id = null;
                $shipment_journey->received_or_refused_by = null;
                $shipment_journey->relation = null;
                $shipment_journey->cnic = null;
                $shipment_journey->save();
            }
            
        }
    }
}
