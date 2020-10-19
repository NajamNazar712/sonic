<?php

namespace App\Console\Commands;

use App\http\Models\Admin\KeyAccountPendingCrm;
use App\http\Models\Admin\KeyAccountPendingSummaryCrm;
use App\Http\Models\CRM\CrmRequest;
use Illuminate\Console\Command;

class KeyAccountDashboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keyaccount:dashboard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update counts of pending crm requests';

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
        $pending_crm_request = KeyAccountPendingCrm::get();
        if($pending_crm_request){
            foreach ($pending_crm_request as $crm_request_pending){
                $crm_request = CrmRequest::find($crm_request_pending->crm_request_id);
                if($crm_request->status == 4){
                    $crm_request_summary = KeyAccountPendingSummaryCrm::where('case_nature_type_id', $crm_request->case_nature_type_id)->where('admin_id', $crm_request_pending->admin_id);
                    if($crm_request_summary->exists()){
                        $crm_request_summary->first();
                        $tat = $crm_request_summary->tat;
                        $count = $crm_request_summary->count;
                        $new_count = $crm_request_summary->count - 1;
                        $new_tat = (($tat * $count) / $new_count);

                        $crm_request_summary->count = $new_count;
                        $crm_request_summary->tat = $new_tat;
                        $crm_request_summary->save();
                    }
                    KeyAccountPendingCrm::where('crm_request_id', $crm_request_pending->crm_request_id)->where('admin_id', $crm_request_pending->admin_id)->delete();
                }
            }
        }
    }
}
