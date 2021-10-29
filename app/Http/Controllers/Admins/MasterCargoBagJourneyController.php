<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\MasterCargo\MasterCargoBagJourney;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MasterCargoBagJourneyController extends Controller
{
    static public function add($bag_id, $seal_number, $bag_status_id, $admin_id, $master_cargo_id = NULL, $master_cargo_status_id = NULL){
        $bag_journey = new MasterCargoBagJourney();
        $bag_journey->bag_id = $bag_id;
        $bag_journey->seal_number = $seal_number;
        $bag_journey->bag_status_id = $bag_status_id;
        $bag_journey->admin_id = $admin_id;
        $bag_journey->master_cargo_id = $master_cargo_id;
        $bag_journey->master_cargo_status_id = $master_cargo_status_id;
        $bag_journey->save();
    }
}
