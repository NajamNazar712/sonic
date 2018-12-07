<?php

use Illuminate\Database\Seeder;

use Carbon\Carbon;

class ZonesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('zones')->truncate();
        DB::table('zones')->insert(array(
            array('id' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'South', 'gst' => 0.13),
            array('id' => 2, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Central', 'gst' => 0.16),
            array('id' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'North', 'gst' => 0.16)
        ));
    }
}
