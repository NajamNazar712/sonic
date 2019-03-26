<?php

use Illuminate\Database\Seeder;

class WalkInStandardWeightChargesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('walk_in_standard_weight_charges')->truncate();
        DB::table('walk_in_standard_weight_charges')->insert(array(
            array('shipping_mode_id'=>1,'delivery_type_id' => 1 ,'actual_weight'=>2,'chargeable_weight'=>70,'local' => 50 , 'national' => 100),
            array('shipping_mode_id'=>1,'delivery_type_id' => 2 ,'actual_weight'=>2,'chargeable_weight'=>60,'local' => 50 , 'national' => 100),
            array('shipping_mode_id'=>2,'delivery_type_id' => 1 ,'actual_weight'=>5,'chargeable_weight'=>30,'local' => 50 , 'national' => 100),
            array('shipping_mode_id'=>2,'delivery_type_id' => 2 ,'actual_weight'=>5,'chargeable_weight'=>20,'local' => 50 , 'national' => 100),
            array('shipping_mode_id'=>3,'delivery_type_id' => 1 ,'actual_weight'=>10,'chargeable_weight'=>40,'local' => 50 , 'national' => 100),
            array('shipping_mode_id'=>3,'delivery_type_id' => 2 ,'actual_weight'=>10,'chargeable_weight'=>30,'local' => 50 , 'national' => 100),

        ));
    }
}