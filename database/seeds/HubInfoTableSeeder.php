<?php

use Illuminate\Database\Seeder;

class HubInfoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('hub_infos')->truncate();
        DB::table('hub_infos')->insert(array(
            array('id'=>10,'hub_name'=>'Karachi'),
            array('id'=>11,'hub_name'=>'Hyderabad'),
            array('id'=>12,'hub_name'=>'Sukhur'),
        ));
    }
}
