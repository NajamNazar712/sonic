<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotPickedShippersSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:notpickedshipperssummary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email for not picked shippers summary';

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
        NotificationsController::send(99, null);
        NotificationsController::send(100, null);
    }
}
