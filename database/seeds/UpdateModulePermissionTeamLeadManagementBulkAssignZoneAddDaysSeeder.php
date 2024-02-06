<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTeamLeadManagementBulkAssignZoneAddDaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 925, 'name' => 'Bulk Assign Zone For Agent', 'module_id' => 25),
            array('id' => 926, 'name' => 'Bulk Add Days For Agent', 'module_id' => 25),
            array('id' => 931, 'name' => 'Assign Zone For Agent - Action', 'module_id' => 25),
            array('id' => 928, 'name' => 'Add Days For Agent - Action', 'module_id' => 25),
            array('id' => 929, 'name' => 'Deactivate Staff For Agent - Action', 'module_id' => 25),
        ));
    }
}
