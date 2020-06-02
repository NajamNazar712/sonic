<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\NotificationsController;

class CancelledPickupRequestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pickuprequest:cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancelled Pickup Request';

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
        $date = \Carbon\Carbon::yesterday()->format('Y-m-d');
        NotificationsController::send(63,$date, 0);
    }
}
