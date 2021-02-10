<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionFormMonthClosingResolvedReattemptTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 442, 'name' => 'Month Closing Resolved - Reattempt', 'module_id' => 16)
        ));
    }
}
