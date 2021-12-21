<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForQAEvaluation extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 648, 'name' => 'QA Evaluation - ADD', 'module_id' => 31)
        ));
    }
}
