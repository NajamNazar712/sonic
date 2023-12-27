<?php

namespace App\Jobs;

use App\Http\Models\ShipmentScanningJourney;
use App\ShipmentScanningJourneyAreaLog;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ShipmentReportingAreaStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $latest_shipment_scanning_id;

    /**
     * Create a new job instance.
     *
     * @param mixed $latest_shipment_scanning_id
     * @return void
     */
    public function __construct($latest_shipment_scanning_id)
    {
        $this->latest_shipment_scanning_id = $latest_shipment_scanning_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $latest_shipment_scanning_id = $this->latest_shipment_scanning_id;
        $shipmentScanning = ShipmentScanningJourney::find($latest_shipment_scanning_id);
        $areaLogId = ShipmentScanningJourneyAreaLog::latest('id')->first()->id;
        $areaLog = ShipmentScanningJourneyAreaLog::find($areaLogId);

        if (!$shipmentScanning) {
            return;
        }

        $cityArea = $areaLog->city_area->reporting_location ?? null;

        if (isset($shipmentScanning, $cityArea)) {
            if (isset($shipmentScanning->latitude, $shipmentScanning->longitude, $cityArea->lat, $cityArea->long)) {
                $journeyLatitude = deg2rad($shipmentScanning->latitude);
                $journeyLongitude = deg2rad($shipmentScanning->longitude);
                $cityLatitude = deg2rad($cityArea->lat);
                $cityLongitude = deg2rad($cityArea->long);

                $earthRadius = 6371;
                $longitudeDelta = $journeyLongitude - $cityLongitude;

                $distance = round($earthRadius * acos(
                    sin($cityLatitude) * sin($journeyLatitude) +
                    cos($cityLatitude) * cos($journeyLatitude) * cos($longitudeDelta)
                ), 2);

                $distance = $distance * 1000;  // To Metre

                $areaLog->location_status = ($distance <= $cityArea->radius) ? 1 : 0;

            } else {
                $areaLog->location_status = 0;
            }
        } else {
            $areaLog->location_status = 0;
        }

        $areaLog->status = 1;
        $areaLog->save();
    }
}
