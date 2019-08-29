<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AdjustmentTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('adjustment_types')->truncate();

        DB::table('adjustment_types')->insert(array(
            array('id' => 1, 'name' => 'Adjusted from Return Confirm', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name' => 'Adjusted from Delivered', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3, 'name' => 'Adjusted from Month Closing', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 4, 'name' => 'Manual Adjustment', 'created_at' => $timestamp, 'updated_at' => $timestamp),

        ));
    }
}
