<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ArchiveBookingApiLogs extends Command
{
    protected $signature = 'logs:archive-booking-api';

    protected $description = 'Archive booking API logs using last ID method.';

    public function handle()
    {
        // STEP 1: Fetch last ID
        $lastId = DB::table('booking_api_logs')->max('id');

        if (!$lastId) {
            $this->info("No logs found.");
            return;
        }

        // STEP 2: Insert all logs <= lastId into archive table
        DB::table('booking_api_log_archives')->insertUsing(
            [
                'user_id',
                'shipment_id',
                'endpoint',
                'payload',
                'ip',
                'original_created_at',
                'created_at',
                'updated_at'
            ],
            DB::table('booking_api_logs')
                ->select(
                    'user_id',
                    'shipment_id',
                    'endpoint',
                    'payload',
                    'ip',
                    'created_at as original_created_at',
                    DB::raw('NOW() as created_at'),
                    DB::raw('NOW() as updated_at')
                )
                ->where('id', '<=', $lastId)
        );

        // STEP 3: Delete archived logs
        DB::table('booking_api_logs')
            ->where('id', '<=', $lastId)
            ->delete();

        $this->info("Archived and deleted logs up to ID: $lastId");
    }
}
