<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionPettyCashRejectedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 243, 'name' => 'Petty Cash Rejected - View', 'module_id' => 14),
        ));
    }
}
