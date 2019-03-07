<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableAutoInvoiceGenerationAndDueDate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('module_permissions')->insert(array(
            array('id' => 171, 'name' => 'Auto-Invoice Generation & Due Date Length', 'module_id' => 14)
        ));
    }
}
