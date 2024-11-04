<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ServiceLedgerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 813, 'screen_name' => 'Service Ledger - View', 'action'=> 'View')
        ));
        DB::table('module_permissions')->insert(array(
            array('id' => 1017, 'name' => 'Service Ledger - View - View', 'module_id' => 8),

        ));
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Service Ledger - View', 'url'=>'admin.finance.shipment_ledger.index', 'permission_id' => 1017)
        );
    }
}
