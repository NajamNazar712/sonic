<?php

use Illuminate\Database\Seeder;

class OperationForecastWeightRangesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('operation_forecast_weight_ranges')->truncate();

        DB::table('operation_forecast_weight_ranges')->insert(array(
            array('id' => 1, 'name' => '0.5 KG', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name' => 'Upto 2 KG', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3, 'name' => 'Upto 5 KG', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 4, 'name' => 'Above 5 KG', 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
