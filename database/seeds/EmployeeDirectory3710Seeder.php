<?php

use Illuminate\Database\Seeder;

class EmployeeDirectory3710Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 591, 'name' => 'Staff Management - Enable/Disable', 'module_id' => 12),
        ));
    }
}
