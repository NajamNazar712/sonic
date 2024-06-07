<?php

use Illuminate\Database\Seeder;

class AddNewRetailShippingModeForInternationalEconomySeeder extends Seeder
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
            array('id' => 11, 'name' => 'International-Economy', 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
