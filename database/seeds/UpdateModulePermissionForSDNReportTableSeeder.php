<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForSDNReportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 252, 'name' => 'Station Deposit Notes', 'module_id' => 9)
        ));
    }
}
