<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForReturnReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
           array('id' => 333, 'name' => 'Return Reason - View', 'module_id' => 14),
        ));
    }
}
