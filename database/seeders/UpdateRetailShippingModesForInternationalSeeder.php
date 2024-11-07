<?php

use Illuminate\Database\Seeder;

class UpdateRetailShippingModesForInternationalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('retail_shipping_modes')->insert(array(
            array('id' => 8, 'name' => 'Doc', 'business_category_id' => 2,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 9, 'name' => 'Non-Doc', 'business_category_id' => 2,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 10, 'name' => 'Box', 'business_category_id' => 2,'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
