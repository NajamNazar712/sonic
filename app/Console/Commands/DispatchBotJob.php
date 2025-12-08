<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Jobs\BotCallDispatch;
use App\Jobs\BotCallDispatchSecod;
use App\Jobs\BotCallDispatchThird;
use Carbon\Carbon;

class DispatchBotJob extends Command
{
    protected $signature = 'bot:dispatch';
    protected $description = 'Dispatch bot call job based on API call logs';

    public function handle()
    {
        $yesterday = Carbon::now()->subDay()->format('Y-m-d');

        // Fetch matching API call logs
        $logs = DB::table('api_call_logs')
            // ->whereDate('created_at', '>=', "2025-12-04")
            // ->where('channel_name', 'zong')
            // ->where('payload', 'like', '%"message":"Please authenticate"%')
            ->distinct()
            ->where('shipment_id', 57577478)
            ->select('shipment_id', 'channel_name', 'created_at', 'call_count_initiate', 'payload')
            ->get();
        $callCount = $logs->count();

        if ($callCount == 0) {
            $this->info('No jobs to dispatch.');
            return 0;
        }

        // Dispatch job based on call count
        foreach ($logs as $log) {

            // Extract shipment_id from payload (assuming JSON structure)
            $shipmentId = $log->shipment_id ?? null;

            if (!$shipmentId) continue; // skip if no shipment_id

            switch ($log->call_count_initiate) {
                case 1:
                    BotCallDispatch::dispatch($shipmentId);
                    $this->info("BotCallDispatch dispatched for shipment_id: {$shipmentId}");
                    break;
                case 2:
                    BotCallDispatchSecod::dispatch($shipmentId);
                    $this->info("BotCallDispatchSecond dispatched for shipment_id: {$shipmentId}");
                    break;
                case 3:
                    BotCallDispatchThird::dispatch($shipmentId);
                    $this->info("BotCallDispatchThird dispatched for shipment_id: {$shipmentId}");
                    break;
                default:
                    $this->info("No matching job for shipment_id: {$shipmentId}");
                    break;
            }
        }

        return 0;
    }
}
