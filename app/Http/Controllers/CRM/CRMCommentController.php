<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\CRM\CrmComments;
use App\Http\Models\CRM\CrmRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CRMCommentController extends Controller
{
    static public function add($crm_request_id, $comment_by_id, $comment_by, $comment_type = 0, $comments, $shipper_email, $sms = NULL){
        $comment = new CrmComments();
        $comment->crm_request_id = $crm_request_id;
        $comment->comment_by_id = $comment_by_id;
        $comment->comment_by = $comment_by;
        $comment->comment_type = $comment_type;
        $comment->comment = $comments;
        $comment->save();

        if($comment_type == 0 && $comment_by == 0){
            $crm_request = CrmRequest::find($crm_request_id);
            if($crm_request->case_nature_id == 4){
                $shipper_id = $crm_request->shipper_id;
                if($shipper_id == NULL){
                    $shipper_id = $crm_request->shipment->user_id;
                }
                if($shipper_email == 1){
                    NotificationsController::send(136,$shipper_id,$comment->id);
                }
            }

        }
        if($comment_by == 0){
            if($comment_type == 3){
                if($sms == 1){
                    //Send SMS to Shipper and Consignee
                    NotificationsController::send(176, 1, $comment->id);
                    NotificationsController::send(176, 2, $comment->id);
                }
                else{
                    //Send SMS to Consignee
                    NotificationsController::send(176, 2, $comment->id);
                }
            }
            if($comment_type == 0 && $sms == 1){
                //Send SMS to Shipper
                NotificationsController::send(176, 1, $comment->id);
            }
        }
    }
}
