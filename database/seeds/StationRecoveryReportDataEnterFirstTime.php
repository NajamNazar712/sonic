<?php

use App\Http\Models\City;
use Illuminate\Database\Seeder;

class StationRecoveryReportDataEnterFirstTime extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('station_recovery_reports')->truncate();
        DB::table('station_recovery_report_deposits')->truncate();

        $hubs = City::where('hub', 1)->where('status', 1)->pluck('id')->toArray();
        if(count($hubs) > 0){
            foreach ($hubs as $hub_id){
                
            }
        }
    }
}
