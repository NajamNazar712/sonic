<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateModulePermissionForRetailAccount extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 428, 'name' => 'Retail Accounts - View', 'module_id' => 2),
            array('id' => 429, 'name' => 'Retail Accounts - Action', 'module_id' => 26),

        ));
    }
}
