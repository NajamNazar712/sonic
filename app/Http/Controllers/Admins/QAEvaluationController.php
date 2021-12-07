<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\EvaluationActivity;
use App\Http\Models\EvaluationCampaign;
use App\Http\Models\EvaluationHandling;
use App\Http\Models\EvaluationNature;

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
        dd($request->all());
    }
}
