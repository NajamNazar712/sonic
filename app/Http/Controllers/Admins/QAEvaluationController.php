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
        $agents = Admin::all(); // 74,50,49,37.29,28,26,21,74
        $campaigns = EvaluationCampaign::all();
        $evaluated_by = Admin::all();
        $natures = EvaluationNature::all();
        return view('admin.qa_evaluation.add',compact('agents','campaigns','evaluated_by','natures'));
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
            if(count($request->activity_ids) >=4){
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
            // dump('ss');
            foreach ($request->activity_ids as $activity_id) {
                QAEvaluationActivity::create([
                    'agent_id' => $request->agent_id,
                    'qa_evaluation_id' => $qa_evaluation->id,
                    'evaluation_activity_id' => $activity_id,
                ]);
            }
        }
        return redirect()->back()->with('success', 'QA Evaluation Added');

    }

    public function index(){
        return view('admin.qa_evaluation.index');
    }

    public function list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            // ActivityTrailController::createActivityTrailLog(Auth::id(),420);
        }
         $evaluation = QAEvaluation::leftjoin('admins as ad','ad.id','=','q_a_evaluations.agent_id')
                ->leftjoin('admins as ev','ev.id','=','q_a_evaluations.evaluated_by')
                ->leftjoin('evaluation_campaigns as ec','ec.campaign_id','=','q_a_evaluations.campaign_id')
                ->leftjoin('evaluation_natures as en','en.id','=','q_a_evaluations.nature_id')
             ->select(['ad.name as agent_name','ec.campaign as campaign','ev.name as evaluated_by','q_a_evaluations.evaluation_date as evaluation_date','en.nature as nature','q_a_evaluations.date_time as date_time','q_a_evaluations.status as status','q_a_evaluations.score as score','q_a_evaluations.score as score','q_a_evaluations.remarks as remarks']);
    
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
             ->addColumn("actions", function ($result) {
                 if (session('role_id') == 1 || count(array_intersect([570,571,572,573,574,575,576,577,578], session('permissions'))) !== 0) {
                     $dropdown = '
                          <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                        ';
    
                     if (session('role_id') == 1 || $result->reporting_manager == Auth::id() || in_array(570, session('permissions'))) {
                         $dropdown .= '<button type="button" class="dropdown-item rm_view" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Reporting Manager View</div></button>';
    
                     }
                     if (session('role_id') == 1 || in_array(571, session('permissions'))) {
                         $dropdown .= '<button type="button" class="dropdown-item cs_view" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Customer Experience View</div></button>';
    
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
}
