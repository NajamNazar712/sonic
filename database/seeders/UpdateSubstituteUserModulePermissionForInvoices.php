<?php

use Illuminate\Database\Seeder;

class UpdateSubstituteUserModulePermissionForInvoices extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('substitute_user_module_permissions')->insert(array(
            array('id' => 14, 'name' => 'Finance Payments - Invoices')
        ));
    }
}
