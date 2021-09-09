<?php

use Illuminate\Database\Seeder;

class UpdateSubstituteUserModulePermissionForWeightReconcilationReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('substitute_user_module_permissions')->insert(array(
            array('id' => 16, 'name' => 'Weight Reconciliation Report')
        ));
    }
}
