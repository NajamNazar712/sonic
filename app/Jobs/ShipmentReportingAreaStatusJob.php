<?php

namespace App\Jobs;

use App\Http\Models\City;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\ShipmentScanningJourneyAreaLog;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Models\ShipmentScanningJourney;

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
        
        if (!$shipmentScanning) {
            return;
        }
        
        $areaLogId = ShipmentScanningJourneyAreaLog::latest('id')->first()->id;
        $areaLog = ShipmentScanningJourneyAreaLog::find($areaLogId);
        
        $cityArea = $areaLog->city_area->reporting_location ?? City::where(['id' => $areaLog->hub_id, 'hub' => "1"])->first() ?? null;
        
        if ($shipmentScanning && $cityArea) {
            $latitude = $cityArea->lat ?? $cityArea->location_latitude ?? null;
            $longitude = $cityArea->long ?? $cityArea->location_longitude ?? null;
        
            if (isset($shipmentScanning->latitude, $shipmentScanning->longitude, $latitude, $longitude)) {
                $journeyLatitude = deg2rad($shipmentScanning->latitude);
                $journeyLongitude = deg2rad($shipmentScanning->longitude);
                $cityLatitude = deg2rad($latitude);
                $cityLongitude = deg2rad($longitude);
        
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
