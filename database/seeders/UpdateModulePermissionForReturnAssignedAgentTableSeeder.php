<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionForReturnAssignedAgentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 316, 'name' => 'Confirmation Pending - Assign Agent', 'module_id' => 7),
            array('id' => 317, 'name' => 'Confirmation Pending - View Assigned Shipments', 'module_id' => 7),
        ));
    }
}
