<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForInternationalRateSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 438, 'name' => 'International Rates Setting - View', 'module_id' => 14),
        ));
    }
}
