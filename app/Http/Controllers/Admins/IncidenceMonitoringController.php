<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\IncidenceMonitoring;
use App\Http\Models\Admin\IncidenceMonitoringArea;
use App\Http\Models\Admin\IncidenceMonitoringCaseNature;
use App\Http\Models\Admin\IncidenceMonitoringNCLevel;
use App\Http\Models\Admin\IncidenceMonitoringStatusHistory;
use App\Http\Models\Admin\IncidenceMonitoringTaggedPerson;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\IncidenceMonitoringComment;
use App\Http\Models\Admin\IncidenceMonitoringImage;
use App\Http\Models\City;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use IncidenceMonitoringNCLevelSeeder;
use Yajra\Datatables\Datatables as DatatablesDatatables;
use Yajra\Datatables\Facades\Datatables;

class IncidenceMonitoringController extends Controller
{
    // public $sale_role_ids = array();
    // public $finance_role_ids = array();
    public $operation_role_ids = array();

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');

        $this->operation_role_ids = AdminRole::where('department_id',6)->pluck('id')->toArray();
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
        $incidence_monitoring->area_id = $request->monitoring_area_id;
        $incidence_monitoring->time_from = $request->time_from;
        $incidence_monitoring->time_to = $request->time_to;
        $incidence_monitoring->case_nature_id = $request->case_nature_id;
        $incidence_monitoring->observation = $request->observations;
        $incidence_monitoring->nc_level_id = $request->nc_level_id;
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

        $history = new IncidenceMonitoringStatusHistory();
        $history->incidence_monitoring_id = $incidence_monitoring->id;
        $history->status_id = 1;
        $history->admin_id = Auth::user()->id;
        $history->save();


        return redirect()->back()->with('success', 'Report Added successfully.');
    }

    public function list(Request $request){
        $data = IncidenceMonitoring::join('admins as admin','admin.id','=','incidence_monitorings.admin_id')
        ->join('cities as station','station.id','=','incidence_monitorings.station_id')
        ->join('incidence_monitoring_areas as area','area.id','=','incidence_monitorings.area_id')
        ->join('incidence_monitoring_case_natures as case_nature','case_nature.id','=','incidence_monitorings.case_nature_id')
        ->join('incidence_monitoring_n_c_levels as nc_level','nc_level.id','=','incidence_monitorings.nc_level_id')
        ->join('incidence_monitoring_statuses as status','status.id','=','incidence_monitorings.status_id')
        ->select(['incidence_monitorings.id','incidence_monitorings.time_from','incidence_monitorings.time_to','incidence_monitorings.observation','incidence_monitorings.tagging_date','incidence_monitorings.clip_link','station.name as station_name','area.name as area_name','case_nature.name as case_nature_type','nc_level.name as nc_level_name','admin.name as created_by','status.status as status_name']);
        

        $datatables = Datatables::of($data)
        ->addColumn('tagged_to', function($report) {
            $tagged_users = IncidenceMonitoringTaggedPerson::where('incidence_monitoring_id', $report->id)->get();
            $msg = '';
            foreach ($tagged_users as $tagged_user) {
                $msg.= $tagged_user->admin->name.'  ';
            }
           return $msg;
        })
        ->addColumn('incidence_monitorings_id_padded', function ($report) {
            return str_pad($report->id, 6, '0', STR_PAD_LEFT);
        })
        ->addColumn('time_slot', function($report) {
            $time_slot = $report->time_from.' - '.$report->time_to;
            return $time_slot;
        })
        ->addColumn('action', function($data) {
            $dropdown = '';
            if (session('role_id') == 1 || in_array(536, session('permissions'))){
                $dropdown .= '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
                    <button onclick="window.open(\'' . route('admin.incidence_monitoring.view_report', ['id' => $data->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Details</div></button>
                    </div>
              </div>
            ';
            }
            return $dropdown;
        });
       
        return $datatables->make(true);
    }

    public function view_report($id){
        $report = IncidenceMonitoring::find($id);
        $images_count = $report->images->count();

        if($report){
            
            return view('admin.incidence_monitoring.view_report',compact('report', 'images_count'));
        }else{
            return redirect()->back()->with('danger', 'Incidence Monitoring Report Not found!');
        }

    }

    public function add_comment(Request $request){
        if(Auth::user()->role_id == 8)
        {
            $comment_by = 1;
        }
        elseif(Auth::user()->role_id == 9)
        {
            $comment_by = 0;
        }
        elseif(in_array(Auth::user()->role_id,$this->operation_role_ids))
        {
            $comment_by = 2;
        }
        else {
            return response()->json(['status'=>0,'error'=>'You are not allowed to comment on the request']);
        }

        $comment = new IncidenceMonitoringComment();
         $comment->incidence_monitoring_id = $request->request_id;
         $comment->comment_by_id = Auth::id();
         $comment->comment_by = $comment_by;
         $comment->comment = $request->comment;
         $comment->save();

         return response()->json(['status'=>1]);
    }

    public function get_comments(Request $request)
     {
        
        $comments = IncidenceMonitoringComment::leftjoin('admins as a','a.id','incidence_monitoring_comments.comment_by_id')
             ->where('incidence_monitoring_comments.incidence_monitoring_id',$request->request_id)
             ->select(['incidence_monitoring_comments.id as id','incidence_monitoring_comments.comment as comment','incidence_monitoring_comments.comment_by as comment_by','incidence_monitoring_comments.comment_by_id as commenter_id','incidence_monitoring_comments.created_at as created_at','a.name as commenter']);
        if($comments->exists())
         {
             $comments = $comments->get();
             return response()->json(['status'=>1,'comments'=>$comments]);
         }

     }

     public function image_details(Request $request){
        $id = $request->request_id;
        if($id){
            $incidence_monitoring = IncidenceMonitoring::find($id);
            if($incidence_monitoring){
                $details = array();
                $images = IncidenceMonitoringImage::where('incidence_monitoring_id', $id);
                if($images->exists()){
                    $images = $images->get();
                    foreach ($images as $image) {
                        $img_url = asset('uploads/incidence_monitoring_report_images/'.$image->image);
                        $details[] = array('id' => $image->id,'date' => Carbon::parse($image->created_at)->toDateTimeString(),'image'=> $img_url);
                    }
                    return response()->json(['status' => 0, 'images' => $details]);
                }
                return response()->json(['status' => 2]);
            }
            return response()->json(['status' => 1, 'error' => 'Incidence Monitoring Report not found!']);
        }
     }

     public function image_submit(Request $request){
        $request_id = $request->image_request_id;
        $image_ids = explode(',', $request->selected_ids);
        if(count($image_ids) == 0){
            return redirect()->back()->with('error', 'No images selected!');
        }
        $report = IncidenceMonitoring::find($id);
        if($report){
            $report_images = $report->images->count();
            if($report_images == 2){
                return redirect()->back()->with('error', 'Two images are already uploaded!');
            }

            foreach ($image_ids as $id){
                    $file_name = 'image_'.$id;
                    $image = $request->file($file_name);

                    $extension = $image->getClientOriginalExtension();
                    $random = rand(1000, 100000);
                    $now = Carbon::now();
                    $time = $now->year . '_' . $now->month;
                    $generated_image_name = $time . $random . Auth::id() . '.' . $extension;
                    $image->move(public_path('uploads/incidence_monitoring_report_images'), $generated_image_name);
                    $report_image = new IncidenceMonitoringImage();
                    $report_image->incidence_monitoring_id = $request_id;
                    $report_image->added_by = Auth::id();
                    $report_image->image = $generated_image_name;
                    $report_image->save();
            }

            return redirect()->back()->with(['status' => 1, 'success' => 'Images updated successfully']);

        }
        return redirect()->back()->with(['status' => 0, 'error' => 'Incidence Monitoring Report Not found!']);
     }

    
}
