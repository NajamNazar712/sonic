<?php

namespace App\Console\Commands;

use App\Http\Models\Admin\Admin;
use Illuminate\Console\Command;

class PasswordUpdateForAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Reset:AdminPasswordMonthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Admin Password Reset Monthly';

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
        Admin::where('first_login','>','0')->Update(['first_login' => '0']);
    }
}