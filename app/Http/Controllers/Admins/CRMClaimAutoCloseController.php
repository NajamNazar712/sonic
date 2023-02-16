<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\CRM\CRMCommentController;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CRMClaimAutoCloseController extends Controller
{
    static public function check_shipment_claims($shipment_id){
        $crm_request = CrmRequest::where('shipment_id', $shipment_id)->where('status_id', 2)->where('case_nature_id', 4);
        if($crm_request->exists()){
            $crm_request = $crm_request->first();
            $crm_request->status_id = 4;
            $crm_request->save();

            CrmRequestStatusHistory::create([
                'crm_request_id' => $crm_request->id,
                'status_id' => 3,
                'agent_id' => 346
            ]);
            CrmRequestStatusHistory::create([
                'crm_request_id' => $crm_request->id,
                'status_id' => 4,
                'agent_id' => 346
            ]);

            $comment = 'Dear Customer,
                        Please accept our sincere apologies for the inconvenience you had, please be noted that adjustments against the subjected tracking have been made as per the policy and your payment will be disbursed with your next transaction. Your patience in this regard is highly appreciated. For any further clarification please approach us.
                        UAN# 021-111-11-8729
                        WhatsApp # 0348-111-8729
                        info@trax.pk
                        Live Chat Messenger.
                        
                        Regards,
                        TRAX-Customer Experience
                        ';

            CRMCommentController::add($crm_request->id, 306, 0, 0, $comment, 0, 0);
        }
    }
}
