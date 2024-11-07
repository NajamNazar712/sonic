<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ZoneRegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        
        DB::table('regions')->insert(array(
            array('id' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'South Region', 'status_id' => 1),
            array('id' => 2, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Punjab Region', 'status_id' => 1),
            array('id' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'North Region', 'status_id' => 1)
        ));

        // DB::table('zone_regions')->truncate();
        DB::table('zone_regions')->insert(array(
            array('id' => 1,'zone_id' => 2,'region_id' => 2, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2,'zone_id' => 5,'region_id' => 2, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3,'zone_id' => 3,'region_id' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 4,'zone_id' => 4,'region_id' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 5,'zone_id' => 1,'region_id' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
