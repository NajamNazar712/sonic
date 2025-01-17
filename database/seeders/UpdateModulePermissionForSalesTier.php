<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionForSalesTier extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('module_permissions')->insert(array(
            array('id' => 331, 'name' => 'Sales Tier', 'module_id' => 14)
        ));
    }
}
