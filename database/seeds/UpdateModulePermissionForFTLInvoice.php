<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForFTLInvoice extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 509, 'name' => 'FTL Invoice - View', 'module_id' => 8),
            array('id' => 510, 'name' => 'FTL Invoice - Receive', 'module_id' => 8)
        ));
    }
}
