<?php

namespace App\Console\Commands;

use App\Http\Controllers\FingaIntegrationController;
use Illuminate\Console\Command;
use App\Models\StatusSharingWithWallet;
use App\Jobs\BulkStatusSharingWithWalletJob;
use Illuminate\Support\Facades\Log;

class BulkStatusSharingWithWalletReplicate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:status-sharing-wallet-replicate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is sharing bulk statuses with wallet';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {

            Log::channel('botCallJobLog')->info('s ' . 'status sharing wallet job initiated');
//        $api = config('app.FINGA_URL');
//        $token = FingaIntegrationController::getToken($api);
            $data = StatusSharingWithWallet::join('shipments as s', 's.id', 'status_sharing_with_wallets.shipment_id')
                ->join('wallet_users as wu', 'wu.user_id', 's.user_id')
                ->leftJoin('finja_log_settlement_records as fls', 's.id', 'fls.shipment_id')
                ->where('status_sharing_with_wallets.is_send', 0)
                ->groupBy('status_sharing_with_wallets.shipment_id')
                ->select([
                    's.*',
                    'status_sharing_with_wallets.shipment_id',
                    'status_sharing_with_wallets.status_id',
                    'fls.logged_cod_charges',
                    'wu.wallet_id'
                ])->get();
            $data->chunk(50)->each(function ($chunkedData){
                
                BulkStatusSharingWithWalletJob::dispatch($chunkedData->toArray());
            });
            Log::channel('botCallJobLog')->info('s ' . 'status sharing wallet job dispatched');
        }catch (\Throwable $e) {
            Log::channel('botCallJobLog')->info('Status sharing job failed: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }   
}
