<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\DeliveryController;
use Illuminate\Console\Command;

class RiderDeliveryImageArchive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archive:riderdeliveryimage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        DeliveryController::rider_delivery_archive_directory();
    }
}
