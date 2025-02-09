<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FingaApiLog;

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

        $record = FingaApiLog::where('status', 'error')
            ->where('nature', 'shipment-status-response')
            ->get();
            
        return Command::SUCCESS;
    }
}
