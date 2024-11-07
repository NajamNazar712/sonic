<?php

use Illuminate\Database\Seeder;

class Seeder4243PermissionActivityTrail extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 625, 'name' => 'Reimbursement Invoice - View', 'module_id' => 8),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 472, 'screen_name' => 'Reimbursement Invoice', 'action'=> 'View'),
            array('id' => 473, 'screen_name' => 'Reimbursement Invoice', 'action'=> 'Excel Download'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Financials > COD Payments > Reimbursement Invoice', 'url'=>'admin.finance.invoices.reimbursement.index', 'permission_id' => 625));

    }
}
