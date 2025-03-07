<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionForMultipleSalesTaggingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 279, 'name' => 'Multiple Sales Tagging', 'module_id' => 11)
        ));
    }
}
