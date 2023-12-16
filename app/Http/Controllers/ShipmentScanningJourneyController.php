<?php

namespace App\Http\Controllers;

use Session;
use Vectorface\Whip\Whip;
use App\Http\Models\Admin\Admin;
use App\Http\Models\BagScanningJourney;
use App\ShipmentScanningJourneyAreaLog;
use App\Http\Models\ShipmentScanningJourney;
use Illuminate\Support\Facades\Auth;

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
            self::shipment_scanning_area_logs($shipment_id);

         
        }
    }

    static public function shipment_scanning_area_logs($shipment_id)
    {
        $adminId = Auth::id();
        $employee = Admin::find($adminId)->employee ?? null;

        $latestShipmentScanningId = ShipmentScanningJourney::latest('id')->first()->id;

        $addScanningHistoryAreaLog = new ShipmentScanningJourneyAreaLog([
            'shipment_id' => $shipment_id,
            'shipment_scanning_journey_id' => $latestShipmentScanningId,
            'hub_id' => Admin::find($adminId)->default_hub_id ?? null,
            'area_id' => optional($employee)->area_id,
            'admin_id' => $adminId,
            'location_status' => 0,
            'status' => 0,
        ]);

        $addScanningHistoryAreaLog->save();
        self::shipment_reporting_area_status($latestShipmentScanningId);
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

    static public function shipment_reporting_area_status($latest_shipment_scanning_id)
    {
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
