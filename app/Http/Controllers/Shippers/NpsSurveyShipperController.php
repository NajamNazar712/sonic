<?php

namespace App\Http\Controllers\Shippers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\NpsSurvey;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Admin\NpsSurveyQuestion;
use App\Http\Models\NpsShipperRatting;
use App\Http\Models\NpsShipperSkipSurvey;
use App\Http\Models\NpsSurveyReport;

class NpsSurveyShipperController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function nps_survey_check(Request $request){

        $survey_id = $request->survey_id;
        $user_id = $request->id;
        $nps = NpsSurvey::find($survey_id);
        if($nps) {
            $shipper_id = explode(',',$nps->shipper_ids);
            if ($nps->all_shipper == 1 || in_array($user_id,$shipper_id)) {
                $nps_question = NpsSurveyQuestion::where('nps_survey_id', $survey_id);
                if ($nps_question->exists()) {
                    if($nps->recommendation_box == 1){
                        $data['recommendation_box'] = 1;
                    }
                    $data['survey_name'] = $nps->survey_name;
                    $data['nps'] = $nps_question->select('id', 'question', 'nps_survey_id')->get();

                    return response()->json(['status' => 1, 'question' => $data]);
                } else {
                    return response()->json(['status' => 0, 'question' => []]);
                }
            } else {
                return response()->json(['status' => 0, 'question' => []]);
            }
        }else{
            return response()->json(['status' => 0, 'question' => []]);
        }

    }

    public function ratting_submit(Request $request){

        if(isset($request->question_id)){


            $questions_id = $request->question_id;
            $nps_survey_id = $request->nps_survey_id;
            $ratting = $request->ratting;
            $user_id = Auth::user()->id;
            $recommendations_box = isset($request->recommendations_box) ? $request->recommendations_box : null;
            $date = Carbon::now();
            $total_ratting = 0;
            $promoters= 0;
            $passive = 0;
            $detractor = 0;

            $nps_shipper_rattings = NpsShipperRatting::where('nps_survey_id',$nps_survey_id)->where('user_id',$user_id);

            if(!$nps_shipper_rattings->exists()) {

                foreach ($questions_id as $key => $value) {
                    $rate = isset($ratting[$key]) ? $ratting[$key] : 0;
                    $nps_shipper_rattings = new NpsShipperRatting();
                    $nps_shipper_rattings->nps_survey_id = $nps_survey_id;
                    $nps_shipper_rattings->user_id = $user_id;
                    $nps_shipper_rattings->question_id = $questions_id[$key];
                    $nps_shipper_rattings->ratting = $rate;
                    $nps_shipper_rattings->created_at = $date;
                    $nps_shipper_rattings->updated_at = $date;
                    $nps_shipper_rattings->save();
                    if($rate >=0 && $rate<=2){
                        $detractor++;
                    }else if($rate == 3){
                        $passive++;
                    }else if($rate >=4 && $rate<=5){
                        $promoters++;
                    }
                    $total_ratting +=$rate;
                }
                $nps_survey_report = new NpsSurveyReport();
                $nps_survey_report->user_id = $user_id;
                $nps_survey_report->nps_survey_id = $nps_survey_id;
                $nps_survey_report->promoters = $promoters;
                $nps_survey_report->passive = $passive;
                $nps_survey_report->detractor = $detractor;
                $nps_survey_report->total_ratting = $total_ratting;
                $nps_survey_report->recommendations_box = $recommendations_box;
                $nps_survey_report->created_at = $date;
                $nps_survey_report->updated_at = $date;
                $nps_survey_report->save();

                session(['nps_survey' => null]); //Disable Survey After Submission

                return redirect()->back()->with('success', 'Survey Submitted');

            }else{
                return redirect()->back()->with('error', 'Survey Already Submitted');
            }

        }else{
            return redirect()->back()->with('error', 'Questions Not Found');
        }
    }

    public function nps_skip(Request $request)
    {
        if(isset($request->survey_id)){

            $nps_shipper_skip_surveys = NpsShipperSkipSurvey::where('nps_survey_id',$request->survey_id)->where('user_id',$request->id);
            if(!$nps_shipper_skip_surveys->exists()){
                $nps_shipper_skip_surveys = new NpsShipperSkipSurvey();
                $nps_shipper_skip_surveys->nps_survey_id = $request->survey_id;
                $nps_shipper_skip_surveys->user_id = $request->id;
                $nps_shipper_skip_surveys->skip_count = 1;
                $nps_shipper_skip_surveys->created_at = Carbon::now();
                $nps_shipper_skip_surveys->updated_at = Carbon::now();
                $nps_shipper_skip_surveys->save();

            }else{
                $nps_shipper_skip_surveys = $nps_shipper_skip_surveys->first();
                $nps_shipper_skip_surveys->skip_count = $nps_shipper_skip_surveys->skip_count+1;
                $nps_shipper_skip_surveys->updated_at = Carbon::now();
                $nps_shipper_skip_surveys->save();
            }
            session(['nps_survey' => null]); //Disable Survey After Skip

            return response()->json(['status' => 1]);
        }else{
            return response()->json(['status' => 0]);
        }
    }
}
