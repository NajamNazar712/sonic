<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\PettyCashAccountHead;
use App\Http\Models\Admin\PettyCashAccountHeadAccountTitle;
use App\Http\Models\Admin\PettyCashAccountTitle;
use App\Http\Models\Admin\PettyCashStatement;
use App\Http\Models\Admin\PettyCashStatementDetail;
use App\Http\Models\City;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class AdminPettyCashController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }
    public function make_petty_cash_statement_index(){
        $head = PettyCashAccountHead::select('id','name')->get();
        $hubs = City::where('hub',1)->where('status',1)->select('id','name')->get();
        return view('admin.petty_cash.make')->with(['heads' => $head,'hubs' => $hubs]);
    }

    public function make_petty_cash_statement_check_reference(Request $request){
        $reference = $request->reference_id;
        if(PettyCashStatement::where('reference_no',$reference)->exists()){
            return 'true';
        }else{
            return 'false';
        }
    }

    public function make_petty_cash_statement_titles(Request $request){
        $account_head = $request->account_head;
        $title_ids = PettyCashAccountHeadAccountTitle::where('petty_cash_account_head_id',$account_head)->select('petty_cash_account_title_id')->get();

        $titles = PettyCashAccountTitle::whereIn('id',$title_ids)->select('id','name')->get();
        return response()->json(['status' => 1, 'titles'=>$titles]);
    }
    public function make_petty_cash_statement_submit(Request $request){
        if(PettyCashStatement::where('reference_no',$request->reference_no)->exists()){
            return ['status' => 0, 'error' => 'Reference No. not Unique'];
        }
        $selected_ids = explode(',', $request->input('selected_rows'));
        $petty_cash = new PettyCashStatement();
        $petty_cash->hub_id = $request->select_statement_hub;
        $petty_cash->reference_no = $request->reference_no;
        $petty_cash->from = $request->select_date_from_formatted;
        $petty_cash->to = $request->select_date_to_formatted;
        $petty_cash->created_by = Auth::id();
        $petty_cash->save();
        foreach ($selected_ids as $selected_id) {
            $hubId = "hub".$selected_id;
            $petty_detail = new PettyCashStatementDetail();
            $petty_detail->petty_cash_statement_id = $petty_cash->id;
            $petty_detail->account_head_id = $request->head[$selected_id];
            $petty_detail->account_title_id = $request->title[$selected_id];
            $petty_detail->hub_id = ($request->has($hubId)? $request->hub[$selected_id]:null);
            $petty_detail->date = $request->date[$selected_id];
            $petty_detail->expense_details = $request->expense[$selected_id];
            $petty_detail->amount = $request->amount[$selected_id];
            $petty_detail->reference_no = $request->reference[$selected_id];
            $petty_detail->remarks = $request->remarks[$selected_id];
            $petty_detail->save();
        }
        return redirect()->back()->with(['status' => 1, 'success' => 'Petty Cash Statement Successfully Created']);
    }

    public function edit_petty_cash_statement_index(Request $request, $id){
        $petty = PettyCashStatement::find($id);
        $head = PettyCashAccountHead::select('id','name')->get();
        $hubs = City::where('hub',1)->where('status',1)->select('id','name')->get();
        return view('admin.petty_cash.edit')->with(['heads' => $head,'hubs' => $hubs, 'petty_statement_details' => $petty]);
    }

    public function edit_petty_cash_statement_list(Request $request, $id){
        $petty_details = PettyCashStatementDetail::leftjoin('cities as h','h.id','=','petty_cash_statement_details.hub_id')
            ->select('petty_cash_statement_details.id as statement_detail_id','h.name as hub','petty_cash_statement_details.hub_id','petty_cash_statement_details.account_head_id','petty_cash_statement_details.account_title_id','petty_cash_statement_details.date','petty_cash_statement_details.expense_details','petty_cash_statement_details.amount','petty_cash_statement_details.reference_no','petty_cash_statement_details.remarks','petty_cash_statement_details.status')
            ->where('petty_cash_statement_details.petty_cash_statement_id',$id);
        return Datatables::of($petty_details)
            ->setRowAttr([
                'status' => function ($petty_details) {
                    return $petty_details->status;
                },
            ])
            ->addColumn('account_head',function ($petty_details){

                    $heads = PettyCashAccountHead::select('id','name')->get();
                    $drops = '';
                    $selected = '';
                    foreach ($heads as $status) {
                        if($status->id == $petty_details->account_head_id){
                            $selected = 'selected';
                        }else{
                            $selected = '';
                        }
                        $drops .= '<option value="' . $status->id . '" ' . $selected . '>' . $status->name . '</option>';
                    }
                    $select = '<select class="form-control form-control-sm select2 head_select" disabled name="head[' . $petty_details->statement_detail_id . ']" >' . $drops . '</select>';
                    return $select;

            })
            ->addColumn('account_title',function ($petty_details){

                    $titles = PettyCashAccountTitle::select('id','name')->get();
                    $drops = '';
                    $selected = '';
                    foreach ($titles as $status) {
                        if($status->id == $petty_details->account_title_id){
                            $selected = 'selected';
                        }else{
                            $selected = '';
                        }
                        $drops .= '<option value="' . $status->id . '" ' . $selected . '>' . $status->name . '</option>';
                    }
                    $select = '<select class="form-control form-control-sm select2 title_select" disabled name="title[' . $petty_details->statement_detail_id . ']" >' . $drops . '</select>';
                    return $select;

            })
            ->addColumn('hub_name',function ($petty_details){
                if($petty_details->hub_id != null){
                    $hubs = City::where('hub',1)->where('status',1)->select('id','name')->get();
                    $drops = '';
                    $selected = '';
                    foreach ($hubs as $hub) {
                        if($hub->id == $petty_details->hub_id){
                            $selected = 'selected';
                        }else{
                            $selected = '';
                        }
                        $drops .= '<option value="' . $hub->id . '" ' . $selected . '>' . $hub->name . '</option>';
                    }
                    $select = '<select class="form-control form-control-sm select2 hub_select" disabled name="hub[' . $petty_details->statement_detail_id . ']" >' . $drops . '</select>';
                    return $select;
                }else{
                    $hubs = City::where('hub',1)->where('status',1)->select('id','name')->get();
                    $drops = '';

                    foreach ($hubs as $hub) {
                        $drops .= '<option value="" selected></option>';
                        $drops .= '<option value="' . $hub->id . '">' . $hub->name . '</option>';
                    }
                    $select = '<select class="form-control form-control-sm select2 hub_select" disabled name="hub[' . $petty_details->statement_detail_id . ']" >' . $drops . '</select>';
                    return $select;
                }

            })
            ->editColumn('date',function ($petty_details){
                return Carbon::parse($petty_details->date)->toDateString();
            })
            ->editColumn('expense_details', function ($petty_details){
                $expense = '<input class="form-control" disabled value="' .$petty_details->expense_details. '" name="expense['.$petty_details->statement_detail_id.']">';
                return $expense;
            })
            ->editColumn('amount', function ($petty_details){
                $amount = '<input class="form-control" disabled value="' .$petty_details->amount. '" name="amount['.$petty_details->statement_detail_id.']">';
                return $amount;
            })
            ->editColumn('reference_no', function ($petty_details){
                $reference = '<input class="form-control" disabled value="' .$petty_details->reference_no. '" name="reference['.$petty_details->statement_detail_id.']">';
                return $reference;
            })
            ->editColumn('remarks', function ($petty_details){
                $remarks = '<input class="form-control" disabled value="' .$petty_details->remarks. '" name="remarks['.$petty_details->statement_detail_id.']">';
                return $remarks;
            })
            ->editColumn('status', function ($petty_details){
                if($petty_details->status == 0){
                    return "Pending";
                }else
                if($petty_details->status == 1){
                    return "Rejected";
                }else if($petty_details->status == 2){
                    return "Approved";
                }
            })
            ->addColumn('action',function ($petty){

                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';


//                if(session('role_id') == 1 || ($petty->status == 0 && session('role_id') == 10) || ($petty->status == 1 && session('role_id') == 3) || ($petty->status == 2 && session('role_id') == 7)){

                    $dropdown .= '<button type="button" class="dropdown-item approve" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Approve</div></button>';
                    $dropdown .= '<button type="button" class="dropdown-item reject" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x"></i></div><div class="col-9 offset-1">Reject</div></button>';

//                }
                return $dropdown;
            })
            ->make(true);
    }

    public function petty_cash_statements_index(){
        return view('admin.petty_cash.statements');
    }

    public function petty_cash_statements_list(Request $request){
        $petty = PettyCashStatement::join('cities as h','h.id','=', 'petty_cash_statements.hub_id')
            ->join('admins as cb','cb.id','=', 'petty_cash_statements.created_by')
            ->leftjoin('admins as sab', 'sab.id', '=', 'petty_cash_statements.station_approved_by')
            ->leftjoin('admins as oab', 'oab.id', '=', 'petty_cash_statements.operation_approved_by')
            ->leftjoin('admins as fab', 'fab.id', '=', 'petty_cash_statements.finance_approved_by')
            ->select('petty_cash_statements.id as statement_id','h.name as hub_name','petty_cash_statements.reference_no','petty_cash_statements.from','petty_cash_statements.to','cb.name as created_by','petty_cash_statements.created_at','sab.name as station_approved_by','petty_cash_statements.station_approved_at','oab.name as operation_approved_by','petty_cash_statements.operation_approved_at','fab.name as finance_approved_by','petty_cash_statements.finance_approved_at','petty_cash_statements.status');

        if (session('role_id') != 1) {
            $petty = $petty->whereIn('petty_cash_statements.hub_id', session('hubs'));
        }

        $petty = Datatables::of($petty)
            ->addColumn('date',function($petty){
                return Carbon::parse($petty->from)->toDateString().' - '.Carbon::parse($petty->to)->toDateString();
            })
            ->filterColumn('date',function ($query,$keyword){
                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('petty_cash_statements.from', 'like', '%' . $keyword . '%')
                            ->orWhere('petty_cash_statements.to', 'like', '%' . $keyword . '%');
                    });
                }

                else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('status',function ($petty){
                $status = '';
                if($petty->status == 0){
                    $status = 'Created';
                }else if($petty->status == 1){
                    $status = 'Station Approved';
                }else if($petty->status == 2){
                    $status = 'Operation Approved';
                }else if($petty->status == 3){
                    $status = 'Finance Approved';
                }
                return $status;
            })
            ->addColumn('action',function ($petty){
                $route = route('admin.petty_cash.statements.edit',['id' => $petty->statement_id]);
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= '<a href="'.$route.'" class="dropdown-item" ><i class="ft-plus-circle"></i> View Statement</a>';

                if(session('role_id') == 1 || ($petty->status == 0 && session('role_id') == 10) || ($petty->status == 1 && session('role_id') == 3) || ($petty->status == 2 && session('role_id') == 7)){

                    $dropdown .= '<button type="button" class="dropdown-item approve" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Approve</div></button>';

                }
                return $dropdown;
            })
            ->make(true);
        return $petty;
    }

    public function petty_cash_statements_approve(Request $request){
        $statement_id = $request->statement_id;
        $petty = PettyCashStatement::find($statement_id);
        if($petty){
            if($petty->station_approved_by == null){
                $petty->station_approved_by = Auth::id();
                $petty->station_approved_at = Carbon::now();
                $petty->status = 1;
                $petty->save();
            }else if($petty->operation_approved_by == null){
                $petty->operation_approved_by = Auth::id();
                $petty->operation_approved_at = Carbon::now();
                $petty->status = 2;
                $petty->save();
            }else if($petty->finance_approved_by == null){
                $petty->finance_approved_by = Auth::id();
                $petty->finance_approved_at = Carbon::now();
                $petty->status = 3;
                $petty->save();
            }
            return response()->json(['status' => 1, 'success' => 'Petty Cash Request Successfully Approved!']);
        }else{
            return response()->json(['status' => 0, 'error' => 'Petty Cash Statement not found!']);

        }

    }

    public function edit_petty_cash_statements_approve(Request $request){
        $id = $request->detail_id;
        if($id){
            $petty_details = PettyCashStatementDetail::find($id);
            if($petty_details){
                if($petty_details->status == 0 || $petty_details->status == 1){
                    $petty_details->status = 2;
                    $petty_details->updated_by = Auth::id();
                    $petty_details->save();
                    return response()->json(['status' => 1, 'success' => 'Petty Cash Statement Detail Successfully Approved!']);
                }else if($petty_details->status == 2){
                    return response()->json(['status' => 1, 'success' => 'Petty Cash Statement Detail Already Approved!']);
                }

            }else{
                return response()->json(['status' => 0, 'error' => 'Petty Cash Statement Details not found!']);
            }
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Petty Cash Statement Detail ID not found!']);
        }

    }
    public function edit_petty_cash_statements_reject(Request $request){
        $id = $request->detail_id;
        if($id){
            $petty_details = PettyCashStatementDetail::find($id);
            if($petty_details){
                if($petty_details->status == 0 || $petty_details->status == 2){
                    $petty_details->status = 1;
                    $petty_details->updated_by = Auth::id();
                    $petty_details->save();
                    return response()->json(['status' => 1, 'success' => 'Petty Cash Statement Detail Successfully Approved!']);
                }else if($petty_details->status == 1){
                    return response()->json(['status' => 0, 'error' => 'Petty Cash Statement Details Already Rejected!']);
                }

            }else{
                return response()->json(['status' => 0, 'error' => 'Petty Cash Statement Details not found!']);
            }
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Petty Cash Statement Detail ID not found!']);
        }

    }
}
