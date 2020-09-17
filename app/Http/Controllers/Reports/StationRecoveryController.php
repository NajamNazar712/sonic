<?php

namespace App\Http\Controllers\Reports;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\StationDepositNote;
use App\Http\Models\City;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\StationRecoveryReport;
use App\Http\Models\ZoneClassCity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class StationRecoveryController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    static public function station_recovery_data(){
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();
        $cut_off_time = '10';
        $settings = GlobalSettings::where('type', 'station_recovery_cron_time');
        if ($settings->exists()) {
            $settings = $settings->first();
            $cut_off_time = $settings->setting_value;
        }
        $from_date = Carbon::createFromFormat('Y-m-d H', $yesterday . ' '.$cut_off_time)->toDateTimeString();
        $to_date = Carbon::createFromFormat('Y-m-d H', $today . ' '.$cut_off_time)->toDateTimeString();

        $hubs = City::where('hub', 1)->where('status', 1)->pluck('id')->toArray();
        if(count($hubs) > 0){
            foreach ($hubs as $hub_id){
                $city_ids = City::where('hub_id', $hub_id)->pluck('id')->toArray();
                $zone = ZoneClassCity::where('city_id',$hub_id)->first();
                $zone_id = $zone->zone_id;
                $delivered_shipments_ids = ShipmentsJourney::whereIn('shipper_status_id', [14, 30, 36, 37])->whereIn('city_id', $city_ids)->where('verification', 1)->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->pluck('shipment_id')->toArray();
                $no_of_delivered_shipments = count($delivered_shipments_ids);
//                $last_day_shipments = ShipmentsJourney::whereIn('shipper_status_id', [14, 30, 36, 37])->whereIn('city_id', $city_ids)->where('verification', 1)->whereDate('created_at', '<=', $yesterday)->whereTime('created_at', '<=', $cut_off_time)->pluck('shipment_id')->toArray();
//                $last_day_balance = Shipment::whereIn('id', $last_day_shipments)->sum('amount');
                //for first day
                $last_day_balance = 0;
                $station_recovery_rep = StationRecoveryReport::where('city_id', $hub_id);
                if($station_recovery_rep->exists()){
                    $station_recovery_rep = $station_recovery_rep->first();
                    $last_day_balance = $station_recovery_rep->last_day_balance;
                }
                //for first day

                $amount = Shipment::whereIn('id', $delivered_shipments_ids)->sum('amount');
                $total_amount = $last_day_balance + $amount;

                $station_recovery = new StationRecoveryReport();
                $station_recovery->date = $today;
                $station_recovery->city_id = $hub_id;
                $station_recovery->zone_id = $zone_id;
                $station_recovery->delivered_shipments = $no_of_delivered_shipments;
                $station_recovery->last_day_balance = $last_day_balance;
                $station_recovery->amount = $amount;
                $station_recovery->total_amount = $total_amount;

                $station_recovery->save();
            }
        }
    }
}
