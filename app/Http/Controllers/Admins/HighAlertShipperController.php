<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\HighAlertShipper;
use App\Http\Models\Shipper\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Yajra\Datatables\Facades\Datatables;

class HighAlertShipperController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');

    }

    public function index(){

        ActivityTrailController::createActivityTrailLog(Auth::id(),516);
        $shippers = User::select(['id', 'name'])->where('status', 3)->get();
        return view('admin.qa.high_alert_shippers')->with(['shippers' => $shippers]);
    }

    public function list(Request $request){

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(),517);
        }

        $shippers = HighAlertShipper::join('users', 'users.id', '=', 'high_alert_shippers.user_id')
            ->join('sale_person_tags as spt', 'spt.user_id', '=', 'users.id')
            ->join('admins AS sp', 'sp.id', '=', 'spt.admin_id')
            ->join('admins AS hab', 'hab.id', '=', 'high_alert_shippers.alert_by')
            ->join('cities as c', 'c.id', '=', 'users.city_id')
            ->select(['users.id', 'users.name as shipper', 'sp.name as sale_person', 'high_alert_shippers.description', 'hab.name as alert_by','high_alert_shippers.status', 'c.name as city'])
            ->where('spt.status', '=', 0);

        $datatables = Datatables::of($shippers)
            ->editColumn('status', function ($shippers) {
                if ($shippers->status == 0) {
                    return 'Removed';
                } elseif ($shippers->status == 1) {
                    return 'Added';
                }
            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || count(array_intersect([694, 695], session('permissions'))) !== 0) {
                    $dropdown = '
                          <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                        ';

                    if (session('role_id') == 1 || in_array(695, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item qa_edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-activity"></i></div><div class="col-9 offset-1">Edit</div></button>';

                    }
                    if (session('role_id') == 1 || in_array(694, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item qa_view" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-crosshair"></i></div><div class="col-9 offset-1">Remove</div></button>';

                    }

                    $dropdown .= '
                            </div>
                          </div>
                        ';

                    return $dropdown;
                } else {
                    return '';
                }
            });
        return $datatables->make(true);
    }

    public function add(Request $request){
        $shipper_id = $request->shipper_id;
        $description = $request->description;
        $alert_by = Auth::id();
        if(!$shipper_id && !$description){
            return response()->json(['status' => 1, 'error' => 'Please fill all fields!']);
        }
        $high_alert = new HighAlertShipper();
        $high_alert->user_id = $shipper_id;
        $high_alert->description = $description;
        $high_alert->alert_by = $alert_by;
        $high_alert->status = 0;
        $high_alert->save();

        return response()->json(['status' => 0, 'success' => 'Shipper marked as high alert!']);
    }
}
