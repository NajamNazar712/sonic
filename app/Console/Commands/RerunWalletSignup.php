<?php

namespace App\Console\Commands;

use App\Jobs\WalletSignUpLPendingRecordLogs;
use Illuminate\Console\Command;

class RerunWalletSignup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rerun:wallet-signup {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-run WalletSignUpLPendingRecordLogs job for a specific ID';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get the ID from the command argument
        $id = $this->argument('id');
        WalletSignUpLPendingRecordLogs::dispatch($id);
        return 0;
    }
}
