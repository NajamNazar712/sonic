<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableInvoice extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 120, 'name' => 'Generate Invoices - View', 'module_id' => 8),
            array('id' => 121, 'name' => 'Generate Invoices - Generate', 'module_id' => 8),
            array('id' => 122, 'name' => 'Invoice History', 'module_id' => 8)
        ));
    }
}
