<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionForHolidayTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 358, 'name' => 'Holidays', 'module_id' => 14),
            array('id' => 359, 'name' => 'Not Attempted Cron Time', 'module_id' => 14),
            array('id' => 360, 'name' => 'Not Attempted Aging', 'module_id' => 9),
        ));
    }
}
