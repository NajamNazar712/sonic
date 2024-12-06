<?php

use Illuminate\Database\Seeder;

class PermissionSeederForResolvedSdnButton extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('module_permissions')->insert(array(
            array('id' => 1018, 'name' => 'Outstanding Station Deposit Notes - Resolved', 'module_id' => 6)
        ));
    }
}
