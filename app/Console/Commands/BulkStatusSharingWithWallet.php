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

        $data = StatusSharingWithWallet::join('shipments as s', 's.id', 'status_sharing_with_wallets.shipment_id')
            ->join('wallet_users as wu', 'wu.user_id', 's.user_id')
            ->leftJoin('finja_log_settlement_records as fls', 's.id', 'fls.shipment_id')
            ->where('status_sharing_with_wallets.is_send', 0)
            ->whereIn('s.id', [
                49411229,
                49836648,
                50153980,
                49485007,
                49571629,
                49611773,
                50116436,
                49989581,
                49551972,
                49498558,
                50090331,
                50034650,
                49768105,
                49614749,
                49915968,
                49702932,
                49991667,
                49942724,
                49992617,
                50156878,
                49273072,
                49411229,
                49768105,
                47827859,
                47827859,
                49273072,
                49532924,
                49915968,
                50153980,
                50034650,
                50116436,
                49571629,
                49604042,
                49684841,
                49948887,
                49942724,
                49529325,
                49836630,
                49492051,
                49492051,
            ]) // Removed duplicates
            ->groupBy('status_sharing_with_wallets.shipment_id')
            ->select([
                's.*',
                'status_sharing_with_wallets.shipment_id',
                'status_sharing_with_wallets.status_id',
                'fls.logged_cod_charges',
                'wu.wallet_id'
            ])
            ->get();
        $data->chunk(100)->each(function ($chunkedData){
            BulkStatusSharingWithWalletJob::dispatchNow($chunkedData->toArray());
        });

    }   
}
