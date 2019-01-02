<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\ShipperAccountController;
use Illuminate\Console\Command;

class AutoDisableShipperAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipper:disable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shipper Accounts Auto Disable';

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
        //
        ShipperAccountController::disable();
    }
}
