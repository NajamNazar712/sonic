<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionsTableUpdateInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $module_permission = ModulePermission::find(120);
        $module_permission->name = 'Invoices - View';
        $module_permission->save();

        $module_permission = ModulePermission::find(121);
        $module_permission->name = 'Invoices - Email Reminder';
        $module_permission->save();

        $module_permission = ModulePermission::find(122);
        $module_permission->name = 'Invoices - Mark as Received';
        $module_permission->save();
    }
}
