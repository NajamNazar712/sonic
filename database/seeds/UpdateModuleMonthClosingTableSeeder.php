<?php

use Illuminate\Database\Seeder;

class UpdateModuleMonthClosingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert(array(
            array('id' =>15, 'name' => 'Month Closing'),
        ));
    }
}
