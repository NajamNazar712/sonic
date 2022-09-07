<?php

namespace App\Console\Commands;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Rider\RiderDeliveryIncentiveRate;
use App\Http\Models\Rider\RiderIncentiveDelivery;
use App\Http\Models\Rider\RiderIncentiveDeliveryShipment;
use App\Http\Models\Rider\RiderIncentivePickup;
use App\Http\Models\ShipmentsJourney;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        $foc_accounts = GlobalSettings::where('type', 'foc_account_tag')->first();
        $foc_accounts = explode(',', $foc_accounts->text);
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
            ->whereDate('shipments_journey.created_at', $yesterday)
            ->get();

        $already_processed_shipments = array();
        if(count($shipments) > 0){
            $rider_incentive_details = array();
            foreach ($shipments as $shipment){
                if(!in_array($shipment->id, $already_processed_shipments)){
                    $rider_id = $shipment->rider_id;
                    $shipment_id = $shipment->id;

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
                    $delivery_incentive_shipment->save();

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
                        foreach ($shipment_type_ids as $shipment_type_id => $details){
                            foreach ($details as $detail){
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

        $void_accounts = GlobalSettings::whereIn('type', ['Walk-In', 'packaging_material'])->pluck('setting_value')->toArray();
        $carrefour__accounts = GlobalSettings::where('type', 'carrefour_accounts')->first();
        if($carrefour__accounts){
            $carrefour__accounts = explode(',', $carrefour__accounts->text);
        }
        else{
            $carrefour__accounts = array();
        }
        $segment_users = User::where('segment_id', 1)->where('sub_segment_id', 2)->pluck('id')->toArray();
        $void_accounts = array_merge($foc_accounts, $void_accounts, $carrefour__accounts, $segment_users, [167, 1159, 6693, 12412]);
        $shipments = ShipmentsJourney::join('shipments as s', 's.id', '=', 'shipments_journey.shipment_id')
            ->join('users as u', 'u.id', '=', 's.user_id')
            ->join('riders as r', 'r.id', '=', 'shipments_journey.rider_id')
            ->join('user_shipping_infos as usi', 'usi.id', '=', 's.pickup_address_id')
            ->select('shipments_journey.rider_id as rider_id', DB::raw('count(DISTINCT s.id) as shipment_count'), DB::raw('sum(s.actual_weight) as actual_weight'), 'r.city_id', 'r.rider_main_category_id')
            ->where('s.packaging_material_request', 0)
            ->where('usi.warehouse', '!=', 0)
            ->where('shipments_journey.shipper_status_id', 2)
            ->whereIn('s.shipper_status_id', [14, 30, 36, 37, 26, 27, 28, 29, 31, 32, 33, 34, 35, 38, 45, 46])
            ->whereNotIn('s.user_id', $void_accounts)
            ->whereNotNull('shipments_journey.rider_id')
            ->whereDate('shipments_journey.created_at', $yesterday)
            ->groupBy('shipments_journey.rider_id')
            ->get();

        if(count($shipments) > 0){
            foreach ($shipments as $shipment){
                if($shipment->main_rider_category == 1){
                    if(in_array($shipment->city_id, [202, 223])){
                        $rate = 1;
                    }
                    else{
                        $rate = 3;
                    }
                }
                else{
                    $rate = 5;
                }
                $incentive = $rate * $shipment->shipment_count;

                $rider_incentive_pickups = new RiderIncentivePickup();
                $rider_incentive_pickups->rider_id = $shipment->rider_id;
                $rider_incentive_pickups->date = $yesterday;
                $rider_incentive_pickups->shipments = $shipment->shipment_count;
                $rider_incentive_pickups->rate = $rate;
                $rider_incentive_pickups->incentive = $incentive;
                $rider_incentive_pickups->save();
            }
        }
    }
}
