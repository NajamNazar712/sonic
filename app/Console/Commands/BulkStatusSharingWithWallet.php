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

        $data = StatusSharingWithWallet::where('is_send', 0)->groupBy('shipment_id')->get();
        $data->chunk(100)->each(function ($chunkedData){
            BulkStatusSharingWithWalletJob::dispatch($chunkedData->toArray());
        });

    }   
}
