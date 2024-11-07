<?php

use Illuminate\Database\Seeder;

class MinimumChargeableWeights extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('minimum_chargeable_weight_settings')->truncate();

        DB::table('minimum_chargeable_weight_settings')->insert(array(
            array('id' => 1, 'shipping_mode_id' => 1, 'weight' => 0.01),
            array('id' => 2, 'shipping_mode_id' => 2, 'weight' => 0.01),
            array('id' => 3, 'shipping_mode_id' => 3, 'weight' => 0.01),
            array('id' => 4, 'shipping_mode_id' => 4, 'weight' => 0.01)
        ));
    }
}
