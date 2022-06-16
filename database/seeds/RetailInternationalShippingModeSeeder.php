<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RetailInternationalShippingModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       DB::table('retail_international_shipping_modes')->insert(array(
           array('id' => 1, 'name' => 'Doc'),
           array('id' => 2, 'name' => 'Non-Doc'),
           array('id' => 3, 'name' => 'Box'))
       );
    }
}
