<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\V2Pickup\V2AdminReportController;
use Illuminate\Console\Command;

class V2PickupCleanDuplicateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pickup:cleandata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        V2AdminReportController::clean_data();

    }
}
