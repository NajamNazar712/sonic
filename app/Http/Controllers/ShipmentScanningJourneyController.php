<?php

namespace App\Http\Controllers;

use Session;
use Vectorface\Whip\Whip;
use Illuminate\Http\Request;
use App\Http\Models\Admin\Admin;
use App\Http\Models\BagScanningJourney;
use App\ShipmentScanningJourneyAreaLog;
use App\Jobs\ShipmentReportingAreaStatus;
use App\Http\Models\ShipmentScanningJourney;

class ShipmentScanningJourneyController extends Controller
{
    //User Types 4 -> Retail User
    // via 1 -> Web and via 2 -> App
    static public function add($shipment_id, $screen_location_id, $user_type, $admin_id = NULL, $user_id = NULL, $substitute_user_id = NULL, $piece_id = NULL, $updated_via = NULL ) {
        if(session('role_id') != 1){
            $add_scanning_history = new ShipmentScanningJourney();
            $add_scanning_history->shipment_id = $shipment_id;
            $add_scanning_history->screen_location_id = $screen_location_id;
            $add_scanning_history->user_type = $user_type;
            $add_scanning_history->admin_id = $admin_id;
            $add_scanning_history->user_id = $user_id;
            $add_scanning_history->substitute_user_id = $substitute_user_id;
            $add_scanning_history->updated_via = $updated_via; // updated_via 1 -> Web and updated_via 2 -> App
            // $add_scanning_history->piece_id = $piece_id;

            $whip = new Whip();
            $client_address = $whip->getValidIpAddress();

            if ($client_address != '') {
                $add_scanning_history->ip_address = $client_address;
            }

            if (Session::has('latitude') && Session::has('longitude')) {
                $add_scanning_history->latitude = session('latitude');
                $add_scanning_history->longitude = session('longitude');
            }

            $add_scanning_history->save();

            $add_scanning_history_area_log = new ShipmentScanningJourneyAreaLog();
            $add_scanning_history_area_log->shipment_id = $shipment_id;
            $add_scanning_history_area_log->shipment_scanning_journey_id = ShipmentScanningJourney::latest('id')->first()->id ?? 1;
            $add_scanning_history_area_log->hub_id = Admin::find(session('id'))->default_hub_id;
            $add_scanning_history_area_log->area_id = Admin::find(session('id'))->area_id;
            $add_scanning_history_area_log->admin_id = Admin::find(session('id'))->id;
            $add_scanning_history_area_log->location_status = 0;
            $add_scanning_history_area_log->status = 0;
            $add_scanning_history_area_log->save();
            $latest_area_log_id = ShipmentScanningJourneyAreaLog::latest('id')->first()->id ?? 1;
            self::shipment_reporting_area_status($latest_area_log_id);
        }
    }


   

    static public function seal_number_add($bag_id, $screen_location_id, $admin_id)
    {
        if(session('role_id') != 1){
            $add_scanning_history = new BagScanningJourney();
            $add_scanning_history->bag_id = $bag_id;
            $add_scanning_history->screen_location_id = $screen_location_id;
            $add_scanning_history->admin_id = $admin_id;

            $whip = new Whip();
            $client_address = $whip->getValidIpAddress();

            if ($client_address != '') {
                $add_scanning_history->ip_address = $client_address;
            }

            if (Session::has('latitude') && Session::has('longitude')) {
                $add_scanning_history->latitude = session('latitude');
                $add_scanning_history->longitude = session('longitude');
            }

            $add_scanning_history->save();
        }
    }

    static public function shipment_reporting_area_status($latest_area_log_id)
    {
        $shipmentAreaLog = ShipmentScanningJourneyAreaLog::find($latest_area_log_id);

        if (!$shipmentAreaLog) {
            return;
        }

        $journey = $shipmentAreaLog->shipment_scanning_journey ?? null;
        $cityArea = $shipmentAreaLog->city_area->reporting_location ?? null;

        if (isset($journey, $cityArea)) {
            if (isset($journey->latitude, $journey->longitude, $cityArea->lat, $cityArea->long)) {
                $journeyLatitude = deg2rad($journey->latitude);
                $journeyLongitude = deg2rad($journey->longitude);
                $cityLatitude = deg2rad($cityArea->lat);
                $cityLongitude = deg2rad($cityArea->long);

                $earthRadius = 6371; 
                $longitudeDelta = $journeyLongitude - $cityLongitude;

                $distance = round($earthRadius * acos(
                    sin($cityLatitude) * sin($journeyLatitude) +
                    cos($cityLatitude) * cos($journeyLatitude) * cos($longitudeDelta)
                ), 2);

                $shipmentAreaLog->location_status = ($distance <= $cityArea->radius) ? 1 : 0;
            } else {
                $shipmentAreaLog->location_status = 0;
            }
        } else {
            $shipmentAreaLog->location_status = 0;
        }

        $shipmentAreaLog->status = 1;
        $shipmentAreaLog->save();
    }

}
