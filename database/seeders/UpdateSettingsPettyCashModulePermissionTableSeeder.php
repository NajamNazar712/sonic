<?php

use Illuminate\Database\Seeder;

class UpdateSettingsPettyCashModulePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 157, 'name' => 'Petty Cash Heads - View', 'module_id' => 14),
            array('id' => 158, 'name' => 'Petty Cash Titles - View', 'module_id' => 14),
            array('id' => 159, 'name' => 'Petty Cash Heads - Add', 'module_id' => 14),
            array('id' => 160, 'name' => 'Petty Cash Heads - Edit', 'module_id' => 14),
            array('id' => 161, 'name' => 'Petty Cash Heads - Active', 'module_id' => 14),
            array('id' => 162, 'name' => 'Petty Cash Heads - Inactive', 'module_id' => 14),
            array('id' => 163, 'name' => 'Petty Cash Titles - Add', 'module_id' => 14),
            array('id' => 164, 'name' => 'Petty Cash Titles - Edit', 'module_id' => 14),
            array('id' => 165, 'name' => 'Petty Cash Titles - Active', 'module_id' => 14),
            array('id' => 166, 'name' => 'Petty Cash Titles - Inactive', 'module_id' => 14),
        ));
    }
}
