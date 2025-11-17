<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Models\Webhook\ShipperWebhookLog;
use App\Http\Models\Webhook\WebhookLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Webhook\ApiCallLog;

class WebhookLogController extends Controller
{
    static public function shipment_status_log($user_id, $status_code, $payload = NULL){
        $log = new WebhookLog();
        $log->user_id = $user_id;
        $log->status_code = $status_code;
        $log->payload = $payload;
        $log->save();
    }

    static public function shipper_webhook_log($shipper_id, $message){
        $log = new ShipperWebhookLog();
        $log->shipper_id = $shipper_id;
        $log->message = $message;
        $log->save();
    }
    
    static public function zong_call_log($user_id, $status_code,$shipment_id,$call_count_log, $payload = NULL,$channel = 'zong'){
        $log = new ApiCallLog();
        $log->user_id = $user_id;
        $log->shipment_id = $shipment_id;
        $log->status_code = $status_code;
        $log->call_count_initiate = $call_count_log;
        $log->payload = $payload;
        $log->channel_name = $channel;
        $log->save();
    }
}
