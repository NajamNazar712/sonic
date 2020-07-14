<?php

namespace App\Http\Controllers\Reports;

use App\Http\Models\Admin\StationDepositNote;
use App\Http\Models\City;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StationRecoveryController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    static public function station_recovery_data(){
        $yesterday = Carbon::yesterday();
        $hubs = City::where('hub', 1)->where('status', 1)->pluck('id')->toArray();
        if(count($hubs) > 0){
            foreach ($hubs as $hub_id){
                $station_notes = StationDepositNote::where('hub_id', $hub_id)->where('dncc_status',0)->get();
            }
        }
    }
}
