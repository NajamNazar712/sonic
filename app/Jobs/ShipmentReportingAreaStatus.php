<?php

namespace App\Jobs;

use App\ShipmentScanningJourneyAreaLog;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ShipmentReportingAreaStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $latest_area_log_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($latest_area_log_id)
    {
        $this->latest_area_log_id = $latest_area_log_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $areaLog = ShipmentScanningJourneyAreaLog::find($this->latest_area_log_id);
        
        $journeyLocation = $areaLog->shipment_scanning_journey ?? null;
        $cityArea = $areaLog->city_area->reporting_location ?? null ;
        if($journeyLocation && $cityArea){            
            $journeyLatitude = deg2rad(isset($journeyLocation->latitude) ?? 0.00);
            $journeyLongitude = deg2rad(isset($journeyLocation->longitude) ?? 0.00);
            $cityLatitude = deg2rad(isset($cityArea->lat) ?? 0.00 );
            $cityLongitude = deg2rad(isset($cityArea->long) ?? 0.00);
        
            $earthRadius = 6371; 
            $longitudeDelta = $journeyLongitude - $cityLongitude;
        
            $distance = round($earthRadius * acos(
                sin($cityLatitude) * sin($journeyLatitude) +
                cos($cityLatitude) * cos($journeyLatitude) * cos($longitudeDelta)
            ), 2);
    
            if ($distance <= $cityArea->radius) {
                $areaLog->location_status = 1;
                $areaLog->status = 1;
                $areaLog->save();
            }
        }
    }
}
