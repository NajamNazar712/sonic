<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForInternationalStandardRateUploadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 477, 'name' => 'International Rates Upload - View', 'module_id' => 14)
        ));
    }
}
