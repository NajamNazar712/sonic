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
            array('name'=>'Abbottabad','hub'=>1,'hub_id'=>31,'pickup'=>1,'status'=>1),
            array('name'=>'Abdul Hakim','hub'=>0,'hub_id'=>null,'pickup'=>1,'status'=>1),
            array('name'=>'Ahmed Pur East','hub'=>0,'hub_id'=>null,'pickup'=>1,'status'=>1),
            array('name'=>'Alipur','hub'=>0,'hub_id'=>null,'pickup'=>1,'status'=>1),
            array('name'=>'Ali Pur Chatta','hub'=>0,'hub_id'=>null,'pickup'=>1,'status'=>1),
            array('name'=>'Arifwala','hub'=>0,'hub_id'=>null,'pickup'=>1,'status'=>1),
            array('name'=>'Attock','hub'=>0,'hub_id'=>null,'pickup'=>1,'status'=>1),
            array('name'=>'Badin','hub'=>0,'hub_id'=>null,'pickup'=>1,'status'=>1),
            array('name'=>'Bahawalnagar','hub'=>0,'hub_id'=>null,'pickup'=>1,'status'=>1),
            array('name'=>'Bahawalpur','hub'=>0,'hub_id'=>null,'pickup'=>1,'status'=>1),
            array('name'=>'Karachi','hub'=>1,'hub_id'=>10,'pickup'=>1,'status'=>1),
            array('name'=>'Islamabad','hub'=>0,'hub_id'=>null,'pickup'=>1,'status'=>1),
            array('name'=>'Lahore','hub'=>1,'hub_id'=>21,'pickup'=>1,'status'=>1),

        ));
    }
}
