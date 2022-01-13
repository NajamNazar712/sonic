<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Models\Webhook\WebhookLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WebhookLogController extends Controller
{
    static public function log($user_id, $status_code, $payload = NULL){
        $log = new WebhookLog();
        $log->user_id = $user_id;
        $log->status_code = $status_code;
        $log->payload = $payload;
        $log->save();
    }
}
