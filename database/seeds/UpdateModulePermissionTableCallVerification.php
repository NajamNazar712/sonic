<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableCallVerification extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('module_permissions')->insert(array(
            array('id' => 153, 'name' => 'Call Verification', 'module_id' => 9)
        ));
    }
}
