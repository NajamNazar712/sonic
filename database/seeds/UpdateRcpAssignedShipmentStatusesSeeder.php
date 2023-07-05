<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateRcpAssignedShipmentStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('rcp_assigned_shipment_statuses')
        ->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Re-attempt - Request')
        );
    }
}
