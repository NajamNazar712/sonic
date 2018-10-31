<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\NotificationsController;

class ReturnConfirmationPendingEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:returnconfirmationpending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Return Confirmation Pending Email';

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
        echo NotificationsController::send(23, 0);
    }
}
