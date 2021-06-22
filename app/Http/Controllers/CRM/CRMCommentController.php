<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\CRM\CrmComments;
use App\Http\Models\CRM\CrmRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CRMCommentController extends Controller
{
    static public function add($crm_request_id, $comment_by_id, $comment_by, $comment_type = 0, $comments){
        $comment = new CrmComments();
        $comment->crm_request_id = $crm_request_id;
        $comment->comment_by_id = $comment_by_id;
        $comment->comment_by = $comment_by;
        $comment->comment_type = $comment_type;
        $comment->comment = $comments;
        $comment->save();

        if($comment_type == 0 && $comment_by == 0){
            $crm_request = CrmRequest::find($crm_request_id);
            $shipper_id = $crm_request->shipper_id;
            if($shipper_id == NULL){
                $shipper_id = $crm_request->shipment->user_id;
            }
            NotificationsController::send(136,$shipper_id,$comment->id);
        }
    }
}
