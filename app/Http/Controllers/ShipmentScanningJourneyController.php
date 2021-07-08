<?php

namespace App\Http\Controllers;

use App\Http\Models\ShipmentScanningJourney;
use Illuminate\Http\Request;
use Vectorface\Whip\Whip;

class ShipmentScanningJourneyController extends Controller
{
    //User Types 4 -> Retail User
    static public function add($shipment_id, $screen_location_id, $user_type, $admin_id = NULL, $user_id = NULL, $substitute_user_id = NULL) {
        $add_scanning_history = new ShipmentScanningJourney();
        $add_scanning_history->shipment_id = $shipment_id;
        $add_scanning_history->screen_location_id = $screen_location_id;
        $add_scanning_history->user_type = $user_type;
        $add_scanning_history->admin_id = $admin_id;
        $add_scanning_history->user_id = $user_id;
        $add_scanning_history->substitute_user_id = $substitute_user_id;

        $whip = new Whip();
        $client_address = $whip->getValidIpAddress();

        if ($client_address != '') {
            $add_scanning_history->ip_address = $client_address;
        }

        $add_scanning_history->save();
    }
}
