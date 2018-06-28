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
    }
}
