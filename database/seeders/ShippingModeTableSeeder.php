<?php

use Illuminate\Database\Seeder;
use App\Http\Models\ShippingMode;
class ShippingModeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipping_modes')->truncate();
        DB::table('shipping_modes')->insert(array(
            array('mode'=>'Overnight'),
            array('mode'=>'Overland'),
            array('mode'=>'Detain'),
            array('mode'=>'Same-day'),


        ));
    }
}
