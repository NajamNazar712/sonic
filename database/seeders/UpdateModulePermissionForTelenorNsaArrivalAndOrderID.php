<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionForTelenorNsaArrivalAndOrderID extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 421, 'name' => 'Telenor Bulk Arrival - View', 'module_id' => 3),
            array('id' => 422, 'name' => 'Telenor Bulk Order ID - View', 'module_id' => 17),
        ));
    }
}
