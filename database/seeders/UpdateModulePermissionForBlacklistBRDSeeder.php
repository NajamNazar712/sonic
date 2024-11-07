<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForBlacklistBRDSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 329, 'name' => 'Blacklist - Category', 'module_id' => 14),
            array('id' => 330, 'name' => 'Blacklist - Consignee Search', 'module_id' => 14),
        ));
    }
}
