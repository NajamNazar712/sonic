<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\V2Pickup\V2PickupCronController;
use Illuminate\Console\Command;

class PickupRegenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pickup:regenerate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pickup regenerate if partial received';

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
        V2PickupCronController::pickup_re_generate();

    }
}
