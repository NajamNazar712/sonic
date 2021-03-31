<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\ActivityTrailLog;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class ActivityTrailController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    static public $department_head_ids = [2,3,4,5,6,52,58];

    static public function createActivityTrailLog($admin_id,$action_id,$dont_send_email = 0)
    {
        $log = new ActivityTrailLog();
        $log->admin_id = $admin_id;
        $log->action_id = $action_id;
        $log->emailed = $dont_send_email;
        $log->save();
    }

    public function activity_trail_index ()
    {
        $group_logs =  ActivityTrailLog::leftjoin('admins as a','a.id','=','activity_trail_logs.admin_id')
            ->leftjoin('admin_roles as ar','ar.id','=','a.role_id')
            ->leftjoin('activity_trail_actions as ata','ata.id','=','activity_trail_logs.action_id')
            ->select('a.name as name','a.designation as designation','ata.screen_name as screen_name','ata.action as action','activity_trail_logs.created_at as created_at','ar.department_id as department_id')
            ->where('emailed',0)
            ->get()
            ->groupBy('department_id');

        foreach ($group_logs as $key => $logs)
        {
             $head_role_id = AdminRole::where('department_id',$key)->whereIn('id',ActivityTrailController::$department_head_ids)->pluck('id')->first();
             $head =  Admin::where('role_id',$head_role_id)->first();

            if (strpos($body, '[contact_person]') !== FALSE) {
                $body = str_replace('[contact_person]', $head->name, $body);
            }

            $preview = `<table>
                           <tr>
                            <th>S No.</th>
                            <th>Team Member Name</th>
                            <th>Designation</th>
                            <th>Screen Name</th>
                            <th>Action Performed</th>
                            <th>Action Performed Time</th>
                           </tr>`
             foreach ($logs as $log)
             {

             }

        }
        exit;
        return view('admin.activity_trail.index');
    }

    public function activity_trail_list (Request $request)
    {
        $data = ActivityTrailLog::leftjoin('admins as a','a.id','=','activity_trail_logs.admin_id')
            ->leftjoin('admin_roles as ar','ar.id','=','a.role_id')
            ->leftjoin('activity_trail_actions as ata','ata.id','=','activity_trail_logs.action_id')
            ->select('a.name as name','a.designation as designation','ata.screen_name as screen_name','ata.action as action','activity_trail_logs.created_at as created_at');

        if($request->get('search_from') && $request->get('search_to'))
        {
            $data->whereBetween('activity_trail_logs.created_at', [$request->get('search_from'), $request->get('search_to')]);
        }

        $head_department_id = Auth::user()->role->department_id;
        if(session('role_id') != 1)
        {
            $data->where('ar.department_id',$head_department_id);
        }

        return Datatables::of($data)
            ->make(true);

    }

}
