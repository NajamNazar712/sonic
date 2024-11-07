<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateModeulePermissionForOmniUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 644, 'name' => 'Omni Users Setting - View', 'module_id' => 14)
        ));

    }
}
