<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InternationalDHLZoneMapTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('international_dhl_zones')->truncate();
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('international_dhl_zones')->insert(array(
            array('id' => 1, 'zone_id' => 13, 'zone_name' => 'zone_1', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'zone_id' => 12, 'zone_name' => 'zone_2', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 3, 'zone_id' => 11, 'zone_name' => 'zone_3', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 4, 'zone_id' => 10, 'zone_name' => 'zone_4', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 5, 'zone_id' => 9, 'zone_name' => 'zone_5', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 6, 'zone_id' => 8, 'zone_name' => 'zone_6', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 7, 'zone_id' => 7, 'zone_name' => 'zone_7', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 8, 'zone_id' => 6, 'zone_name' => 'zone_8', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 9, 'zone_id' => 14, 'zone_name' => 'zone_9', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 10, 'zone_id' => 15, 'zone_name' => 'zone_10', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 11, 'zone_id' => 16, 'zone_name' => 'zone_11', 'created_at'=>$timestamp,'updated_at'=>$timestamp),

        ));
    }
}
