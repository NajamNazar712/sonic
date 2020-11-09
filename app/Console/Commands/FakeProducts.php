<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AccountBlockageEmailDraftController;
use App\Http\Controllers\NotificationsController;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class FakeProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:FakeProducts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'FakeProducts';

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
        $date = Carbon::today();
        $response = AccountBlockageEmailDraftController::fake_product($date);
        NotificationsController::send(94, $date, $response);
    }
}
