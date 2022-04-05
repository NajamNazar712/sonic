<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\HighAlertShipper;
use App\Http\Models\Shipper\User;
use Carbon\Carbon;
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
            ->select(['high_alert_shippers.id', 'users.name as shipper', 'sp.name as sale_person', 'high_alert_shippers.description', 'hab.name as alert_by','high_alert_shippers.status', 'c.name as city'])
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
                        $dropdown .= '<button type="button" class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                    }
                    if (session('role_id') == 1 || in_array(694, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item remove" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-crosshair"></i></div><div class="col-9 offset-1">Remove</div></button>';

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

        $count = HighAlertShipper::where('status', 1)->where('user_id', $shipper_id)->count();
        if($count < 3){
            $high_alert = new HighAlertShipper();
            $high_alert->user_id = $shipper_id;
            $high_alert->description = $description;
            $high_alert->alert_by = $alert_by;
            $high_alert->last_updated_by = $alert_by;
            $high_alert->status = 1;
            $high_alert->save();

            return response()->json(['status' => 0, 'success' => 'Shipper marked as high alert!']);
        }
        else{
            $user = User::find($shipper_id);
            if($user){
                $user->disable_at = Carbon::now();
                $user->status = 4;
                $user->save();

                return response()->json(['status' => 0, 'success' => 'Shipper is disabled due to High Alert Frequency!']);
            }
        }
    }

    public function remove(Request $request){
        $alert_id = $request->alert_id;
        if($alert_id){
            $high_alert = HighAlertShipper::find($alert_id);
            if($high_alert){
                if($high_alert->status == 1){
                    $high_alert->status = 0;
                    $high_alert->last_updated_by = Auth::id();
                    $high_alert->save();

                    return response()->json(['status' => 0, 'success' => 'High Alert removed from this shipper!']);
                }
                else{
                    return response()->json(['status' => 1, 'error' => 'High Alert from this shipper is already removed!']);
                }
            }
            else{
                return response()->json(['status' => 1, 'error' => 'Something went wrong!']);
            }
        }
        else{
            return response()->json(['status' => 1, 'error' => 'Something went wrong!']);
        }
    }

    public function info(Request $request){
        $alert_id = $request->alert_id;
        if($alert_id){
            $high_alert = HighAlertShipper::find($alert_id);
            if($high_alert){
                if($high_alert->status == 1){
                    $shipper_id = $high_alert->user_id;
                    $description = $high_alert->description;

                    return response()->json(['status' => 0, 'shipper_id' => $shipper_id, 'description' => $description]);
                }
                else{
                    return response()->json(['status' => 1, 'error' => 'High Alert from this shipper is already removed!']);
                }
            }
            else{
                return response()->json(['status' => 1, 'error' => 'Something went wrong!']);
            }
        }
        else{
            return response()->json(['status' => 1, 'error' => 'Something went wrong!']);
        }
    }

    public function edit(Request $request){
        $alert_id = $request->alert_id;
        if($alert_id){
            $high_alert = HighAlertShipper::find($alert_id);
            if($high_alert){
                if($high_alert->status == 1){
                    $high_alert->description = $request->description;
                    $high_alert->alert_by = Auth::id();
                    $high_alert->last_updated_by = Auth::id();
                    $high_alert->save();
                    return response()->json(['status' => 0, 'success' => 'High Alert updated!']);
                }
                else{
                    return response()->json(['status' => 1, 'error' => 'High Alert from this shipper is already removed!']);
                }
            }
            else{
                return response()->json(['status' => 1, 'error' => 'Something went wrong!']);
            }
        }
        else{
            return response()->json(['status' => 1, 'error' => 'Something went wrong!']);
        }
    }

}
