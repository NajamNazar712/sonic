<?php

use Illuminate\Database\Seeder;
use App\Http\Models\ShippingMode;
class TransportModeVendorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transport_mode_vendors')->truncate();

        DB::table('transport_mode_vendors')->insert(array(
            array('id' => 1, 'transport_mode_id' => 1, 'name' => 'Airblue'),
            array('id' => 2, 'transport_mode_id' => 1, 'name' => 'PIA'),
            array('id' => 3, 'transport_mode_id' => 1, 'name' => 'Serene'),
            array('id' => 4, 'transport_mode_id' => 1, 'name' => 'Shaheen'),
            array('id' => 5, 'transport_mode_id' => 2, 'name' => 'Daewoo'),
            array('id' => 6, 'transport_mode_id' => 2, 'name' => 'Faisal Movers'),
            array('id' => 7, 'transport_mode_id' => 2, 'name' => 'TM Cargo'),
            array('id' => 8, 'transport_mode_id' => 3, 'name' => 'Lasani Cargo')
        ));
    }
}
