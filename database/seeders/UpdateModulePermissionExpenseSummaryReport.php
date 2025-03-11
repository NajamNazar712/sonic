<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionExpenseSummaryReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 395, 'name' => 'Expense Summary Report', 'module_id' => 9),
        ));
    }
}
