<?php

use App\Http\Models\ShippingMode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateShippingModeSeeder extends Seeder
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

            array('id' => 1, 'mode' => 'Rush'),
            array('id' => 2, 'mode' => 'SaverPlus'),
            array('id' => 3, 'mode' => 'Swift'),
            array('id' => 4, 'mode' => 'Same-day'),
        
        ));

    }
}
