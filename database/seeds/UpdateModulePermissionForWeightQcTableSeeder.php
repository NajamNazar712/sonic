<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForWeightQcTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 444, 'name' => 'Weight QC - View', 'module_id' => 9)
        ));
    }
}
