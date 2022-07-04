<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;
use App\Http\Models\Shipper\User;
use App\Http\Models\Admin\NpsSurvey;
use App\Http\Models\Admin\NpsSurveyQuestion;
use App\Http\Models\NpsShipperRatting;
use App\Http\Models\NpsSurveyReport;

class NpsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');

    }


    public function index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),548);
        return view('admin.nps.index');
    }

    public function list(Request $request)
    {
        $nps = NpsSurvey::join('admins as a', 'a.id', '=', 'nps_survey.admin_id')
            ->select(['nps_survey.id', 'nps_survey.survey_name','nps_survey.id as survey_id', 'nps_survey.start_time', 'nps_survey.end_time', 'nps_survey.recommendation_box', 'nps_survey.all_shipper', 'nps_survey.shipper_ids', 'nps_survey.status', 'nps_survey.admin_id', 'a.name as admin_name']);

        $datatables = Datatables::of($nps)
            ->editColumn('survey_id', function ($nps) {
                return str_pad($nps->survey_id, 6, 0, STR_PAD_LEFT);
            })
            ->editColumn('status', function ($nps) {
                return ($nps->status == 1 ? 'Active' : 'Deactivate');
            })
            ->editColumn('recommendation_box', function ($nps) {
                return ($nps->recommendation_box == 1 ? 'Yes' : 'No');
            })
            ->addColumn('shippers', function ($nps) {
                if ($nps->all_shipper == 0) {
                    $shiper = explode(',', $nps->shipper_ids);
                    $count = count($shiper);
                    return '<button class="btn btn-sm btn-outline-info align-middle shippers_modal">' . $count . '</button>';
                } else {
                    return 'All Shippers';
                }
            })
            ->addColumn('questions', function ($nps) {
                            $question = NpsSurveyQuestion::where('nps_survey_id',$nps->id)->count();
                    return '<button class="btn btn-sm btn-outline-info align-middle question_modal">' .$question . '</button>';
            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || count(array_intersect([753,754], session('permissions'))) !== 0) {
                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    if (session('role_id') == 1 || in_array(753, session('permissions'))) {
                        $dropdown .= '<a href="' . route('admin.nps.edit', $result->id) . '" class="dropdown-item edit_mapping"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit</div></div></a>';
                    }
                    if (session('role_id') == 1 || in_array(754, session('permissions'))) {
                        if ($result->status == 0) {
                            $dropdown .= '<button type="button" class="dropdown-item active_survey" rel="activate" data-target-id="' . $result->id . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate</div></button>';
                        } else {
                            $dropdown .= '<button type="button" class="dropdown-item active_survey" rel="deactivate" data-target-id="' . $result->id . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Deactivate</div></button>';
                        }
                    }
                    $dropdown .= '
                        </div>
                      </div>
                    ';

                    return $dropdown;
                } else {
                    return '';
                }
            });


        return $datatables->make(true);

    }

    public function view_shippers(Request $request)
    {
        $survey_id = $request->survey_id;
        $nps = NpsSurvey::where('id', $survey_id);
        if ($nps->exists()) {
            $nps = $nps->first();
            $shippers_ids = explode(',', $nps->shipper_ids);
            $data = User::select(['id', 'name'])->where('status', 3)->where('blacklist', 0)->whereIn('id', $shippers_ids)->get();
            return response()->json(['status' => 1, 'shippers' => $data]);
        } else {
            return response()->json(['status' => 0, 'shippers' => []]);
        }
    }

    public function view_questions(Request $request)
    {
        $survey_id = $request->survey_id;
        $nps_question = NpsSurveyQuestion::where('nps_survey_id', $survey_id);
        if ($nps_question->exists()) {
            $data = $nps_question->select('question')->get();
            return response()->json(['status' => 1, 'question' => $data]);
        } else {
            return response()->json(['status' => 0, 'question' => []]);
        }
    }


    public function add()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),549);
        $max_date = Carbon::now()->addYear(1);
        $min_date = Carbon::now()->subYear(1);
        $shippers = User::where('status', 3)->where('blacklist', 0)->select('id', 'name')->get();
        return view('admin.nps.add', compact('shippers', 'min_date', 'max_date'));
    }

    public function submit(Request $request)
    {
        $survey_name = $request->survey_name;
        $start_time = $request->start_time;
        $end_time = $request->end_time;
        $shipper_id = isset($request->shipper_ids) ? implode(',', $request->shipper_ids) : null;
        $all_shipper = isset($request->all_shipper) ? 1 : 0;
        $recommendation_box = isset($request->recommendation_box) ? 1 : 0;
        $date = Carbon::now();

        $NpsSurvey = new NpsSurvey();
        $NpsSurvey->survey_name = $survey_name;
        $NpsSurvey->start_time = $start_time;
        $NpsSurvey->end_time = $end_time;
        $NpsSurvey->shipper_ids = $shipper_id;
        $NpsSurvey->all_shipper = $all_shipper;
        $NpsSurvey->recommendation_box = $recommendation_box;
        $NpsSurvey->status = 0; //Default not active
        $NpsSurvey->admin_id = Auth::user()->id; //Default not active
        $NpsSurvey->created_at = $date;
        $NpsSurvey->updated_at = $date;
        $NpsSurvey->save();

        foreach ($request->question as $question) {
            $NpsSurveyQuestion = new NpsSurveyQuestion();
            $NpsSurveyQuestion->nps_survey_id = $NpsSurvey->id;
            $NpsSurveyQuestion->question = $question;
            $NpsSurveyQuestion->created_at = $date;
            $NpsSurveyQuestion->updated_at = $date;
            $NpsSurveyQuestion->save();
        }

        return redirect()->route('admin.nps.index')->with('success', 'Survey Added Successfully');


    }

    public function SurveyStatus(Request $request)
    {

        $id = $request->shid; //shipper id
        $status = $request->status;
        $survey = NpsSurvey::find($id);
        if (!$survey) {
            return back()->with('danger', 'Survey not found.');
        }
        if ($status == 'activate') {
            if ($survey->status == 0) {
                $survey->status = 1;
                $survey->updated_at = Carbon::now();
                $survey->save();

                NpsSurvey::where('id','!=',$id)->update(['status' => 0,'updated_at'=>Carbon::now()]);

                return redirect()->route('admin.nps.index')->with('success', 'Survey is Activated.');

            } else {
                return back()->with('danger', 'Survey is Already Activate');
            }
        }else{
            if ($survey->status == 1) {
                $survey->status = 0;
                $survey->updated_at = Carbon::now();
                $survey->save();

                return redirect()->route('admin.nps.index')->with('success', 'Survey is Deactivated.');

            } else {
                return back()->with('danger', 'Survey is Already Deactivate');
            }


        }

    }

    public function survey_edit ($id){

        $nps_survey = NpsSurvey::find($id);
        $max_date = Carbon::now()->addYear(1);
        $min_date = Carbon::now()->subYear(1);
        $shippers = User::where('status', 3)->where('blacklist', 0)->select('id', 'name')->get();
        return view('admin.nps.edit', compact('shippers', 'min_date', 'max_date','nps_survey','id'));
    }

    public function survey_update($id,Request $request)
    {

        $survey_name = $request->survey_name;
        $start_time = $request->start_time;
        $end_time = $request->end_time;
        $shipper_id = isset($request->shipper_ids) ? implode(',', $request->shipper_ids) : null;
        $all_shipper = isset($request->all_shipper) ? 1 : 0;
        $recommendation_box = isset($request->recommendation_box) ? 1 : 0;
        $date = Carbon::now();

        $NpsSurvey =  NpsSurvey::find($id);
        $NpsSurvey->survey_name = $survey_name;
        $NpsSurvey->start_time = $start_time;
        $NpsSurvey->end_time = $end_time;
        $NpsSurvey->shipper_ids = $shipper_id;
        $NpsSurvey->all_shipper = $all_shipper;
        $NpsSurvey->recommendation_box = $recommendation_box;
        $NpsSurvey->admin_id = Auth::user()->id;
        $NpsSurvey->updated_at = $date;
        $NpsSurvey->save();

        $NpsSurveyQuestion = NpsSurveyQuestion::where('nps_survey_id',$id)->delete();
        foreach ($request->question as $question) {
            $NpsSurveyQuestion= new NpsSurveyQuestion();
            $NpsSurveyQuestion->nps_survey_id = $NpsSurvey->id;
            $NpsSurveyQuestion->question = $question;
            $NpsSurveyQuestion->created_at = $date;
            $NpsSurveyQuestion->updated_at = $date;
            $NpsSurveyQuestion->save();
        }

        return redirect()->route('admin.nps.index')->with('success', 'Survey Updated Successfully');


    }

    public function response_report(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),550);
        $shippers = User::where('status', 3)->where('blacklist', 0)->select('id', 'name')->get();
        $nps_survey = NpsSurvey::orderby('id','desc')->select(['id','survey_name'])->get();
        return view('admin.nps.report.response',compact('shippers','nps_survey'));
    }

    public function response_report_list(Request $request){


        $nps = NpsSurveyReport::
             leftjoin('nps_survey as ns', 'ns.id', '=', 'nps_survey_reports.nps_survey_id')
            ->leftjoin('admins as a', 'a.id', '=', 'ns.admin_id')
            ->leftjoin('users as u', 'u.id', '=', 'nps_survey_reports.user_id')
            ->select(['u.name as shipper_name','nps_survey_reports.user_id','nps_survey_reports.nps_survey_id','ns.survey_name','nps_survey_reports.recommendations_box','nps_survey_reports.created_at as response_date','a.name as requested_by','nps_survey_reports.promoters','nps_survey_reports.passive','nps_survey_reports.detractor']);

        if ($request->get('requested_from_date') && $request->get('requested_to_date')) {
            $from = $request->get('requested_from_date');
            $to = $request->get('requested_to_date');
            $nps->whereBetween('nps_survey_reports.created_at', [$from, $to]);
        }

        if ($search_shipper = $request->get('search_shipper')) {
            $nps = $nps->whereIn('nps_survey_reports.user_id', $search_shipper);
        }

        if ($search_survey = $request->get('search_survey')) {
            $nps = $nps->where('nps_survey_reports.nps_survey_id', $search_survey);
        }

        $datatables = Datatables::of($nps);
        return $datatables->make(true);
    }

    public function consolidate_report(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),551);
        $nps_survey = NpsSurvey::orderby('id','desc')->select(['id','survey_name'])->get();
        return view('admin.nps.report.consolidate',compact('shippers','nps_survey'));
    }

    public function consolidate_report_list(Request $request){


        $nps = NpsSurvey::leftjoin('admins as a', 'a.id', '=', 'nps_survey.admin_id')
            ->leftJoin('nps_survey_reports as nsr','nsr.nps_survey_id', '=', 'nps_survey.id')
            ->select(['nps_survey.survey_name',DB::raw('count(nsr.nps_survey_id) as total_response'),DB::raw('sum(nsr.promoters) as promoters'),DB::raw('sum(nsr.passive) as passive'),DB::raw('sum(nsr.detractor) as detractor'),'nps_survey.all_shipper','nps_survey.shipper_ids'])
            ->groupby(['nps_survey.id']);



        if ($request->get('requested_from_date') && $request->get('requested_to_date')) {
            $from = $request->get('requested_from_date');
            $to = $request->get('requested_to_date');
            $nps->whereBetween('nps_survey.created_at', [$from, $to]);
        }

        if ($search_survey = $request->get('search_survey')) {
            $nps = $nps->where('nps_survey.id', $search_survey);
        }

        $datatables = Datatables::of($nps)
        ->addColumn('total_shippers', function ($nps) {
            if ($nps->all_shipper == 0) {
                $shiper = explode(',', $nps->shipper_ids);
                $count = count($shiper);
                return $count;
            } else {
                return User::where('status', 3)->where('blacklist', 0)->select('id', 'name')->count();

            }
        })
        ->addColumn('response_percentage', function ($nps) {
            $pecentage= 0;
           $total_shippers = $nps->total_shippers;
           $total_response = $nps->total_response;
           if(!empty($total_response) && !empty($total_shippers)) {
               $pecentage = ($total_response / $total_shippers) * 100;
           }
           return $pecentage;
        });

        return $datatables->make(true);
    }

}
