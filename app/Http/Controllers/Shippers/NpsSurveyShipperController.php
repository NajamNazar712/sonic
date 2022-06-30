<?php

namespace App\Http\Controllers\Shippers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\NpsSurvey;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Admin\NpsSurveyQuestion;

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

    }
}
