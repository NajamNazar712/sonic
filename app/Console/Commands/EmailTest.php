<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EmailTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test';

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
        Log::channel('code_test_log')->info('Noman bhai ka log in logs!');
        echo "Noman bhai ka log!";
//        $ref = 'nothing';
//        NotificationsController::send(219, $ref);
    }
}
