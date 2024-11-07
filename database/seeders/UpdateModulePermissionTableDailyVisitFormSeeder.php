<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableDailyVisitFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 265, 'name' => 'Daily Visit Form - View', 'module_id' => 2)
        ));
    }
}
