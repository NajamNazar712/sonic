<?php

use Illuminate\Database\Seeder;

class CityPickupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('city_pickup')->insert(array(
            array('pickup_type_id'=>1,'city_code'=>101),
            array('pickup_type_id'=>1,'city_code'=>102),
            array('pickup_type_id'=>1,'city_code'=>103),
            array('pickup_type_id'=>1,'city_code'=>104),
            array('pickup_type_id'=>1,'city_code'=>105),
            array('pickup_type_id'=>1,'city_code'=>106),
            array('pickup_type_id'=>1,'city_code'=>107),
            array('pickup_type_id'=>1,'city_code'=>108),
            array('pickup_type_id'=>1,'city_code'=>109),
            array('pickup_type_id'=>1,'city_code'=>110),
            array('pickup_type_id'=>1,'city_code'=>202),
            array('pickup_type_id'=>1,'city_code'=>174),
            array('pickup_type_id'=>1,'city_code'=>223),

        ));
    }
}
