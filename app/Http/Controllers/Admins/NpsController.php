<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use App\Http\Models\Shipper\User;
use App\Http\Models\Admin\NpsSurvey;
use App\Http\Models\Admin\NpsSurveyQuestion;

class NpsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');

    }

    public function index()
    {
        $shippers = User::select(['id', 'name'])->where('status', 3)->get();
        return view('admin.qa.high_alert_shippers')->with(['shippers' => $shippers]);
    }

    public function add()
    {
        $max_date = Carbon::now()->addYear(1);
        $min_date = Carbon::now()->subYear(1);
        $shippers = User::where('status', 3)->where('blacklist', 0)->select('id', 'name')->get();
        return view('admin.nps.add', compact('shippers', 'min_date', 'max_date'));
    }

    public function submit(Request $request)
    {
        $start_time = $request->start_time;
        $end_time = $request->end_time;
        $shipper_id = isset($request->shipper_ids) ? implode(',', $request->shipper_ids) : '';
        $all_shipper = isset($request->all_shipper) ? 1 : 0;
        $recommendation_box = isset($recommendation_box->recommendation_box) ? $request->recommendation_box : 0;
        $date = Carbon::now();

        $NpsSurvey = new NpsSurvey();
        $NpsSurvey->start_time = $start_time;
        $NpsSurvey->end_time = $end_time;
        $NpsSurvey->shipper_ids = $shipper_id;
        $NpsSurvey->all_shipper = $all_shipper;
        $NpsSurvey->recommendation_box = $recommendation_box;
        $NpsSurvey->status = 0; //Default not active
        $NpsSurvey->created_at = $date;
        $NpsSurvey->updated_at = $date;
        $NpsSurvey->save();

        foreach ($request->question as $question){
            $NpsSurveyQuestion = new NpsSurveyQuestion();
            $NpsSurveyQuestion->nps_survey_id = $NpsSurvey->id;
            $NpsSurveyQuestion->question = $question;
            $NpsSurveyQuestion->created_at = $date;
            $NpsSurveyQuestion->updated_at = $date;
            $NpsSurveyQuestion->save();
        }

        return redirect()->back()->with(['success' => 'Survey Added Successfully']);

    }
}
