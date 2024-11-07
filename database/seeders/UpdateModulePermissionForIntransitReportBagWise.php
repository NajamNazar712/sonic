<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForIntransitReportBagWise extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 472, 'name' => 'In Transit Report Bag Wise - View', 'module_id' => 9),
        ));
    }
}
