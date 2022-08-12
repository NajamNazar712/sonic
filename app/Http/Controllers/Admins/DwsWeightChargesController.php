<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\DwsWeightCharges;
use App\Http\Models\DwsWeightChargesHistory;
use App\Http\Models\PendingDwsWeightCharges;

class DwsWeightChargesController extends Controller
{
    //

    static public function add($user_id, $shipping_mode_id, $dws_weight_status,$admin_id)
    {

        PendingDwsWeightCharges::create([
            'user_id' => $user_id,
            'shipping_mode_id' => $shipping_mode_id,
            'dws_weight_status' => $dws_weight_status,
            'admin_id' => $admin_id
        ]);
    }
    static public function edit($user_id, $shipping_mode_id, $dws_weight_status,$admin_id)
    {
        PendingDwsWeightCharges::where('user_id',$user_id)->where('shipping_mode_id',$shipping_mode_id)->delete();

        PendingDwsWeightCharges::create([ 
            'user_id' => $user_id,
            'shipping_mode_id' => $shipping_mode_id,
            'dws_weight_status' => $dws_weight_status,
            'admin_id' => $admin_id
        ]);

    }

    static public function delete_dws_rate($user_id, $shipping_mode_id)
    {
        PendingDwsWeightCharges::where('user_id',$user_id)->where('shipping_mode_id',$shipping_mode_id)->delete();

        DwsWeightChargesHistory::create([
            'user_id' => $user_id,
            'shipping_mode_id' => $shipping_mode_id,
            'dws_weight_status' => 0,
            'admin_id' => 0
        ]);

        

    }

    static public function approve($user_id)
    {
        DwsWeightCharges::where('user_id',$user_id)->delete();

        $pending_wight = PendingDwsWeightCharges::where('user_id',$user_id);
        if($pending_wight->exists()){
            $pending_wight = $pending_wight->get();
            foreach ($pending_wight as $value) {

                DwsWeightCharges::create([
                    'user_id' => $value->user_id,
                    'shipping_mode_id' => $value->shipping_mode_id,
                    'dws_weight_status' => $value->dws_weight_status,
                    'admin_id' => $value->admin_id
                ]);
                DwsWeightChargesHistory::create([
                    'user_id' => $value->user_id,
                    'shipping_mode_id' => $value->shipping_mode_id,
                    'dws_weight_status' => $value->dws_weight_status,
                    'admin_id' => $value->admin_id
                ]);
            }
        }
        PendingDwsWeightCharges::where('user_id',$user_id)->delete();
       
    }
}
