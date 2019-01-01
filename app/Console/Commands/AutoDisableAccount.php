<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\ShipmentActiveAccountController;
use Illuminate\Console\Command;

class AutoDisableAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:disable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Accounts Auto Disable';

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
        ShipmentActiveAccountController::disable();
    }
}
