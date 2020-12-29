<?php

namespace App\Console\Commands;

use App\Http\Controllers\UserOTPController;
use Illuminate\Console\Command;

class UserOTPVerifiy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:usersotp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify OTP Users';

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
        UserOTPController::verify_users_otp();
    }
}
