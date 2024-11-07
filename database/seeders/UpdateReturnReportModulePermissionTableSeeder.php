<?php

use Illuminate\Database\Seeder;

class UpdateReturnReportModulePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 174, 'name' => 'Return Re-Attempt Ratio', 'module_id' => 9)
        ));
    }
}
