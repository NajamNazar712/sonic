<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;

class PermissionForPaymentRequisition extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('module_permissions')->insert([
            ['id' => 1046, 'name' => 'Payment Requisition - Form', 'module_id' => 33],
            ['id' => 1047, 'name' => 'Payment Requisition - List', 'module_id' => 33],
            ['id' => 1048, 'name' => 'Payment Requisition - Add Cheque Number', 'module_id' => 33],
            ['id' => 1049, 'name' => 'Payment Requisition - Completed/Done', 'module_id' => 33],
            ['id' => 1050, 'name' => 'Payment Requisition - Completed/Done List', 'module_id' => 33],

            
        ]);

        // // if new screen or excel
        // DB::table('activity_trail_actions')->insert([
        //     ['id' => 785, 'screen_name' => 'Barcode Generator', 'action' => 'View'],
        //     ['id' => 786, 'screen_name' => 'Barcode Generator', 'action' => 'Submit'],
        // ]);

        // if new screen
        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Support > Payment Requisition > Form',
                'url' => 'admin.finance.prf.index',
                'permission_id' => 1046
            ],

            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Support > Payment Requisition > List',
                'url' => 'admin.finance.prf.index',
                'permission_id' => 1047
            ],
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Support > Payment Requisition > Completed/Cancelled List',
                'url' => 'admin.finance.prf.completed_list',
                'permission_id' => 1050
            ]
        ]);
    }
}
