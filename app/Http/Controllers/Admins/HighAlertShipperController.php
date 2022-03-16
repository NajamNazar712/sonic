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
            ->select(['users.id', 'users.name as shipper', 'sp.name as sale_person', 'high_alert_shippers.description', 'high_alert_shippers.status'])
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
                if (session('role_id') == 1 || count(array_intersect([654, 655], session('permissions'))) !== 0) {
                    $dropdown = '
                          <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                        ';

                    if (session('role_id') == 1 || in_array(654, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item qa_edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit</div></button>';

                    }
                    if (session('role_id') == 1 || in_array(655, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item qa_view" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View</div></button>';

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
}
