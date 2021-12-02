<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Shipper\User;
use App\Http\Models\DwsWeightCharges;
use App\Http\Models\DwsWeightChargesHistory;
use App\Http\Models\PendingDwsWeightCharges;
use App\Http\Models\RateStatus;
use App\Http\Models\CorporateRateStatus;

class SetDWSWeightHighForShippers extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shippers = User::where('status', 3)->pluck('id')->toArray();

                $rates =  RateStatus::whereIn('user_id',$shipper);
                if($rates->exists()){
                    foreach ($rates->get() as $value) {
                        PendingDwsWeightCharges::create([
                            'user_id' => $value->user_id,
                            'shipping_mode_id' => $value->shipping_mode_id,
                            'dws_weight_status' => 1,
                            'admin_id' => 174
                        ]);
                        DwsWeightCharges::create([
                            'user_id' => $value->user_id,
                            'shipping_mode_id' => $value->shipping_mode_id,
                            'dws_weight_status' => 1,
                            'admin_id' => 174
                        ]);
                    }
                }
                
                    $rates =  CorporateRateStatus::whereIn('user_id',$shipper);
                    if($rates->exists()){
                        foreach ($rates->get() as $value) {
                            PendingDwsWeightCharges::create([
                                'user_id' => $value->user_id,
                                'shipping_mode_id' => $value->shipping_mode_id,
                                'dws_weight_status' => 1,
                                'admin_id' => 174
                            ]);
                            DwsWeightCharges::create([
                                'user_id' => $value->user_id,
                                'shipping_mode_id' => $value->shipping_mode_id,
                                'dws_weight_status' => 1,
                                'admin_id' => 174
                            ]);
                        }
                    }
                
               
    }
}
