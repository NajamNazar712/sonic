<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRDTSEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 269, 'name' => 'Return Delivered To Shipper Email - View', 'module_id' => 14),
        ));
    }
}
