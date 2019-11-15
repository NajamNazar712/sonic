<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\NotificationsController;

class ReturnDeliveredToShipperEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:returndeliveredtoshipper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consolidated Email Return Delivered To Shipper';

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
        NotificationsController::send(39, 0);
    }
}
