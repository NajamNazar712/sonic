<?php

namespace App\Console\Commands;

use App\Jobs\WalletSettlementFromDonePayments;
use Illuminate\Console\Command;

class RerunWalletSettlement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rerun_wallet_settlement';

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
        WalletSettlementFromDonePayments::dispatchNow(1563023,  346);
    }
}
