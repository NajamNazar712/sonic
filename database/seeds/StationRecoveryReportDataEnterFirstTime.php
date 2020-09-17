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
//        DB::table('station_recovery_reports')->truncate();
//        DB::table('station_recovery_report_deposits')->truncate();

        $data = array(
            101 => (
              ['delivered_shipments' => 13000 ,'last_day_balance' => 4000, 'amount' => 30000]
            ),
            106 => (
              ['delivered_shipments' => 13000 ,'last_day_balance' => 4000, 'amount' => 30000]
            ),
        );
        $hubs = City::where('hub', 1)->where('status', 1)->pluck('id')->toArray();
        if(count($hubs) > 0){
            foreach ($hubs as $hub_id){
                $report = \App\Http\Models\StationRecoveryReport::where('city_id', $hub_id)->first();
                if($report){
                    $report->delivered_shipments = $data[$hub_id]['delivered_shipments'];
                    $report->last_day_balance = $data[$hub_id]['last_day_balance'];
                    $report->amount = $data[$hub_id]['amount'];
                    $report->save();
                }

            }
        }
    }
}
