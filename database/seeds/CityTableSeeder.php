<?php

use Illuminate\Database\Seeder;
use App\Http\Models\CityInfo;


class CityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        DB::table('city_infos')->truncate();
        DB::table('city_infos')->insert(array(
            array('city_name'=>'Abbottabad','city_code'=>101,'hub_info_id'=>10),
            array('city_name'=>'Abdul Hakim','city_code'=>102,'hub_info_id'=>10),
            array('city_name'=>'Ahmed Pur East','city_code'=>103,'hub_info_id'=>10),
            array('city_name'=>'Alipur','city_code'=>104,'hub_info_id'=>12),
            array('city_name'=>'Ali Pur Chatta','city_code'=>105,'hub_info_id'=>12),
            array('city_name'=>'Arifwala','city_code'=>106,'hub_info_id'=>12),
            array('city_name'=>'Attock','city_code'=>107,'hub_info_id'=>10),
            array('city_name'=>'Badin','city_code'=>108,'hub_info_id'=>10),
            array('city_name'=>'Bahawalnagar','city_code'=>109,'hub_info_id'=>10),
            array('city_name'=>'Bahawalpur','city_code'=>110,'hub_info_id'=>10),
            array('city_name'=>'Karachi','city_code'=>202,'hub_info_id'=>11),
            array('city_name'=>'Islamabad','city_code'=>174,'hub_info_id'=>11),
            array('city_name'=>'Lahore','city_code'=>223,'hub_info_id'=>11),

        ));

    }
}
