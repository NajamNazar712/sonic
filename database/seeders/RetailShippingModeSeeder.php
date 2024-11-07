<?php

use Illuminate\Database\Seeder;

class RetailShippingModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('retail_shipping_modes')->truncate();

        DB::table('retail_shipping_modes')->insert(array(
            array('id' => 1, 'name' => 'Overland', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name' => 'Overnight', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3, 'name' => 'COD', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 4, 'name' => 'Detained', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 5, 'name' => 'Trax Box', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 6, 'name' => 'Flyers', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 7, 'name' => 'Hard Docs', 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
