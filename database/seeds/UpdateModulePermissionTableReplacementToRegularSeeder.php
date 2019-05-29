<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableReplacementToRegularSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 206, 'name' => 'Replacement Not Collected', 'module_id' => 6),
            array('id' => 207, 'name' => 'Replacement Collected', 'module_id' => 6),
            array('id' => 208, 'name' => 'Replacement to Regular logs', 'module_id' => 6)
        ));
    }
}
