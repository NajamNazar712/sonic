<?php

use Illuminate\Database\Seeder;

class AddFintechChargesPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 851, 'name' => 'Fintech Companies', 'module_id' => 14),
            array('id' => 852, 'name' => 'Add Fintech Company', 'module_id' => 14),
            array('id' => 853, 'name' => 'Edit Fintech Company Charges', 'module_id' => 14),
            array('id' => 854, 'name' => 'Add Standard Fintech Charges', 'module_id' => 14),
            array('id' => 855, 'name' => 'Pending-Add Fintech Charges', 'module_id' => 2),
            array('id' => 856, 'name' => 'Active-Add Fintech Charges', 'module_id' => 2),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 646, 'screen_name' => 'Fintech Charges', 'action'=> 'View'),
            array('id' => 647, 'screen_name' => 'Fintech Company', 'action'=> 'Add'),
            array('id' => 648, 'screen_name' => 'Fintech Company Charges', 'action'=> 'Edit'),
            array('id' => 649, 'screen_name' => 'Standard Fintech Charges', 'action'=> 'Add'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Financials > Add Fintech Charges', 'url'=>'admin.settings.fintech_company_charges.index', 'permission_id' => 851),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Financials > Add Standard Fintech Charges', 'url'=>'admin.settings.standard_fintech_charges.index', 'permission_id' => 852),
        ));
    }
}
