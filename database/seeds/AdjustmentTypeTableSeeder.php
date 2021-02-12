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
            array('id' => 5, 'name' => 'Replacement to Regular Adjustment', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 6, 'name' => 'Manual Adjustment - Weight Dispute', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 7, 'name' => 'Manual Adjustment - Amount Change', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 8, 'name' => 'Manual Adjustment - Damage Shipment', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 9, 'name' => 'Manual Adjustment - Short Content', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 10, 'name' => 'Manual Adjustment - Shipment Lost', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 11, 'name' => 'Manual Adjustment - Charges Wave Off', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 12, 'name' => 'Adjustment for Weight Change', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 13, 'name' => 'Adjusted from Month Closing (Return Confirm)', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 14, 'name' => 'Adjusted from Month Closing (Delivered)', 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
