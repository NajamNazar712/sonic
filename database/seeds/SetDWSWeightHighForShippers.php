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
        $shippers = User::whereIn('status', [3, 4])->get();

        foreach($shippers as $shipper){
            for ($i=1; $i <5;  $i++) { 

                $pending = PendingDwsWeightCharges::where('user_id',$shipper->id)->where('shipping_mode_id',$i);

                $active = DwsWeightCharges::where('user_id',$shipper->id)->where('shipping_mode_id',$i);
              
                $history = DwsWeightChargesHistory::where('user_id',$shipper->id)->where('shipping_mode_id',$i);
                

                if(!$pending->exists()){

                    PendingDwsWeightCharges::create([
                        'user_id' => $shipper->id,
                        'shipping_mode_id' => $i,
                        'dws_weight_status' => 1,
                        'admin_id' => 174
                    ]);
                }
                if(!$active->exists()){
                
                    DwsWeightCharges::create([
                        'user_id' => $shipper->id,
                        'shipping_mode_id' => $i,
                        'dws_weight_status' => 1,
                        'admin_id' => 174
                    ]);
                }
                if(!$history->exists()){
                
                    DwsWeightChargesHistory::create([
                        'user_id' => $shipper->id,
                        'shipping_mode_id' => $i,
                        'dws_weight_status' => 1,
                        'admin_id' => 174
                    ]);
                }
            }
        }
    }
}
