<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\Admins\GlobalSettingsController;

class PendingCashCollectionReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pendingCashCollection:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pending Cash Collection Report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        GlobalSettingsController::insertPendingCashCollectionData();
    }
}
