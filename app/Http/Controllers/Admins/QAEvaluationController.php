<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\EvaluationActivity;
use App\Http\Models\EvaluationCampaign;
use App\Http\Models\EvaluationHandling;
use App\Http\Models\EvaluationNature;
use App\Http\Models\QAEvaluation;
use App\Http\Models\QAEvaluationActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class QAEvaluationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');

    }

    public function add(){
        $max_date = Carbon::tomorrow();
        $min_date = Carbon::now()->subYear(1);
        $agents = Admin::all(); // 74,50,49,37.29,28,26,21,74
        $campaigns = EvaluationCampaign::all();
        $evaluated_by = Admin::all();
        $natures = EvaluationNature::all();
        return view('admin.qa_evaluation.add',compact('agents','campaigns','evaluated_by','natures','min_date','max_date'));
    }

    public function handlings(Request $request){
        $evaluation_handlings = EvaluationHandling::where('campaign_id',$request->id)->get();
        $handlings = EvaluationHandling::where('campaign_id',$request->id)->pluck('id')->toArray();

        
        $evaluated_activities = EvaluationActivity::whereIn('evaluation_handling_id', $handlings)->get();
        return response()->json(['status'=>1,'evaluation_handlings'=>$evaluation_handlings, 'evaluated_activities' => $evaluated_activities]);

    }

    public function submit(Request $request){
        
        $score = 100;
        if($request->has('activity_ids')){
            if(count($request->activity_ids) > 4){
                $score = 0;
            }else{
                foreach ($request->activity_ids as $activity_id) {
                    $activity = EvaluationActivity::find($activity_id);
                    if(in_array($activity->handling->id, [4,10,17])){
                        $score = 0;
                        break;
                    }else{
                        $score-=$activity->weightage;
                    }
                }
            }
        }
        
        //0 fatal, 1 non fatal, 2 accurate
        if($score <= 0){
            $status = 0;
            $score = 0;
        }elseif ($score == 100) {
            $status = 2;
        }else{
            $status = 1;
        }
        $qa_evaluation = QAEvaluation::create([
            'agent_id' => $request->agent_id,
            'campaign_id' => $request->campaign_id,
            'evaluated_by' => Auth::id(),
            'evaluation_date' => Carbon::today(),
            'nature_id' => $request->nature_id,
            'call_duration' => $request->call_duaration,
            'date_time' => $request->call_date_time,
            'query_by' => $request->query_by,
            'caller_contact' => $request->contact_number,
            'status' => $status,
            'score' => $score,
            'remarks' => $request->remarks,
        ]);
        if($request->has('activity_ids')){
            foreach ($request->activity_ids as $activity_id) {
                QAEvaluationActivity::create([
                    'agent_id' => $request->agent_id,
                    'qa_evaluation_id' => $qa_evaluation->id,
                    'evaluation_activity_id' => $activity_id,
                ]);
            }
        }

        return redirect()->route('admin.qa_evaluation.index')->with('success', 'QA Evaluation Added');

    }

    public function index(){

        ActivityTrailController::createActivityTrailLog(Auth::id(),486);

        return view('admin.qa_evaluation.index');
    }

    public function list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),487);
        }
        
        $evaluation = QAEvaluation::join('admins as ad','ad.id','=','q_a_evaluations.agent_id')
                ->join('evaluation_natures as en','en.id','=','q_a_evaluations.nature_id')
                ->join('evaluation_campaigns as ec','ec.campaign_id','=','q_a_evaluations.campaign_id')
                ->join('admins as ev','ev.id','=','q_a_evaluations.evaluated_by')
             ->select(['q_a_evaluations.id','ad.name as agent_name','ev.name as evaluated_by','en.nature as nature','ec.campaign as campaign','q_a_evaluations.updated_at as evaluation_date','q_a_evaluations.date_time as date_time','q_a_evaluations.status as status','q_a_evaluations.score as score','q_a_evaluations.score as score','q_a_evaluations.remarks as remarks']);
    

         $datatables = Datatables::of($evaluation)
         
             ->editColumn('status',function ($evaluation) {
                 if($evaluation->status == 0){
                     return 'Fatal';
                 }elseif ($evaluation->status == 1) {
                     return 'Non-Fatal';
                 }else{
                    return 'Accurate';

                 }
             })
             ->setRowAttr([
                'class' => function ($evaluation){

                    if ($evaluation->status == 0) {
                        return 'fatal';
                    }
                    if ($evaluation->status == 1) {
                        return 'non_fatal';
                    }
                    if ($evaluation->status == 2) {
                        return 'accurate';
                    }
                    
                },
            ])
             ->addColumn("action", function ($result) {
                 if (session('role_id') == 1 || count(array_intersect([570,571,572,573,574,575,576,577,578], session('permissions'))) !== 0) {
                     $dropdown = '
                          <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                        ';
    
                     if (session('role_id') == 1 || in_array(571, session('permissions'))) {
                         $dropdown .= '<button type="button" class="dropdown-item qa_edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit</div></button>';
    
                     }
                     if (session('role_id') == 1 || in_array(571, session('permissions'))) {
                         $dropdown .= '<button type="button" class="dropdown-item qa_view" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View</div></button>';
    
                     }
                   
                     $dropdown .= '
                            </div>
                          </div>
                        ';
    
                     return $dropdown;
                 }
                 else {
                     return '';
                 }
             });
         ;
         return $datatables->make(true);
    }

    public function edit($id){
        $qa_evaluations = QAEvaluation::find($id);
        $agents = Admin::all(); // 74,50,49,37.29,28,26,21,74
        $campaigns = EvaluationCampaign::all();
        $evaluated_by = Admin::all();
        $natures = EvaluationNature::all();
        return view('admin.qa_evaluation.edit',compact('agents','campaigns','evaluated_by','natures','qa_evaluations'));
    }

    public function view($id){
        $qa_evaluations = QAEvaluation::find($id);
        $agents = Admin::all(); // 74,50,49,37.29,28,26,21,74
        $campaigns = EvaluationCampaign::all();
        $evaluated_by = Admin::all();
        $natures = EvaluationNature::all();
        return view('admin.qa_evaluation.view',compact('agents','campaigns','evaluated_by','natures','qa_evaluations'));
    }

    public function handlings_edit(Request $request){
        $evaluation_handlings = EvaluationHandling::where('campaign_id',$request->id)->get();
        $handlings = EvaluationHandling::where('campaign_id',$request->id)->pluck('id')->toArray();

        $qa_handings = QAEvaluationActivity::where('qa_evaluation_id',$request->qa_evaluation_id)->pluck('evaluation_activity_id')->toArray();
        $evaluated_activities = EvaluationActivity::whereIn('evaluation_handling_id', $handlings)->get();
        return response()->json(['status'=>1,'evaluation_handlings'=>$evaluation_handlings, 'evaluated_activities' => $evaluated_activities,'qa_handings' => $qa_handings]);

    }

    public function update(Request $request){


        $score = 100;
        
        if($request->has('activity_ids')){
            if(count($request->activity_ids) > 4){
                $score = 0;
            }else{
                foreach ($request->activity_ids as $activity_id) {
                    $activity = EvaluationActivity::find($activity_id);
                    if(in_array($activity->handling->id, [4,10,17])){
                        $score = 0;
                        break;
                    }else{
                        $score-=$activity->weightage;
                    }
                }
            }
        }
        
        //0 fatal, 1 non fatal, 3 accurate
        if($score <= 0){
            $status = 0;
            $score = 0;
        }elseif ($score == 100) {
            $status = 3;
        }else{
            $status = 1;
        }
        $qa_evaluation = QAEvaluation::find($request->qa_evaluation_id);

        $qa_evaluation->agent_id = $request->agent_id ;
        $qa_evaluation->campaign_id = $request->campaign_id ;
        $qa_evaluation->evaluated_by = Auth::id() ;
        $qa_evaluation->evaluation_date = Carbon::today()->format('Y-m-d');
        $qa_evaluation->nature_id = $request->nature_id ;
        $qa_evaluation->call_duration = $request->call_duaration ;
        $qa_evaluation->date_time = $request->call_date_time ;
        $qa_evaluation->query_by = $request->query_by ;
        $qa_evaluation->caller_contact = $request->contact_number ;
        $qa_evaluation->status = $status ;
        $qa_evaluation->score = $score ;
        $qa_evaluation->remarks = $request->remarks ;
        $qa_evaluation->save();
        QAEvaluationActivity::where('qa_evaluation_id',$request->qa_evaluation_id)->delete();

        if($request->has('activity_ids')){
            foreach ($request->activity_ids as $activity_id) {
                QAEvaluationActivity::create([
                    'agent_id' => $request->agent_id,
                    'qa_evaluation_id' => $qa_evaluation->id,
                    'evaluation_activity_id' => $activity_id,
                ]);
            }
        }
        return redirect()->route('admin.qa_evaluation.index')->with('success', 'QA Evaluation Updated');
    }

    public function edit_activities(Request $request){
        // $evaluation_handlings = EvaluationHandling::all();

        return view('admin.qa_evaluation.edit_activites');
    }

    public function actvities_data(Request $request){
        $handlings = EvaluationHandling::where('campaign_id',$request->id)->get();
        $activities = EvaluationActivity::leftjoin('evaluation_handlings as eh','eh.id','=','evaluation_activities.evaluation_handling_id')
        ->where('eh.campaign_id',$request->id)->
        select('evaluation_activities.id as id','evaluation_activities.activity as activity','evaluation_activities.weightage as weightage','eh.id as evaluation_handling_id')->get();
        return response()->json(['status'=>1,'activities'=>$activities,'handlings'=>$handlings]);
    }

    public function update_activities(Request $request){

        $removed_activity = [];
        $new_activity = [];
        foreach ($request->activity_name as $key => $value) {
            // $evaluation_handlings = EvaluationHandling::find($key);
                foreach ($value  as $activity_key => $activity_value) {
                    if($activity_key == 0){
                        //insert
                        $activity = new EvaluationActivity;
                            $activity->evaluation_handling_id = $key;
                            $activity->activity = $activity_value;
                            $activity->weightage = $request->activity_weightage[$key][$activity_key];
                            $activity->save();
                        array_push($new_activity,$activity->id);

                    }else{
                        //update
                        $activity = EvaluationActivity::find($activity_key);
                        $activity->evaluation_handling_id = $key;
                        $activity->activity = $activity_value;
                        $activity->weightage = $request->activity_weightage[$key][$activity_key];
                        $activity->save();
                    }
                }
            $ids = array_keys($request->activity_name[$key]);

                $evaluated_activity = EvaluationActivity::where('evaluation_handling_id',$key)->whereNotIn('id',$ids);
                if($evaluated_activity->exists()){
                    $evaluated_activity = $evaluated_activity->pluck('id')->toArray();
                    array_push($removed_activity,$evaluated_activity);
                }
        }
        if(!empty($removed_activity)){

            if($removed_activity[0] == $new_activity){
                $removed_activity = [];
            }else{
                $removed_activity =array_unique( array_merge($new_activity, $removed_activity[0]) );
            }
            foreach ($removed_activity as $key => $value) {
                //delete
                EvaluationActivity::where('id',$value)->delete();
            }
        }
        // $removed_activity=array_diff($removed_activity,$new_activity);
        return redirect()->back()->with('success', 'Activity Updated');
        
    }
}
