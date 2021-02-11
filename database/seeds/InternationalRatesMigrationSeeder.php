<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InternationalRatesMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now();
        DB::table('international_user_rates')->truncate();
        $international_users = \App\Http\Models\InternationalUsersInformation::all();
        if(count($international_users) > 0){
            foreach ($international_users as $user_rate){
                if($user_rate->status == 1 || $user_rate->status == 4){
                    $international_user_rate = new \App\Http\Models\InternationalUserRate();
                    $international_user_rate->user_id = $user_rate->user_id;
                    $international_user_rate->margin = 20.00;
                    $international_user_rate->updated_by = 6;
                    $international_user_rate->rates_updated_at = $timestamp;
                    $international_user_rate->created_at = $timestamp;
                    $international_user_rate->updated_at = $timestamp;
                    $international_user_rate->save();
                }
                else if($user_rate->status == 2){
                    $international_user_rate = new \App\Http\Models\PendingInternationalUserRate();
                    $international_user_rate->user_id = $user_rate->user_id;
                    $international_user_rate->margin = 20.00;
                    $international_user_rate->updated_by = 6;
                    $international_user_rate->rates_updated_at = $timestamp;
                    $international_user_rate->created_at = $timestamp;
                    $international_user_rate->updated_at = $timestamp;
                    $international_user_rate->save();
                }
            }
        }
    }
}
