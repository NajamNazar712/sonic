<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRevertReversionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 249, 'name' => 'Request Status Reversion', 'module_id' => 8)
        ));
    }
}
