<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRiderRequest extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 425, 'name' => 'Rider Request - View', 'module_id' => 12),
            array('id' => 426, 'name' => 'Rider Request - Approve', 'module_id' => 12),
        ));
    }
}
