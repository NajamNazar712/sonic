<?php

use Illuminate\Database\Seeder;

class PettyCashModulePermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 145, 'name' => 'Make Petty Cash - View', 'module_id' => 8),
            array('id' => 146, 'name' => 'Petty Cash Statements - View', 'module_id' => 8),
            array('id' => 147, 'name' => 'Edit Petty Cash Statements - View', 'module_id' => 8),
        ));
    }
}
