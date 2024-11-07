<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CorporateStandardWeightChargesZoneWise extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('corporate_standard_weight_charge_zone_wises')->truncate();
        DB::table('corporate_standard_weight_charge_zone_wises')->insert(array(
            array('shipping_mode_id'=>1,'delivery_type_id' => 1,'range_up'=>0.01,'range_down'=>10,'local'=>50,'same_zone'=>50,'different_zone'=>50),
            array('shipping_mode_id'=>1,'delivery_type_id' => 1,'range_up'=>10.01,'range_down'=>1000,'local'=>50,'same_zone'=>50,'different_zone'=>50),
            array('shipping_mode_id'=>1,'delivery_type_id' => 2,'range_up'=>0.01,'range_down'=>10,'local'=>20,'same_zone'=>20,'different_zone'=>20),
            array('shipping_mode_id'=>1,'delivery_type_id' => 2,'range_up'=>10.01,'range_down'=>1000,'local'=>20,'same_zone'=>20,'different_zone'=>20),
            array('shipping_mode_id'=>2,'delivery_type_id' => 1,'range_up'=>0.01,'range_down'=>10,'local'=>50,'same_zone'=>50,'different_zone'=>50),
            array('shipping_mode_id'=>2,'delivery_type_id' => 1,'range_up'=>10.01,'range_down'=>1000,'local'=>50,'same_zone'=>50,'different_zone'=>50),
            array('shipping_mode_id'=>2,'delivery_type_id' => 2,'range_up'=>0.01,'range_down'=>10,'local'=>20,'same_zone'=>20,'different_zone'=>20),
            array('shipping_mode_id'=>2,'delivery_type_id' => 2,'range_up'=>10.01,'range_down'=>1000,'local'=>20,'same_zone'=>20,'different_zone'=>20),
            array('shipping_mode_id'=>3,'delivery_type_id' => 1,'range_up'=>0.01,'range_down'=>10,'local'=>50,'same_zone'=>50,'different_zone'=>50),
            array('shipping_mode_id'=>3,'delivery_type_id' => 1,'range_up'=>10.01,'range_down'=>1000,'local'=>50,'same_zone'=>50,'different_zone'=>50),
            array('shipping_mode_id'=>3,'delivery_type_id' => 2,'range_up'=>0.01,'range_down'=>10,'local'=>20,'same_zone'=>20,'different_zone'=>20),
            array('shipping_mode_id'=>3,'delivery_type_id' => 2,'range_up'=>10.01,'range_down'=>1000,'local'=>20,'same_zone'=>20,'different_zone'=>20),
            array('shipping_mode_id'=>4,'delivery_type_id' => 1,'range_up'=>0.01,'range_down'=>10,'local'=>50,'same_zone'=>50,'different_zone'=>0),
            array('shipping_mode_id'=>4,'delivery_type_id' => 1,'range_up'=>10.01,'range_down'=>1000,'local'=>50,'same_zone'=>50,'different_zone'=>0),
            array('shipping_mode_id'=>4,'delivery_type_id' => 2,'range_up'=>0.01,'range_down'=>10,'local'=>20,'same_zone'=>20,'different_zone'=>0),
            array('shipping_mode_id'=>4,'delivery_type_id' => 2,'range_up'=>10.01,'range_down'=>1000,'local'=>20,'same_zone'=>20,'different_zone'=>0),


        ));
    }
}
