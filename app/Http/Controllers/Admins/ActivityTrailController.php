<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\ActivityTrailLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivityTrailController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    static public function createActivityTrailLog($admin_id,$action_id,$send_email = false)
    {
        $log = new ActivityTrailLog();
        $log->admin_id = $admin_id;
        $log->action_id = $action_id;
        $log->save();
    }

    public function activity_trail_index ()
    {
        return view('admin.activity_trail.index');
    }

    public function activity_trail_list (Request $request)
    {

    }

}
