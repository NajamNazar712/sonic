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
        foreach ($request->activity_ids as $activity_id) {
            $activity = EvaluationActivity::find($activity_id);
            if(in_array($activity->handling->id, [4,10,17])){
                $score = 0;
            }else{
                $score-=$activity->weightage;
            }
        }
        if(count($request->activity_ids) >=3){
            $score = 0;
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
            'evaluated_by' => $request->evaluated_by,
            'evaluation_date' => Carbon::today(),
            'nature_id' => $request->nature_id,
            'call_duration' => $request->call_duaration,
            'date_time' => $request->call_date_time,
            'query_by' => $request->query_by,
            'caller_contact' => $request->contact_number,
            'status' => $status,
            'score' => $score,
        ]);
        foreach ($request->activity_ids as $activity_id) {
            QAEvaluationActivity::create([
                'agent_id' => $request->agent_id,
                'qa_evaluation_id' => $qa_evaluation->id,
                'evaluation_activity_id' => $activity_id,
            ]);
        }
        return redirect()->back()->with('success', 'QA Evaluation Added');

    }
}
