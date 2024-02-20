<?php

namespace App\Console\Commands;

use App\Http\Controllers\admins\CronControllers\SackBagCronController;
use Illuminate\Console\Command;

class SackBagStatusUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sackbag:statusupdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update SackBag Status Yesterday date';

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
        SackBagCronController::sackbag_status_update();
    }
}
