<?php

use Illuminate\Database\Seeder;

class NewCityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cities')->truncate();
        DB::table('cities')->insert(array(
            array('id'=>1,'name'=>'Multiple','hub'=>1,'hub_id'=>0,'pickup'=>1,'status'=>1),
            array('id'=>101,'name'=>'Abbottabad','hub'=>1,'hub_id'=>101,'pickup'=>1,'status'=>1),
            array('id'=>102,'name'=>'Abdul Hakim','hub'=>0,'hub_id'=>251,'pickup'=>1,'status'=>1),
            array('id'=>103,'name'=>'Ahmed Pur East','hub'=>0,'hub_id'=>110,'pickup'=>1,'status'=>1),
            array('id'=>104,'name'=>'Alipur','hub'=>0,'hub_id'=>251,'pickup'=>1,'status'=>1),
            array('id'=>105,'name'=>'Ali Pur Chatta','hub'=>0,'hub_id'=>251,'pickup'=>1,'status'=>1),
            array('id'=>106,'name'=>'Arifwala','hub'=>0,'hub_id'=>251,'pickup'=>1,'status'=>1),
            array('id'=>107,'name'=>'Attock','hub'=>0,'hub_id'=>251,'pickup'=>1,'status'=>1),
            array('id'=>108,'name'=>'Badin','hub'=>0,'hub_id'=>251,'pickup'=>1,'status'=>1),
            array('id'=>109,'name'=>'Bahawalnagar','hub'=>0,'hub_id'=>251,'pickup'=>1,'status'=>1),
            array('id'=>110,'name'=>'Bahawalpur','hub'=>1,'hub_id'=>110,'pickup'=>1,'status'=>1),
            array('id'=>202,'name'=>'Karachi','hub'=>1,'hub_id'=>202,'pickup'=>1,'status'=>1),
            array('id'=>174,'name'=>'Islamabad','hub'=>1,'hub_id'=>174,'pickup'=>1,'status'=>1),
            array('id'=>223,'name'=>'Lahore','hub'=>1,'hub_id'=>223,'pickup'=>1,'status'=>1),
            array('id'=>251,'name'=>'Multan','hub'=>1,'hub_id'=>251,'pickup'=>1,'status'=>1),

        ));

        DB::table('city_deliveries')->truncate();
        DB::table('city_deliveries')->insert(array(
            array('city_id'=>1,'booking_type_id'=>1,'shipping_mode_id'=>1),
            array('city_id'=>1,'booking_type_id'=>1,'shipping_mode_id'=>2),
            array('city_id'=>1,'booking_type_id'=>1,'shipping_mode_id'=>3),
            array('city_id'=>1,'booking_type_id'=>1,'shipping_mode_id'=>4),
            array('city_id'=>101,'booking_type_id'=>1,'shipping_mode_id'=>1),
            array('city_id'=>101,'booking_type_id'=>2,'shipping_mode_id'=>2),
            array('city_id'=>101,'booking_type_id'=>3,'shipping_mode_id'=>1),
            array('city_id'=>101,'booking_type_id'=>4,'shipping_mode_id'=>1),
            array('city_id'=>102,'booking_type_id'=>1,'shipping_mode_id'=>1),
            array('city_id'=>102,'booking_type_id'=>2,'shipping_mode_id'=>2),
            array('city_id'=>103,'booking_type_id'=>1,'shipping_mode_id'=>1),
            array('city_id'=>104,'booking_type_id'=>1,'shipping_mode_id'=>1),
            array('city_id'=>105,'booking_type_id'=>1,'shipping_mode_id'=>1),
            array('city_id'=>106,'booking_type_id'=>1,'shipping_mode_id'=>1),
            array('city_id'=>107,'booking_type_id'=>1,'shipping_mode_id'=>1),
            array('city_id'=>108,'booking_type_id'=>1,'shipping_mode_id'=>1),
            array('city_id'=>109,'booking_type_id'=>1,'shipping_mode_id'=>1),
            array('city_id'=>110,'booking_type_id'=>1,'shipping_mode_id'=>1),
            array('city_id'=>202,'booking_type_id'=>1,'shipping_mode_id'=>1),
            array('city_id'=>174,'booking_type_id'=>1,'shipping_mode_id'=>1),
            array('city_id'=>223,'booking_type_id'=>1,'shipping_mode_id'=>1),
            array('city_id'=>251,'booking_type_id'=>1,'shipping_mode_id'=>1),
        ));
    }
}
