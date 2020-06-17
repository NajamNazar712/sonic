<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\Admins\V2Pickup\V2AdminReportController;

class PickupReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pickup:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pickup Report';

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
        V2AdminReportController::insertReportData();
    }
}
