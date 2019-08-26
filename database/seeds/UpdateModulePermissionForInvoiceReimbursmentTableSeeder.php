<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForInvoiceReimbursmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 232, 'name' => 'Invoice for Reimbursement - View', 'module_id' => 8),
        ));
    }
}
