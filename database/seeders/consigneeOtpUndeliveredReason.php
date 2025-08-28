<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class consigneeOtpUndeliveredReason extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert([
            [
                'id' => 249,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Consignee OTP for Undelivered Reason',
                'type_id' => 2,
                'subject' => null,
                'body' => 'Dear [consignee_name] Trax rider [rider_name] generated an OTP for Shipment [tracking_number].OTP : [otp]',
                'updated_by' => 3495,
                'status' => 1
            ]
        ]);
    }
}
