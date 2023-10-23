<?php

use Illuminate\Database\Seeder;

class UpdatePermissionOrdinaryDiscrepancyReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 908, 'name' => 'Ordinary Discrepancy Report - Track', 'module_id' => 9),
        ));
    }
}
