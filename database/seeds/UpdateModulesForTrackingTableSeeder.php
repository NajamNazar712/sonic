<?php

use Illuminate\Database\Seeder;

class UpdateModulesForTrackingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert(array(
            array('id' => 19, 'name' => 'Tracking')
        ));
    }
}
