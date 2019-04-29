<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableCxQuickTrackingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 204, 'name' => 'CX Quick Tracking', 'module_id' => 19)
        ));
    }
}
