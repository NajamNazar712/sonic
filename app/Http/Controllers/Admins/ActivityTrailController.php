<?php

namespace App\Http\Controllers\Admins;

use Session;
use Vectorface\Whip\Whip;
use Illuminate\Http\Request;
use App\Http\Models\Admin\Admin;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\AdminRole;
use Illuminate\Support\Facades\Auth;
use App\ShipmentScanningJourneyAreaLog;
use App\Http\Models\Admin\ActivityTrailLog;
use App\Http\Controllers\NotificationsController;

class ActivityTrailController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    static public $department_head_ids = [2,3,4,5,6,52,58];

    static public function createActivityTrailLog($admin_id,$action_id,$dont_send_email = 0)
    {
        if(session('role_id') != 1) {
            $log = new ActivityTrailLog();
            $log->admin_id = $admin_id;
            $log->action_id = $action_id;
            $log->emailed = $dont_send_email;

            $whip = new Whip();
            $client_address = $whip->getValidIpAddress();

            if ($client_address != '') {
                $log->ip_address = $client_address;
            }

            if (Session::has('latitude') && Session::has('longitude')) {
                $log->latitude = session('latitude');
                $log->longitude = session('longitude');
            }

            $log->save();
        }
    }

    public function activity_trail_index ()
    {   self::createActivityTrailLog(Auth::id(),332);
        return view('admin.activity_trail.index');
    }

    public function activity_trail_list (Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            self::createActivityTrailLog(Auth::id(),333);
        }

        $data = ActivityTrailLog::leftjoin('admins as a','a.id','=','activity_trail_logs.admin_id')
           ->leftjoin('city_areas as ca' ,'ca.id' ,'=','a.area_id')
            ->leftjoin('employee_designations as ed', 'ed.id', '=', 'a.designation_id')
            ->leftjoin('admin_roles as ar','ar.id','=','a.role_id')
            ->leftjoin('shipment_scanning_journey_area_logs as location_status','location_status.admin_id','=','a.id')
            ->leftjoin('activity_trail_actions as ata','ata.id','=','activity_trail_logs.action_id')
            ->select('a.name as name','ed.name as designation','ata.screen_name as screen_name','ata.action as action','activity_trail_logs.created_at as created_at', 'activity_trail_logs.ip_address', 'activity_trail_logs.latitude', 'activity_trail_logs.longitude','ca.name as city_area','location_status.admin_id as admin_id');

        if($request->get('search_from') && $request->get('search_to'))
        {
            $data->whereBetween('activity_trail_logs.created_at', [$request->get('search_from'), $request->get('search_to')]);
        }

        if(!in_array(session('role_id'), [1, 58, 24, 5]))
        {
            $head_department_id = Auth::user()->role->department_id;
            $data->where('ar.department_id',$head_department_id);
        }

        return Datatables::of($data)
        ->editColumn('admin_id', function($data){
            $areaLog = ShipmentScanningJourneyAreaLog::where('admin_id', $data->admin_id)->first();
            
            if ($areaLog && isset($areaLog->location_status)) {
                 return $areaLog->location_status == 1 ? 'On-site' : 'Off-site';
            } else {
                return '-';
            }
        })
        
        ->make(true);

    }

}
