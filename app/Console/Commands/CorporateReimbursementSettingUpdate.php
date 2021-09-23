<?php

namespace App\Console\Commands;

use App\Http\Models\Rates\Corporate\CorporateReimbursementSetting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CorporateReimbursementSettingUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'corporate_reimbursement_setting:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Corporate Reimbursement Setting';

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
        $settings = CorporateReimbursementSetting::where('starting_date',Carbon::now()->firstOfMonth()->toDateString())->get();
        foreach ($settings as $setting)
        {
            $setting->starting_date = null;
            $setting->ending_date = null;
            $setting->setting_on = 1;
            $setting->update();
        }

        $settings = CorporateReimbursementSetting::where('ending_date',Carbon::now()->subMonth()->LastOfMonth()->toDateString())->get();
        foreach ($settings as $setting)
        {
            $setting->starting_date = null;
            $setting->ending_date = null;
            $setting->setting_on = 0;
            $setting->update();
        }
    }
}
