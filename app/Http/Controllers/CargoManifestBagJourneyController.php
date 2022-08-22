<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagJourney;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CargoManifestBagJourneyController extends Controller
{
    static public function add($bag_id, $seal_number, $bag_status_id, $admin_id, $cargo_manifest_id = NULL, $cargo_manifest_status_id = NULL, $screen_number = NULL){
        $bag_journey = new CargoManifestBagJourney();
        $bag_journey->cargo_manifest_bag_id = $bag_id;
        $bag_journey->seal_number = $seal_number;
        $bag_journey->bag_status_id = $bag_status_id;
        $bag_journey->admin_id = $admin_id;
        $bag_journey->cargo_manifest_id = $cargo_manifest_id;
        $bag_journey->cargo_manifest_status_id = $cargo_manifest_status_id;
        $bag_journey->junction_id = Auth::user()->default_hub_id ?? NULL;
        $bag_journey->screen_number = $screen_number;
        $bag_journey->save();
    }
}
