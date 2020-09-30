<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Controllers\NotificationsController;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class OutstandingSDNReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:outstanding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Outstanding SDN Report';

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
        $date = Carbon::yesterday()->format('Y-m-d');
        $response = AdminReportsEmailController::outstanding_sdn($date . ' 00:00:00');
        NotificationsController::send(90, $date, $response);
    }
}
