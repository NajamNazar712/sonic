<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\CxTraining;
use App\Http\Models\Admin\CxTrainingUnit;
use Auth;
use Carbon\Carbon;
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
        $units = CxTrainingUnit::all();
        return view('admin.qa.cx_training',compact('agents','units'));
    }

    public function cx_training_list(Request $request){
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(),517);
        }

        $training = CxTraining::join('admins', 'admins.id', '=', 'cx_trainings.agent_id')
            ->join('cx_training_units as ctu', 'ctu.id', '=', 'cx_trainings.cx_training_unit_id')
            ->join('admins as rb', 'rb.id', '=', 'cx_trainings.requested_by')
            ->leftjoin('admins as tb', 'tb.id', '=', 'cx_trainings.training_by')
            ->select(['cx_trainings.id','cx_trainings.joining_date','cx_trainings.updated_at','cx_trainings.status','cx_trainings.requested_date', 'admins.name as agent_name', 'ctu.name as unit', 'tb.name as training_by', 'rb.name as requested_by']);

            if ($request->get('search_date_from') && $request->get('search_date_to')) {
                $from = $request->get('search_date_from');
                $to = $request->get('search_date_to');
                $training->whereBetween('cx_trainings.requested_date', [$from, $to]);
            }
    
        $datatables = Datatables::of($training)
            ->addColumn('status_name', function ($training) {
                if ($training->status == 1) {
                    return 'Requested';
                } elseif ($training->status == 2) {
                    return 'Inprocess';
                }elseif ($training->status == 3) {
                    return 'Completed';
                }
            })->addColumn('aging', function ($training) {
                $updated_at = Carbon::parse($training->requested_date)->startOfDay();

                $now = Carbon::now()->startOfDay();

                return $updated_at->diffInDays($now);
            })
            ->addColumn("action", function ($result) {
                if(session('role_id') == 1 || in_array(779,session('permissions'))){
                    
                    $dropdown = '
                          <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                        ';
                    if ($result->status == 1) {
                        $dropdown .= '<button type="button" class="dropdown-item update_status" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Mark Inprogress</div></button>';

                    }elseif ($result->status == 2) {
                        $dropdown .= '<button type="button" class="dropdown-item update_status" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Mark Completed</div></button>';

                    }else{
                        return '';

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

    public function cx_training_add(Request $request){
        $cx_training  = new CxTraining;
        $cx_training->joining_date  = $request->joining_date_formatted;
        $cx_training->cx_training_unit_id  = $request->unit_id;
        $cx_training->requested_by  = Auth::id();
        $cx_training->agent_id  = $request->agent_id;
        $cx_training->requested_date  = Carbon::now();
        
        $cx_training->save();
        return redirect()->back()->with('success','CX Training Added Successfully!');
    }

    public function cx_training_update_status(Request $request){
        $cx_training = CxTraining::find($request->id);
        if($cx_training){
            if($cx_training->status == 1){
                $cx_training->status = 2;
                $cx_training->training_by = Auth::id();
                
                $cx_training->save();
            }elseif ($cx_training->status == 2) {
                $cx_training->status = 3;
                $cx_training->training_by = Auth::id();
                $cx_training->save();

            }
            return response()->json(['status' => 0, 'success' => 'Status updated successfully!']);

        }else{
            return response()->json(['status' => 1, 'error' => 'Not found!']);

        }
    }
}
