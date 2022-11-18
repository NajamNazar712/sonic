<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Models\Admin\GlobalSettings;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReversePickupSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'summary:reversepickup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reverse Pickup Summary';

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
        $settings = GlobalSettings::where('type', 'pickup_request_cut_off_time');

        if ($settings->exists()) {
            $settings = $settings->first();
            if((int)$settings->setting_value < 10){
                $cut_off_time = '0' . $settings->setting_value . ':00';
            }
            else{
                $cut_off_time = $settings->setting_value . ':00';
            }
            $start_date = Carbon::today()->subDay(1)->format('Y-m-d');
            $start_date = $start_date . ' ' . $cut_off_time;
            $end_date = Carbon::today()->format('Y-m-d');
            $end_date = $end_date . ' ' . $cut_off_time;
            AdminReportsEmailController::reverse_pickup_summary($start_date, $end_date);
        }
    }
}
