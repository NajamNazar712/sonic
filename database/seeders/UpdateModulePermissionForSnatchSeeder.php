<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForSnatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 837, 'name' => 'Snatch - Action', 'module_id' => 6),
            array('id' => 838, 'name' => 'Deduction - Action', 'module_id' => 6),
        ));
    }
}
