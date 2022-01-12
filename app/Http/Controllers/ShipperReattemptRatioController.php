<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\ReattemptPercentageForShipper;
use App\Http\Models\Shipper\User;
use Illuminate\Http\Request;
use DB;

class ShipperReattemptRatioController extends Controller
{
    static public function reattempt_ratio_calculate(){

        $settings = GlobalSettings::where('type', 'reattempt_percentage');
        
        if ($settings->exists()) {

            $users = User::where('status', 3)->whereNotIn('id', [1690, 8761, 9358])->pluck('id')->toArray();

            if(count($users) > 0){

                foreach ($users as $user_id){

                    $delivered_shipments_count = DB::connection('reports')->table('shipments')->where('packaging_material_request', '=', 0)->where('user_id', $user_id)->whereIn('shipper_status_id', [14, 30, 36, 37])->count('id');

                    $reattempt_count = DB::connection('reports')->table('shipments')->where('user_id', $user_id)
                        ->whereExists(function ($query) {
                            $query->from('shipments_journey')
                                ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->where('shipper_status_id', 12)
                                ->where('verification', 1);
                        })->where('shipments.packaging_material_request', '=', 0)->whereIn('shipments.shipper_status_id', [14, 30, 36, 37])->count();


                    if(($delivered_shipments_count > 0) && ($reattempt_count > 0)){
                        $user_percentage = ($reattempt_count / $delivered_shipments_count) * 100;
                        $user_percentage = round($user_percentage,2);

                        $reattempt_percentage_shippers = ReattemptPercentageForShipper::where('user_id', $user_id);
                        if($reattempt_percentage_shippers->exists()){
                            $reattempt_percentage_shippers = $reattempt_percentage_shippers->first();
                            $reattempt_percentage_shippers->percentage = $user_percentage;
                            $reattempt_percentage_shippers->save();

                        }
                        else{
                            $reattempt_percentage_for_shippers = new ReattemptPercentageForShipper();
                            $reattempt_percentage_for_shippers->user_id = $user_id;
                            $reattempt_percentage_for_shippers->percentage = $user_percentage;
                            $reattempt_percentage_for_shippers->save();
                        }
                    }
                }
            }
        }
    }
}
