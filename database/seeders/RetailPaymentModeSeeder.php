<?php

use Illuminate\Database\Seeder;

class RetailPaymentModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('retail_payment_modes')->truncate();

        DB::table('retail_payment_modes')->insert(array(
            array('id' => 1, 'name' => 'Cash', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name' => 'Credit', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3, 'name' => 'QR', 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
