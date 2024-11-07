<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForPettyCashReportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 156, 'name' => 'Petty Cash Statement', 'module_id' => 9),
        ));
    }
}
