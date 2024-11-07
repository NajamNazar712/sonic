<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RiderTypeReferralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('rider_type_referrals')->truncate();
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('rider_type_referrals')->insert(array(
            array('id' => 1, 'name' => 'Referred by Courier', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'General Referred','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 3, 'name' => 'Non Referred','created_at'=>$timestamp,'updated_at'=>$timestamp)

        ));
    }
}
