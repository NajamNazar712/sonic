<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Model\IncidenceMonitoring;
use App\Http\Model\IncidenceMonitoringArea;
use App\Http\Model\IncidenceMonitoringCaseNature;
use App\Http\Model\IncidenceMonitoringNCLevel;
use App\Http\Model\IncidenceMonitoringTaggedPerson;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\City;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use IncidenceMonitoringNCLevelSeeder;

class IncidenceMonitoringController extends Controller
{
    // public $sale_role_ids = array();
    // public $finance_role_ids = array();
    // public $operation_role_ids = array();

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
        // $this->sale_role_ids = AdminRole::where('department_id',7)->pluck('id')->toArray();
        // $this->finance_role_ids = AdminRole::where('department_id',4)->pluck('id')->toArray();
        // $this->operation_role_ids = AdminRole::where('department_id',6)->pluck('id')->toArray();
    }


    public function index(){
        $monitoring_areas = IncidenceMonitoringArea::all();
        $case_natures = IncidenceMonitoringCaseNature::all();
        $nc_levels = IncidenceMonitoringNCLevel::all();
        $stations = City::all();
        return view('admin.incidence_monitoring.index', compact('monitoring_areas', 'case_natures', 'nc_levels', 'stations'));
    }

    public function get_managers(Request $request){
        $admin_ids = AdminHub::where('hub_id',$request->hub_id)->pluck('admin_id')->toArray();
        if(count($admin_ids) > 0){

            $agents = Admin::whereIn('id', $admin_ids)->where('status',1)->get();
           
            return response()->json(['status' => 1, 'agents' => $agents]);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Agents Does\'nt exist!']);
        }
    }


    public function add(Request $request){
        
        $incidence_monitoring = new IncidenceMonitoring();
        $incidence_monitoring->station_id = $request->station_id;
        $incidence_monitoring->incidence_monitoring_area_id = $request->monitoring_area_id;
        $incidence_monitoring->time_from = $request->time_from;
        $incidence_monitoring->time_to = $request->time_to;
        $incidence_monitoring->incidence_monitoring_case_nature_id = $request->case_nature_id;
        $incidence_monitoring->obeservation = $request->observations;
        $incidence_monitoring->incidence_monitoring_n_c_level_id = $request->nc_level_id;
        $incidence_monitoring->tagging_date = Carbon::now();
        $incidence_monitoring->clip_link = $request->clip_link;
        $incidence_monitoring->admin_id = Auth::user()->id;
        $incidence_monitoring->save();

        foreach ($request->tagged_to as $value) {
            $incidence_monitoring_tagged_person = new IncidenceMonitoringTaggedPerson();
            $incidence_monitoring_tagged_person->incidence_monitoring_id = $incidence_monitoring->id;
            $incidence_monitoring_tagged_person->admin_id = $value;
            $incidence_monitoring_tagged_person->save();
        }

        return redirect()->back()->with('success', 'Report Added successfully.');
    }

    public function list(Request $request){
        $report = IncidenceMonitoring::select(['station_id as station','incidence_monitoring_area_id as monitoring_area']);
        $monitoring_areas = IncidenceMonitoringArea::all();
        $case_natures = IncidenceMonitoringCaseNature::all();
        $nc_levels = IncidenceMonitoringNCLevel::all();
        $stations = City::all();
        return view('admin.incidence_monitoring.index', compact('monitoring_areas', 'case_natures', 'nc_levels', 'stations'));
    }
}
