<?php

namespace App\Console\Commands;

use App\Http\Admins\V3Pickup\V3PickupCronController;
use Illuminate\Console\Command;

class AutoRegularPickup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:regular_pickup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regular Pickup Generation and Auto Assigning';

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
        V3PickupCronController::regular_pickups_create();
    }
}
