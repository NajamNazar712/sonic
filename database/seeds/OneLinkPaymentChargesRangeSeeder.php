<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OneLinkPaymentChargesRangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('one_link_payment_charges_ranges')->insert(array(
            array('id' => 1,'range_up' => 0, 'range_down' => 10000, 'charges' => 10, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2,'range_up' => 10001, 'range_down' => 100000, 'charges' => 25, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3,'range_up' => 100001, 'range_down' => 250000, 'charges' => 50, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 4,'range_up' => 250001, 'range_down' => 1000000, 'charges' => 100, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 5,'range_up' => 1000001, 'range_down' => 10000000, 'charges' => 200, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            ));
    }
}
