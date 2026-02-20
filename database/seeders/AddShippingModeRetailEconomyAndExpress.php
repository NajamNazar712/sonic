<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddShippingModeRetailEconomyAndExpress extends Seeder
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
            array('id' => 13, 'name' => 'Economy', 'business_category_id' => 1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 14, 'name' => 'Express', 'business_category_id' => 1,'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
