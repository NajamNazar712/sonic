<?php

namespace App\Console\Commands;

use App\ChangeLogs;
use App\RiderWiseDeliveryNoteShipment;
use App\RiderWiseDeliveryNoteSummary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class lastMileAppReportCountUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lastmile:countupdate {Startdate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force fully last mile update';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $lastMileReport = RiderWiseDeliveryNoteSummary::where([['delivery_date','>=',$this->argument('Startdate').' 00:00:00'],['delivery_date','<=', $this->argument('Startdate').' 23:59:59']])->get();
        $changesLogs = ChangeLogs::where('updated_by','346')->get();
        // foreach($changesLogs as $changes){
        //     $lastMileReport = RiderWiseDeliveryNoteSummary::where([['delivery_date', '>=', $this->argument('Startdate') . ' 00:00:00'], ['delivery_date', '<=', $this->argument('Startdate') . ' 23:59:59'],['id',$changes->record_id]])->get();
        //     $changesjson = json_decode($changes->old_data);
        //     RiderWiseDeliveryNoteSummary::where('id',$changes->record_id)->update([
        //         'shipment_update_count' => $changesjson->shipment_update_count,
        //         'before_11_count' => $changesjson->before_11_count,
        //         'at_11_count' => $changesjson->at_11_count,
        //         'at_12_count' => $changesjson->at_12_count,
        //         'at_13_count' => $changesjson->at_13_count,
        //         'at_14_count' => $changesjson->at_14_count,
        //         'at_15_count' => $changesjson->at_15_count,
        //         'at_16_count' => $changesjson->at_16_count,
        //         'at_17_count' => $changesjson->at_17_count,
        //         'at_18_count' => $changesjson->at_18_count,
        //         'at_19_count' => $changesjson->at_19_count,
        //         'at_20_count' => $changesjson->at_20_count,
        //         'at_21_count' => $changesjson->at_21_count,
        //         'at_21_count' => $changesjson->at_21_count,
        //         'at_22_count' => $changesjson->at_22_count,
        //         'at_23_count' => $changesjson->at_23_count,
        //         'via_rider_count' => $changesjson->via_rider_count
        //     ]);
        //     var_dump($changes->record_id);
           
        // }
        foreach($lastMileReport as $rwds){
            $before_11 = 0;
            $shipment_count = 0;
            $at_11_count = 0;
            $at_12_count = 0;
            $at_13_count = 0;
            $at_14_count = 0;
            $at_15_count = 0;
            $at_16_count = 0;
            $at_17_count = 0;
            $at_18_count = 0;
            $at_19_count = 0;
            $at_20_count = 0;
            $at_21_count = 0;
            $at_22_count = 0;
            $at_23_count = 0;
            $rider = RiderWiseDeliveryNoteShipment::select(
                'rwdnsum_id',
                DB::raw('COUNT(*) AS count'),
                // DB::raw('CONCAT("at_", DATE_FORMAT(updated_time, "%H"), "_count") AS time')
                DB::raw('DATE_FORMAT(updated_time, "%H") AS time')
            )->where([['rwdnsum_id',$rwds->id],['updated_time','>=', '00:00:00'], ['updated_time', '<=', '23:59:59']])->groupBy('time')
                ->orderBy('time')
                ->get();
                foreach($rider as $update){
                    if($update->time <= 10){
                        $before_11 += $update->count;
                    }
                    elseif($update->time == 11){
                         $at_11_count = $update->count;
                    }
                    elseif($update->time == 12){
                         $at_12_count = $update->count;
                    }
                    elseif($update->time == 13){
                         $at_13_count = $update->count;
                    }
                    elseif($update->time == 14){
                         $at_14_count = $update->count;
                    }
                    elseif($update->time == 15){
                         $at_15_count = $update->count;
                    }
                    elseif($update->time == 16){
                         $at_16_count = $update->count;
                    }
                    elseif($update->time == 17){
                         $at_17_count = $update->count;
                    }
                    elseif($update->time == 18){
                         $at_18_count = $update->count;
                    }
                    elseif($update->time == 19){
                         $at_19_count = $update->count;
                    }
                    elseif($update->time == 20){
                         $at_20_count = $update->count;
                    }
                    elseif($update->time == 21){
                         $at_21_count = $update->count;
                    }
                    elseif($update->time == 22 || $update->time == 23){
                         $at_23_count = $update->count;
                    }
                    $shipment_count += $update->count;
                }
                $originalValue = $rwds->getOriginal();
                $rwds->before_11_count = $before_11;
                $rwds->at_11_count = $at_11_count;
                $rwds->at_12_count = $at_12_count;
                $rwds->at_13_count = $at_13_count;
                $rwds->at_14_count = $at_14_count;
                $rwds->at_15_count = $at_15_count;
                $rwds->at_16_count = $at_16_count;
                $rwds->at_17_count = $at_17_count;
                $rwds->at_18_count = $at_18_count;
                $rwds->at_19_count = $at_19_count;
                $rwds->at_20_count = $at_20_count;
                $rwds->at_21_count = $at_21_count;
                $rwds->at_23_count = $at_23_count;
                $rwds->shipment_update_count = $shipment_count;
                $rwds->via_rider_count = $shipment_count;
                if($rwds->isDirty()){
                    $rwds->save();
                    ChangeLogs::where('record_id', $rwds->id)->update([
                        'table_name' => $rwds->getTable(),
                        'record_id' => $rwds->getKey(),
                        // 'old_data' => json_encode($originalValue),
                        'new_data' => json_encode($rwds->getChanges()),
                        'updated_by' => 346,
                    ]);
                    var_dump($rwds->rider_name);
                }
        }
    }
}
