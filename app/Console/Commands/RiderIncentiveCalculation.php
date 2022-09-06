<?php

namespace App\Console\Commands;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Rider\RiderDeliveryIncentiveRate;
use App\Http\Models\Rider\RiderIncentiveDelivery;
use App\Http\Models\Rider\RiderIncentiveDeliveryShipment;
use App\Http\Models\ShipmentsJourney;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RiderIncentiveCalculation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rider_incentive:calculation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rider Incentive Calculation Version 2';

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

    private function rate_calculation($city_id, $courier_type_id, $shipment_type_id, $shipment_weight_type_id){
        if($city_id != 223){
            $city_id = null;
        }
        $delivery_incentive_rate = RiderDeliveryIncentiveRate::where('city_id', $city_id)->where('courier_type_id', $courier_type_id)->where('shipment_type_id', $shipment_type_id)->where('shipment_weight_type_id', $shipment_weight_type_id);
        if($delivery_incentive_rate->exists()){
            $delivery_incentive_rate = $delivery_incentive_rate->first();
            $rate = $delivery_incentive_rate->rate;
        }
        else{
            $rate = 0;
        }

        return $rate;
    }

    public function handle()
    {
        //SELECT DISTINCT(`s`.`id`), `s`.`actual_weight` FROM `shipments_journey` AS `sj` INNER JOIN `shipments` AS `s` ON `sj`.`shipment_id` = `s`.`id` WHERE `s`.`packaging_material_request` = 0 AND `sj`.`created_at` >= '2022-09-05 00:00:00' AND `sj`.`created_at` < '2022-09-06 00:00:00' AND `sj`.`shipper_status_id` IN (14, 30, 36, 37) AND `s`.`shipper_status_id` IN (14, 30, 36, 37, 26, 27, 28, 29, 31, 32, 33, 34, 35, 38, 45, 46) AND NOT FIND_IN_SET(`s`.`user_id`, (SELECT `text` FROM `global_settings` WHERE `type` = 'foc_account_tag')) AND `sj`.`rider_id` IS NOT NULL;
        $foc_accounts = GlobalSettings::where('type', 'foc_account_tag')->first();
        $foc_accounts = explode(',', $foc_accounts);
        $yesterday = Carbon::yesterday()->toDateString();
        $shipments = ShipmentsJourney::join('shipments as s', 's.id', '=', 'shipments_journey.shipment_id')
            ->join('users as u', 'u.id', '=', 's.user_id')
            ->join('riders as r', 'r.id', '=', 'shipments_journey.rider_id')
            ->select('shipments_journey.rider_id as rider_id', 's.id as id', 's.actual_weight as actual_weight', 's.amount as amount', 'u.segment_id as segment_id', 'u.sub_segment_id as sub_segment_id', 'r.rider_category_id as rider_category_id', 'r.city_id as city_id')
            ->where('s.packaging_material_request', 0)
            ->whereIn('shipments_journey.shipper_status_id', [14, 30, 36, 37])
            ->whereIn('s.shipper_status_id', [14, 30, 36, 37, 26, 27, 28, 29, 31, 32, 33, 34, 35, 38, 45, 46])
            ->whereNotIn('s.user_id', $foc_accounts)
            ->whereNotNull('shipments_journey.rider_id')
            ->whereDate('shipments_journey.date', $yesterday);

        $already_processed_shipments = array();
        if(count($shipments) > 0){
            $rider_incentive_details = array();
            foreach ($shipments as $shipment){
                if(!in_array($shipment->id, $already_processed_shipments)){
                    $rider_id = $shipment->rider_id;
                    $shipment_id = $shipment->shipment_id;

                    if($shipment->rider_category_id == 2){
                        $courier_type_id = 2;
                    }
                    else{
                        $courier_type_id = 1;
                    }

                    if($shipment->amount > 0){
                        $shipment_type_id = 1;
                    }
                    else if($shipment->amount == 0 && $shipment->segment_id == 1 && $shipment->sub_segment_id == 2){
                        $shipment_type_id = 3;
                    }
                    else{
                        $shipment_type_id = 2;
                    }

                    if($shipment->segment_id == 1 && $shipment->sub_segment_id == 2){
                        $shipment_weight_type_id = 3;
                    }
                    else if($courier_type_id == 2){
                        if($shipment->actual_weight <= 5){
                            $shipment_weight_type_id = 1;
                        }
                        else{
                            $shipment_weight_type_id = 2;
                        }
                    }
                    else{
                        if($shipment->actual_weight <= 1.5){
                            $shipment_weight_type_id = 1;
                        }
                        else{
                            $shipment_weight_type_id = 2;
                        }
                    }

                    $rate = $this::rate_calculation($shipment->city_id, $courier_type_id, $shipment_type_id, $shipment_weight_type_id);

                    $delivery_incentive_shipment = new RiderIncentiveDeliveryShipment();
                    $delivery_incentive_shipment->rider_id = $rider_id;
                    $delivery_incentive_shipment->date = $yesterday;
                    $delivery_incentive_shipment->courier_type_id = $courier_type_id;
                    $delivery_incentive_shipment->shipment_type_id = $shipment_type_id;
                    $delivery_incentive_shipment->shipment_weight_type_id = $shipment_weight_type_id;
                    $delivery_incentive_shipment->shipment_id = $shipment_id;
                    $delivery_incentive_shipment->rate = $rate;

                    $flag = false;
                    if(array_key_exists($rider_id, $rider_incentive_details)){
                        if(array_key_exists($courier_type_id, $rider_incentive_details[$rider_id])){
                            if(array_key_exists($shipment_type_id, $rider_incentive_details[$rider_id][$courier_type_id])){
                                $flag = true;
                            }
                        }
                    }
                    if($flag == true){
                        $rider_incentive_details[$rider_id][$courier_type_id][$shipment_type_id][$shipment_weight_type_id]['rate'] = $rate + $rider_incentive_details[$rider_id][$courier_type_id][$shipment_type_id][$shipment_weight_type_id]['rate'];
                        $rider_incentive_details[$rider_id][$courier_type_id][$shipment_type_id][$shipment_weight_type_id]['shipments']++;
                    }
                    else{
                        $rider_incentive_details[$rider_id][$courier_type_id][$shipment_type_id][$shipment_weight_type_id]['rate'] = $rate;
                        $rider_incentive_details[$rider_id][$courier_type_id][$shipment_type_id][$shipment_weight_type_id]['shipments'] = 1;
                    }

                    $already_processed_shipments[] = $shipment->id;
                }
            }

            if(count($rider_incentive_details) > 0){
                foreach ($rider_incentive_details as $rider_id => $courier_type_ids){
                    foreach ($courier_type_ids as $courier_type_id => $shipment_type_ids){
                        foreach ($shipment_type_ids as $shipment_type_id => $detail){

                            $shipment_count = $detail['shipments'];
                            $rate = $detail['rate'];
                            $incentive = $rate * $shipment_count;

                            $delivery_incentive = new RiderIncentiveDelivery();
                            $delivery_incentive->rider_id = $rider_id;
                            $delivery_incentive->date = $yesterday;
                            $delivery_incentive->courier_type_id = $courier_type_id;
                            $delivery_incentive->shipment_type_id = $shipment_type_id;
                            $delivery_incentive->shipment_weight_type_id = $shipment_weight_type_id;
                            $delivery_incentive->shipments = $shipment_count;
                            $delivery_incentive->rate = $rate;
                            $delivery_incentive->incentive = $incentive;
                            $delivery_incentive->save();
                        }
                    }
                }
            }
        }
    }
}
