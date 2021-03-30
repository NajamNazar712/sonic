<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\ActivityTrailLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivityTrailController extends Controller
{
    static public function createActivityTrailLog($admin_id,$action_id,$send_email = false)
    {

        $log = new ActivityTrailLog();
        $log->admin_id = $admin_id;
        $log->action_id = $action_id;
        $log->save();

        if($send_email)
        {

        }
    }

}
