<?php

use Illuminate\Database\Seeder;

class SeederMaazSprint84ForEmployeeDirectory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 652, 'name' => 'Employee Directory - HR', 'module_id' => 28),
        ));
    }
}
