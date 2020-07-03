<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RateRejectionEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ratesEmail:rejection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rates Rejection Email';

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
        NotificationsController::send(64,0, 0);
    }
}
