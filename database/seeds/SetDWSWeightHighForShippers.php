<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Shipper\User;
use App\Http\Models\DwsWeightCharges;
use App\Http\Models\DwsWeightChargesHistory;
use App\Http\Models\PendingDwsWeightCharges;

class SetDWSWeightHighForShippers extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shippers = User::where('status', 3)->get();

        foreach($shippers as $shipper){
            // for (1=1; 1 <5;  1++) { 

                    PendingDwsWeightCharges::create([
                        'user_id' => $shipper->id,
                        'shipping_mode_id' => 1,
                        'dws_weight_status' => 1,
                        'admin_id' => 174
                    ]);
                    PendingDwsWeightCharges::create([
                        'user_id' => $shipper->id,
                        'shipping_mode_id' => 2,
                        'dws_weight_status' => 1,
                        'admin_id' => 174
                    ]);
                    PendingDwsWeightCharges::create([
                        'user_id' => $shipper->id,
                        'shipping_mode_id' => 3,
                        'dws_weight_status' => 1,
                        'admin_id' => 174
                    ]);
                    PendingDwsWeightCharges::create([
                        'user_id' => $shipper->id,
                        'shipping_mode_id' => 4,
                        'dws_weight_status' => 1,
                        'admin_id' => 174
                    ]);
                
                    DwsWeightCharges::create([
                        'user_id' => $shipper->id,
                        'shipping_mode_id' => 1,
                        'dws_weight_status' => 1,
                        'admin_id' => 174
                    ]);
                    DwsWeightCharges::create([
                        'user_id' => $shipper->id,
                        'shipping_mode_id' => 2,
                        'dws_weight_status' => 1,
                        'admin_id' => 174
                    ]);
                    DwsWeightCharges::create([
                        'user_id' => $shipper->id,
                        'shipping_mode_id' => 3,
                        'dws_weight_status' => 1,
                        'admin_id' => 174
                    ]);
                    DwsWeightCharges::create([
                        'user_id' => $shipper->id,
                        'shipping_mode_id' => 4,
                        'dws_weight_status' => 1,
                        'admin_id' => 174
                    ]);
                
                    // DwsWeightChargesHistory::create([
                    //     'user_id' => $shipper->id,
                    //     'shipping_mode_id' => 1,
                    //     'dws_weight_status' => 1,
                    //     'admin_id' => 174
                    // ]);
            // }
            
        }
    }
}
