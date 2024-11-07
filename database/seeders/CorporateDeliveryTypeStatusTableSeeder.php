<?php

namespace Database\Seeders;

use App\Http\Models\Shipper\User;
use Illuminate\Database\Seeder;

class CorporateDeliveryTypeStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::where('account_type_id', 2)->where('status', 3)->pluck('id')->toArray();
        foreach ($users as $user_id){
            $rate_status = \App\Http\Models\CorporateRateStatus::where('user_id', $user_id);
            if($rate_status->exists()){
                $rate_status = $rate_status->get();
                foreach ($rate_status as $status){
                    $delivery_type_status = new \App\Http\Models\CorporateDeliveryTypeStatus();
                    $delivery_type_status->user_id = $user_id;
                    $delivery_type_status->delivery_type_id = 2;
                    $delivery_type_status->shipping_mode_id = $status->shipping_mode_id;
                    $delivery_type_status->status = 1;
                    $delivery_type_status->save();
                }
            }
        }
    }
}
