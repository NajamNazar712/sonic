<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FingaApiLog;
use App\Jobs\ShipmentStatusSharingWithWallet;

class FailedStatusRePushToWallet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'status:re-push-wallet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $records = FingaApiLog::whereIn('status', ['error', 'exception'])
            ->where('nature', 'shipment-status-response')
            ->whereNotNull('request_id')
            ->orderby('shipment_id', 'desc')
            ->get();

        foreach ($records as $record) {
            $request = FingaApiLog::where('request_id', $record->request_id)->first();
            if (!empty($request)) {
                $data = [
                    'payload' => json_decode($request->details),
                    'shipment_id' => $request->shipment_id
                ];
                ShipmentStatusSharingWithWallet::dispatch($data, 2);

                FingaApiLog::whereIn('id', [$record->id,$request->id])->delete();
            }
        }
        return 1;
    }
}
