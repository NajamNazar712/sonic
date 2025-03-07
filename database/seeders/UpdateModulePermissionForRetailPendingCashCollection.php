<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateModulePermissionForRetailPendingCashCollection extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 423, 'name' => 'Pending Cash Collection Retail - View', 'module_id' => 26)
        ));
    }
}
