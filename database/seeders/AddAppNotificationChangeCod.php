<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddAppNotificationChangeCod extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('app_notifications')->insert(array(
            array('id' => 23, 'name' => 'Shipment Change Cod Amount', 'title' => 'Shipment Change Cod Amount', 'body' => 'COD Amount Change from [old_amount] to [new_amount] of Tracking number is ([tracking_number]), kindly check.', 'app_id' => 1, 'updated_by' => 346, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
