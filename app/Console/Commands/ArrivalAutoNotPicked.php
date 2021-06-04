<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\V2Pickup\V2PickupCronController;
use Illuminate\Console\Command;

class ArrivalAutoNotPicked extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'arrival:autonotpicked';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Arrival Auto Not Picked Cron';

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
        V2PickupCronController::ready_pickups();
        V2PickupCronController::arrival_not_picked();
        V2PickupCronController::auto_pickup_assign();
    }
}
