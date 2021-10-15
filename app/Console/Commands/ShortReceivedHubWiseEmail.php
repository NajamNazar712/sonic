<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\ShortReceiveReportTimeHubWise;
use App\Http\Models\City;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ShortReceivedHubWiseEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:shortreceivedhubwise';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Short Received Report Hub Wise';

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
        $current_hour = Carbon::now()->format('H');
        $default_hub_ids = array();
        $settings = GlobalSettings::where('type', 'short_received_hub_wise_cron');
        if ($settings->exists()) {
            $settings = $settings->first();
            if($current_hour == $settings->setting_value){
                $void_hub_ids = ShortReceiveReportTimeHubWise::pluck('hub_id')->toArray();
                $default_hub_ids = City::whereNotIn('id', $void_hub_ids)->where('hub', 1)->where('status', 1)->pluck('id')->toArray();
            }
        }
        $hub_ids = ShortReceiveReportTimeHubWise::where('time', $current_hour)->pluck('hub_id')->toArray();
        $hub_ids = array_merge($default_hub_ids,$hub_ids);
        if(count($hub_ids) > 0){
            AdminReportsEmailController::short_received_report_hub_wise($hub_ids);
        }
    }
}
