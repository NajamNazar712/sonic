<?php

namespace App\Http\Controllers\Survey;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Survey\disable_account_intimation_submit_survey;
use App\Http\Models\Survey\DisableAccountIntimationSendSurvey;
use App\Http\Models\Survey\DisableAccountIntimationQuestion;

class DisabledAccountIntimationSurveyController extends Controller
{
    
    function survey($id)
    {
        $survey_id = $id;
        $questions = DisableAccountIntimationQuestion::leftjoin('admins as created_user','created_user.id','disable_account_intimation_questions.created_by')
        ->leftjoin('admins as updated_user','updated_user.id','disable_account_intimation_questions.updated_by')
        ->where('disable_account_intimation_questions.status',1)
        ->select(['disable_account_intimation_questions.*', 'created_user.name as created_by_name' , 'updated_user.name as updated_by_name' ])->get();

        // dd($questions);

        return view('survey')->with(['questions' => $questions , 'survey_id' => $survey_id]);
    }

    function submit_survey(Request $request)
    {
        
        dd($request->all());

        // return view('survey')->with(['questions' => $questions]);
    }



    // function survey_details($id)
    // {
    //     dd("its working" , $id);
    // }
}
