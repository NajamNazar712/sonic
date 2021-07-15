<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\NotificationsController;
class InactiveRiderReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:inactiveriderreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inactive Rider For Three or More Report';

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
        $date = \Carbon\Carbon::now()->subDays(3);
        
        NotificationsController::send(140,$date,0);
        
    }
}
