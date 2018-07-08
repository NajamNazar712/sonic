<?php

use Illuminate\Database\Seeder;

class BanksListTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('banks_lists')->truncate();
        DB::table('banks_lists')->insert(array(
            array('name'=>'Al Baraka Bank (Pakistan) Limited.','code'=>'ABPL','affiliate'=>0,'status'=>1),
            array('name'=>'Allied Bank Limited.','code'=>'ABL','affiliate'=>0,'status'=>1),


        ));
    }
}
