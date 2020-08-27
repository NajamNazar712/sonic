<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReversePickupSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'summary:reversepickup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reverse Pickup Summary';

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
        $start_date = Carbon::yesterday()->startOfDay()->toDateTimeString();
        $end_date = Carbon::yesterday()->endOfDay()->toDateTimeString();
        $response = AdminReportsEmailController::reverse_pickup_summary($start_date, $end_date);
    }
}
