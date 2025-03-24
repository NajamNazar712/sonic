<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StatusSharingWithWallet;
use App\Jobs\BulkStatusSharingWithWalletJob;

class BulkStatusSharingWithWallet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:status-sharing-wallet';

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

        $data = StatusSharingWithWallet::
              join('shipments','shipments.id','status_sharing_with_wallets.shipment_id')
              ->join('wallet_users','wallet_users.user_id','shipments.user_id')
                ->leftjoin('finja_log_settlement_records','shipments.id','finja_log_settlement_records.shipment_id')
            ->where('status_sharing_with_wallets.is_send', 0)
            ->groupBy('status_sharing_with_wallets.shipment_id')
            ->select(['shipments.*','status_sharing_with_wallets.shipment_id','status_sharing_with_wallets.status_id','finja_log_settlement_records.logged_cod_charges','wallet_users.wallet_id'])
            ->where('shipments.id',49912349)
            ->get();
        $data->chunk(100)->each(function ($chunkedData){
            BulkStatusSharingWithWalletJob::dispatch($chunkedData->toArray());
        });

    }   
}
