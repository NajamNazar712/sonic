<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddTerritorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('territories')->truncate();

        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        $city_ids = array(202,202,202,202,202,202,202,202,202,202,202,202,223,223,223,223,223,223,223,174,174,174,174,174,288,288,288,288,288,144,144,144,144,144,144,251,251,251,251,271,271,283);


        $territory = array('Tariq Road','Malir','ii Chundirgarh','Johar','Korangi' ,'Saddar Karachi','PECHS','Head Office','DHA','Nazimabad','Gulshan','FB Area',
            'Ferozpur Road','Old Lahore','Riwand Road','Multan Road','Defence','Johar Town','Gulberg',
            'Khanna Pull','Kashmir Highway','Blue Area','IJP Road','Bahria town',
            'Khanna Pull','Airport Road','Murree Road','IJP Road','Bahria town',
            'F1','F2','F3','Canal Road','City Area','Millat road',
            'INSIDE ROUTE','CANTT','KETCHERY','GULGASHT',
            'University Road','Main City',
            'Quetta'
            
            );

        for($i=0;$i<count($city_ids);$i++) {

            DB::table('territories')->insert(array(
                array('city_id' => $city_ids[$i], 'name' => $territory[$i],'created_at'=> $timestamp,'updated_at'=>$timestamp),
            ));
        }

    }
}
