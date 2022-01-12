<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Shipper\User;
use Illuminate\Http\Request;
use DB;

class ShipperReattemptRatioController extends Controller
{
    static public function reattempt_ratio_calculate(){

        $settings = GlobalSettings::where('type', 'reattempt_percentage');
        
        if ($settings->exists()) {
            /*$settings = $settings->first();
            $percentage = $settings->setting_value;*/

            $users = User::where('status', 3)->pluck('id')->toArray();

            if(count($users) > 0){

                $user_counts = array();
                foreach ($users as $user_id){


                        $total_count = DB::connection('reports')->table('shipments')->where('user_id', $user_id)->where('packaging_material_request', '=', 0)->whereNotIn('user_id', [1690, 8761, 9358])->whereNotIn('shipper_status_id', [1, 17])->count();

                        $reattempt_count = DB::connection('reports')->table('shipments')->where('user_id', $user_id)
                            ->whereExists(function ($query) {
                                $query->from('shipments_journey')
                                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                    ->where('shipper_status_id', 12)
                                    ->where('verification', 1);
                            })->where('shipments.packaging_material_request', '=', 0)->whereNotIn('shipments.user_id', [1690, 8761, 9358])->whereNotIn('shipments.shipper_status_id', [1, 17])->count();


                    if(($total_count > 0) && ($reattempt_count > 0)){
                        $user_percentage = ($reattempt_count / $total_count) * 100;
                        $user_counts = round($user_percentage,2);

                        $

                        $user_counts[$user_id]['total'] = $total_count;
                        $user_counts[$user_id]['reattempt'] = $reattempt_count;
                        $user_counts[$user_id]['percentage'] = round($user_percentage,2);
                    }

                }
                return $user_counts;

            }
        }
    }
}
