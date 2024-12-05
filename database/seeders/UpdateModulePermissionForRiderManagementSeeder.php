<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRiderManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 377, 'name' => 'Rider Permanent - View', 'module_id' => 12),
            array('id' => 378, 'name' => 'Rider Incentive - View', 'module_id' => 12),
            array('id' => 379, 'name' => 'Rider Blacklist - View', 'module_id' => 12),
            array('id' => 380, 'name' => 'Rider SMS History - View', 'module_id' => 12),
            array('id' => 381, 'name' => 'Rider Management Permanent/Incentive - Action', 'module_id' => 12),
            array('id' => 382, 'name' => 'Rider Management - Blacklist', 'module_id' => 12),
            array('id' => 383, 'name' => 'Rider Management SMS - Action', 'module_id' => 12),
        ));
    }
}
