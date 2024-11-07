<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForZeroReportSetting extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 357, 'name' => 'Zero Charges Report Settings', 'module_id' => 14),
        ));
    }
}
