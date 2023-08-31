<?php

namespace App\Http\Controllers\Survey;

use Exception;
use Illuminate\Http\Request;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\CRM\CrmRequestFeedback;
use App\Http\Models\Survey\DisableAccountIntimationQuestion;
use App\Http\Models\Survey\DisableAccountIntimationSendSurvey;
use App\Http\Models\Survey\DisableAccountIntimationSubmitSurvey;

class DisabledAccountIntimationSurveyController extends Controller
{

    function survey($id)
    {

        $survey_exists = DisableAccountIntimationSendSurvey::where('random_id', $id);

        if ($survey_exists->exists()) {

            $survey_exists = $survey_exists->first();

            if ($survey_exists->status == 0) {
                $survey_id = $id;

                $questions = DisableAccountIntimationQuestion::leftjoin('admins as created_user', 'created_user.id', 'disable_account_intimation_questions.created_by')
                    ->leftjoin('admins as updated_user', 'updated_user.id', 'disable_account_intimation_questions.updated_by')
                    ->where('disable_account_intimation_questions.status', 1)
                    ->select(['disable_account_intimation_questions.*', 'created_user.name as created_by_name', 'updated_user.name as updated_by_name'])->get();

                return view('survey')->with(['questions' => $questions, 'survey_id' => $survey_id]);
            } else {
                $result = "You have already Submit this Form";
                $alert = "alert alert-success";

                return view('survey')->with(['result' => $result, 'alert' => $alert]);
            }
        } else {

            $result = "Invalid ID";
            $alert = "alert alert-danger";

            return view('survey')->with(['result' => $result, 'alert' => $alert]);

        }

    }

    function submit_survey(Request $request)
    {

        foreach ($request->option as $key => $val) {

            $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

            $SubmitSurvey = new DisableAccountIntimationSubmitSurvey();

            $SubmitSurvey->survey_id = $request->survey_id;
            $SubmitSurvey->question_id = $key;
            $SubmitSurvey->selected_option = $val;
            $SubmitSurvey->created_at = $timestamp;
            $SubmitSurvey->updated_at = $timestamp;

            $SubmitSurvey->save();

        }

        DisableAccountIntimationSendSurvey::where('random_id', $request->survey_id)->update(['status' => '1']);

        $result = "Thank you for your response";
        $alert = "alert alert-success";
        // return redirect()->back()->with('success', 'Order ID restricted successfully!');


        return view('survey')->with(['result' => $result, 'alert' => $alert]);
    }

    public function feedback_store(Request $request)
    {
        try {
            if (count($request->all()) != 0) {
                $request = $request->except('_token');
                foreach ($request as $key => $value) {
                    CrmRequestFeedback::where('crm_request_id', $key)->delete();
                    CrmRequestFeedback::insert([
                        'crm_request_id' => $key,
                        'rating_id' => $value,
                    ]);
                }

                return back()->with('success', 'ThankYou For Giving Us Feedback');
            } else {
                return back()->with('error', 'Please Submit Your Feedback');

            }
        } catch (Exception $ex) {
            return back()->with('error', $ex->getMessage());
        }
    }


    public function feedback_index($ids)
    {
        $ids = explode(',', $ids);
        $crm_closed_case = CrmRequest::leftjoin('crm_request_feedbacks', 'crm_requests.id', '=', 'crm_request_feedbacks.crm_request_id')
            ->whereNull('crm_request_feedbacks.crm_request_id')
            ->whereIn('crm_requests.id', $ids)
            ->select('crm_requests.*')
            ->get();

        return view('crm_closed_feedback')->with('crm_closed_cases', $crm_closed_case);
    }

}