<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForReattemptPercentageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 659, 'name' => 'Re-Attempt Percentage', 'module_id' => 14),
        ));
    }
}
