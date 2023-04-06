<?php

use Illuminate\Database\Seeder;

class SpecialRequestReasonsOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('special_request_reason_options')->truncate();

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('special_request_reason_options')->insert(array(
                array('id' => 1,'name' => 'Pre-requisites are fulfilled but the amount is > to Policy','created_at' => $timestamp, 'updated_at' => $timestamp),
                array('id' => 2,'name' => 'Pre-requisites are not fulfilled and the amount is > to Policy','created_at' => $timestamp, 'updated_at' => $timestamp),
                array('id' => 3,'name' => 'Pre-requisites are not fulfilled but the amount is < to Policy','created_at' => $timestamp, 'updated_at' => $timestamp),
                array('id' => 4,'name' => 'Time Lapsed for logging claim/No Pre-req and amount is also > to Policy','created_at' => $timestamp, 'updated_at' => $timestamp),
                array('id' => 5,'name' => 'Time Lapsed and the amount is < to Policy','created_at' => $timestamp, 'updated_at' => $timestamp),
                array('id' => 6,'name' => 'Invalid case due to packaging issue','created_at' => $timestamp, 'updated_at' => $timestamp),
                array('id' => 7,'name' => 'Others','created_at' => $timestamp, 'updated_at' => $timestamp),
            )
        );
    }
}
