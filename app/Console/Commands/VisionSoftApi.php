<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\VisionSoftAPIController;
use Illuminate\Console\Command;

class VisionSoftApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:visionsoft';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vision Soft API(s)';

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
        // VisionSoftAPIController::login();
        VisionSoftAPIController::customers();
        VisionSoftAPIController::customer_banks();
        VisionSoftAPIController::city_hub();
        VisionSoftAPIController::employees();
        VisionSoftAPIController::cities();
        VisionSoftAPIController::cod_payable();
        VisionSoftAPIController::cod_receivable();
        // VisionSoftAPIController::arrival_revenue();
        VisionSoftAPIController::del_ret_revenue();
        VisionSoftAPIController::bank_deposits();
        VisionSoftAPIController::cod_payment();
        VisionSoftAPIController::cod_payment_clear();
        VisionSoftAPIController::daily_exp();
        VisionSoftAPIController::prc_load_api_data();
    }
}