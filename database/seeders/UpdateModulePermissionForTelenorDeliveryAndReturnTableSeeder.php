<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionForTelenorDeliveryAndReturnTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 399, 'name' => 'Telenor Bulk Delivered - View', 'module_id' => 6),
            array('id' => 400, 'name' => 'Telenor Return Update - View', 'module_id' => 7)
        ));
    }
}
