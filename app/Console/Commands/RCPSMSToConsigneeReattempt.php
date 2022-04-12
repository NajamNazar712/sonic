<?php

namespace App\Console\Commands;

use App\Http\Models\Admin\GlobalSettings;
use App\Jobs\RCPSmsToConsignee;
use App\ReturnConfirmationPendingSmsAttempt;
use Illuminate\Console\Command;

class RCPSMSToConsigneeReattempt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:rcp_sms_to_consignee_reattempt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminder For RCP Sms to Consignee';

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
        $rcp_sms = ReturnConfirmationPendingSmsAttempt::where('status',0);
        if($rcp_sms->exists()){
            $rcp_sms = $rcp_sms->get();
            $limit = GlobalSettings::where('type','return_confirmation_pending_sms')->first();


            foreach($rcp_sms as $rcp) {
                if ($rcp->count <= $limit->text){
                    dispatch(new RCPSmsToConsignee($rcp->shipment_id));
                }
            }
        }
    }
}
