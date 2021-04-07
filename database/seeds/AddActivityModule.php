<?php

use Illuminate\Database\Seeder;

class AddActivityModule extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert(array(
            array('id' => 27, 'name' => 'Activity'),
        ));
    }
}
