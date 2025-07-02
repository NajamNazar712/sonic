<?php

namespace App\Console\Commands;

use App\Http\Controllers\CRM\CRMCommentController;
use App\Http\Models\CRM\CrmRequest;
use Illuminate\Console\Command;

class DailyAutoCommentForCRMClaims extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comment:dailycrmclaimshipments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Comment For Claim';

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
        //Claim
        $crm_requests = CrmRequest::where('status_id', 2)->where('case_nature_id', 4);
        if ($crm_requests->exists()) {
            $crm_requests = $crm_requests->get();

            $comment = 'Dear Customer,
We are investigating the subject case and will get back to you as soon as possible. Your patience in this regard is highly appreciated. For any further clarification please approach us.
                        
UAN# 021-111-11-8729
WhatsApp # 0348-111-8729
info@slgtrax.com
Live Chat Messenger
                        
Regards,
TRAX-Customer Experience';
            $internal_comment = 'Dear Team,
                                 Please conclude this case on priority.';
            foreach ($crm_requests as $crm_request) {
                CRMCommentController::add($crm_request->id, 306, 0, 0, $comment, 0, 0);
                CRMCommentController::add($crm_request->id, 306, 0, 1, $internal_comment, 0, 0);
            }
        }


    }
}