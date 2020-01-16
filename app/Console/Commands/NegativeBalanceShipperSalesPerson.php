<?php

namespace App\Console\Commands;
use App\Http\Controllers\NotificationsController;
use Illuminate\Console\Command;

class NegativeBalanceShipperSalesPerson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:negativebalanceshippersalesperson';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email notification for negative balance to sales person';

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
        NotificationsController::send(56, 0);
    }
}
