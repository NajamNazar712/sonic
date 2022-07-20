<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\CxTraining;
use Auth;
use Yajra\Datatables\Facades\Datatables;


class QualityAssuranceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');

    }

    public function cx_training_index(){

        ActivityTrailController::createActivityTrailLog(Auth::id(),516);

        $agents = Admin::whereIn('role_id', [50, 49, 37.29, 28, 26, 21, 74, 13, 37])->where('status', 1)->get();

        return view('admin.qa.cx_training',compact('agents'));
    }

    public function cx_training_list(Request $request){
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(),517);
        }

        $training = CxTraining::join('admins', 'admins.id', '=', 'cx_trainings.agent_id')
            ->join('cx_training_units as ctu', 'ctu.id', '=', 'cx_trainings.cx_training_unit_id')
            ->join('cx_training_units as ctu', 'ctu.id', '=', 'cx_trainings.cx_training_unit_id')
            ->join('admins as rb', 'rb.id', '=', 'cx_trainings.requested_by')
            ->join('admins as tb', 'tb.id', '=', 'cx_trainings.training_by')
            ->leftjoin('admins AS hab', 'hab.id', '=', 'high_alert_shippers.alert_by')
            ->select(['cx_trainings.joining_date','cx_trainings.updated_at','cx_trainings.status','cx_trainings.requested_date', 'admins.name as agent_name', 'ctu.name as unit', 'tb.name as training_by', 'rb.name as requested_by']);

        $datatables = Datatables::of($training)
            ->editColumn('status', function ($training) {
                if ($training->status == 0) {
                    return 'Removed';
                } elseif ($training->status == 1) {
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
}
