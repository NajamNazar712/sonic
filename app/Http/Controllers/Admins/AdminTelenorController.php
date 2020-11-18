<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\TelenorCallResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class AdminTelenorController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    //Response Screen
    public function telenor_response(){
        return view('admin.telenor.call_response');
    }

    public function telenor_response_list(){
        $telenor = TelenorCallResponse::leftjoin('telenor_api_errors as tae', 'tae.id', '=', 'telenor_call_responses.error_id')
        ->select('telenor_call_responses.id', 'telenor_call_responses.tracking_number', 'telenor_call_responses.status', 'telenor_call_responses.response', 'telenor_call_responses.response_status', 'telenor_call_responses.created_at', 'tae.text as error');
        $datatables = Datatables::of($telenor)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('status',function ($data){
                if($data->status == 0){
                    return 'Pending for Call';
                }else if($data->status == 1){
                    return 'Call Sent';
                }else if($data->status == 2){
                    return 'Response Received';
                }else if($data->status == 3){
                    return 'Error In API';
                }else{
                    return 'Invalid';
                }
            })
            ->editColumn('response',function ($data){
                if($data->response == 1){
                    return 'Delivered';
                }else if($data->response == 2){
                    return 'Not Delivered';
                }
                else if($data->response == 3){
                    return 'Not Responded';
                }
                else{
                    return '-';
                }
            })
            ->editColumn('response_status',function ($data){
                if($data->response_status != ''){
                    if($data->response_status == 0){
                        return 'Call Scheduled';
                    }
                    else if($data->response_status == 1){
                        return 'Call Sent';
                    }
                    else if($data->response_status == 2){
                        return 'Recipient Busy';
                    }
                    else if($data->response_status == 3){
                        return 'Not Responding';
                    }
                    else if($data->response_status == 4){
                        return 'Not Answering';
                    }
                }
                else{
                    return '-';
                }
            })
            ->editColumn('error',function ($data){
                if($data->error != null){
                    return $data->error;
                }
                else{
                    return '-';
                }
            });
        return $datatables->make(true);
    }
    //Response Screen
}
