<?php

use Illuminate\Database\Seeder;

class FleetTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('vehicle_types')->insert(array(
            array('id' => 1, 'name' => 'DHL vehicle Type'),
            
        ));

        DB::table('fleets')->insert(array(
            array('id' => 1, 'reg_number' => 'DHL Plane' , 'vehicle_type_id' => 1),
            
        ));
    }
}
