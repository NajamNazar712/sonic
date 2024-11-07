<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionFormMonthClosingReturnConfirmStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 587, 'name' => 'Month Closing Resolved - Return Confirm', 'module_id' => 16)
        ));
    }
}
