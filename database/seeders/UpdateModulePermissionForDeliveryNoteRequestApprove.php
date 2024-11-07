<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForDeliveryNoteRequestApprove extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 531, 'name' => 'Delivery Note Request - View', 'module_id' => 6),
            array('id' => 533, 'name' => 'Delivery Note Request Approve - Action', 'module_id' => 6)
        ));
    }
}
