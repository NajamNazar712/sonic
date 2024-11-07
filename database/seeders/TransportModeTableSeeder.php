<?php

use Illuminate\Database\Seeder;
use App\Http\Models\ShippingMode;
class TransportModeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transport_modes')->truncate();

        DB::table('transport_modes')->insert(array(
            array('id' => 1, 'name' => 'Air'),
            array('id' => 2, 'name' => 'Road'),
            array('id' => 3, 'name' => 'Train')
        ));
    }
}
