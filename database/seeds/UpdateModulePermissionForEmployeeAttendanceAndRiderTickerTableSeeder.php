<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForEmployeeAttendanceAndRiderTickerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 465, 'name' => 'Employee Attendance - View', 'module_id' => 11),
            array('id' => 466, 'name' => 'Rider Ticker - View', 'module_id' => 14),
        ));
    }
}
