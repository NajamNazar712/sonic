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
            array('id' => 648, 'name' => 'QA Evaluation - Report', 'module_id' => 31),
            array('id' => 654, 'name' => 'QA Evaluation - Add/Edit', 'module_id' => 31),
            array('id' => 655, 'name' => 'QA Evaluation - View', 'module_id' => 31),
            array('id' => 656, 'name' => 'QA Evaluation - Edit Activity', 'module_id' => 14),
        ));
    }
}
