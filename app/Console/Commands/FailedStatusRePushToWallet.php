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

        $records = FingaApiLog::where('status', 'error')
            ->where('nature', 'shipment-status-response')
            ->get();
        
        foreach($records  as $record) {
            $request_id = $record->id - 1;
            $request = FingaApiLog::where('id', $request_id)->first();

            $data = [
                'payload' => json_decode( $request->details),
                'shipment_id' => $request->shipment_id
            ];
            ShipmentStatusSharingWithWallet::dispatch($data, 2);
        }
        return Command::SUCCESS;
    }
}
