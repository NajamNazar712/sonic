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
        $head = PettyCashAccountHead::where('status',1)->select('id','name')->get();
        if(session('role_id') == 1){
            $hub_cities = City::where('hub',1)->where('status',1)->select('id','name')->get();
        }else{
            $hub_cities = City::where('hub',1)->where('status',1)->whereIn('id',session('hubs'))->select('id','name')->get();
        }
        $cities = City::where('status',1)->select('id','name')->get();
        return view('admin.petty_cash.make')->with(['heads' => $head,'cities' => $cities,'hub_cities' => $hub_cities]);
    }

    public function make_petty_cash_statement_check_reference(Request $request){
        $reference = $request->reference;
        if(PettyCashStatement::where('reference_no',$reference)->exists()){
            return "true";
        }else{
            return "false";
        }
    }

    public function make_petty_cash_statement_titles(Request $request){
        $account_head = $request->account_head;
        $title_ids = PettyCashAccountHeadAccountTitle::where('petty_cash_account_head_id',$account_head)->join('petty_cash_account_titles as pct','pct.id','=', 'petty_cash_account_head_account_title.petty_cash_account_title_id')->where('pct.status',1)->select('petty_cash_account_title_id')->get();

        $titles = PettyCashAccountTitle::whereIn('id',$title_ids)->select('id','name')->get();
        return response()->json(['status' => 1, 'titles'=>$titles]);
    }
    public function make_petty_cash_statement_submit(Request $request){
        $total_amount = 0;
        if(PettyCashStatement::where('reference_no','=',$request->reference_no)->exists()){
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
            $total_amount += $request->amount[$selected_id];
            //$hubId = "hub.$selected_id";
            $petty_detail = new PettyCashStatementDetail();
            $petty_detail->petty_cash_statement_id = $petty_cash->id;
            $petty_detail->account_head_id = $request->head[$selected_id];
            $petty_detail->account_title_id = $request->title[$selected_id];
//            $petty_detail->hub_id = ($request->has($hubId)? $request->hub[$selected_id]:null);
            $petty_detail->hub_id = $request->hub[$selected_id];
            $petty_detail->date = $request->date[$selected_id];
            $petty_detail->expense_details = $request->expense[$selected_id];
            $petty_detail->amount = $request->amount[$selected_id];
            $petty_detail->reference_no = $request->reference[$selected_id];
            $petty_detail->remarks = $request->remarks[$selected_id];
            $petty_detail->save();
        }
        PettyCashStatement::where('id' , $petty_cash->id)->update(['total_amount' => $total_amount]);
        return redirect()->back()->with(['status' => 1, 'success' => 'Petty Cash Statement Successfully Created']);
    }

    public function edit_petty_cash_statement_index(Request $request, $id){
        $petty = PettyCashStatement::find($id);
        $head = PettyCashAccountHead::select('id','name')->get();
        $hubs = City::where('hub',1)->where('status',1)->select('id','name')->get();
        $cities = City::where('status',1)->select('id','name')->get();
        return view('admin.petty_cash.edit')->with(['heads' => $head,'hubs' => $hubs, 'cities' => $cities , 'petty_statement_details' => $petty]);
    }

    public function edit_petty_cash_statement_list(Request $request, $id){
        $petty_details = PettyCashStatementDetail::leftjoin('cities as h','h.id','=','petty_cash_statement_details.hub_id')
            ->join('petty_cash_statements as pcs','pcs.id', '=', 'petty_cash_statement_details.petty_cash_statement_id')
            ->select('petty_cash_statement_details.id as statement_detail_id','h.name as hub','petty_cash_statement_details.hub_id','petty_cash_statement_details.account_head_id','petty_cash_statement_details.account_title_id','petty_cash_statement_details.date','petty_cash_statement_details.expense_details','petty_cash_statement_details.amount','petty_cash_statement_details.reference_no','petty_cash_statement_details.remarks','petty_cash_statement_details.status','pcs.status as petty_status')
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
                    $select = '<select class="form-control form-control-sm select2 head_select" disabled name="head[' . $petty_details->statement_detail_id . ']" data-rule-required="true" data-msg-required="Account Head is required">' . $drops . '</select>';
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
                    $select = '<select class="form-control form-control-sm select2 title_select" disabled name="title[' . $petty_details->statement_detail_id . ']" data-rule-required="true" data-msg-required="Account Title is required">' . $drops . '</select>';
                    return $select;

            })
            ->addColumn('hub_name',function ($petty_details){
                if($petty_details->hub_id != null){
                    $hubs = City::where('status',1)->select('id','name')->get();
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
                    $select = '<select class="form-control form-control-sm select2 hub_select" disabled name="hub[' . $petty_details->statement_detail_id . ']" data-rule-required="true" data-msg-required="Hub is required">' . $drops . '</select>';
                    return $select;
                }else{
                    $hubs = City::where('hub',1)->where('status',1)->select('id','name')->get();
                    $drops = '';

                    foreach ($hubs as $hub) {
                        $drops .= '<option value="" selected></option>';
                        $drops .= '<option value="' . $hub->id . '">' . $hub->name . '</option>';
                    }
                    $select = '<select class="form-control form-control-sm select2 hub_select" disabled name="hub[' . $petty_details->statement_detail_id . ']" data-rule-required="true" data-msg-required="Hub is required">' . $drops . '</select>';
                    return $select;
                }

            })
            ->editColumn('date',function ($petty_details){
                return Carbon::parse($petty_details->date)->toDateString();
            })
            ->editColumn('expense_details', function ($petty_details){
                $expense = '<textarea class="form-control" disabled name="expense['.$petty_details->statement_detail_id.']" data-rule-required="true" data-msg-required="Expense Detail is required">'.$petty_details->expense_details.'</textarea>';
                return $expense;
            })
            ->editColumn('amount', function ($petty_details){
                $amount = '<input class="form-control" disabled value="' .$petty_details->amount. '" name="amount['.$petty_details->statement_detail_id.']" data-rule-required="true" data-msg-required="Amount is required">';
                return $amount;
            })
            ->editColumn('reference_no', function ($petty_details){
                $reference = '<input class="form-control" disabled value="' .$petty_details->reference_no. '" name="reference['.$petty_details->statement_detail_id.']" data-rule-required="true" data-msg-required="Reference No. is required">';
                return $reference;
            })
            ->editColumn('remarks', function ($petty_details){
                $remarks = '<textarea class="form-control" disabled name="remarks['.$petty_details->statement_detail_id.']">'.$petty_details->remarks.'</textarea>';
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
                $dropdown = '';
                if(session('role_id') == 1 || ($petty->petty_status == 2 && (session('role_id') == 2 || session('role_id') == 7 || session('role_id') == 14))){
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                    $dropdown .= '<button type="button" class="dropdown-item approve" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Approve</div></button>';
                    $dropdown .= '<button type="button" class="dropdown-item reject" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x"></i></div><div class="col-9 offset-1">Reject</div></button>';

                }
                return $dropdown;
            })
            ->make(true);
    }

    public function petty_cash_statements_index(){
        if(session('role_id') == 1){
            $hubs = City::where('hub',1)->where('status',1)->get();
        }else{
            $hubs = City::where('hub',1)->where('status',1)->whereIn('id',session('hubs'))->get();
        }
        return view('admin.petty_cash.statements')->with(['hubs'=> $hubs]);
    }

    public function petty_cash_statements_list(Request $request){
        $petty = PettyCashStatement::join('cities as h','h.id','=', 'petty_cash_statements.hub_id')
            ->join('admins as cb','cb.id','=', 'petty_cash_statements.created_by')
            ->leftjoin('admins as sab', 'sab.id', '=', 'petty_cash_statements.station_approved_by')
            ->leftjoin('admins as oab', 'oab.id', '=', 'petty_cash_statements.operation_approved_by')
            ->leftjoin('admins as fab', 'fab.id', '=', 'petty_cash_statements.finance_approved_by')
            ->select('petty_cash_statements.id as statement_id','petty_cash_statements.id as statement_link','h.name as hub_name','petty_cash_statements.reference_no','petty_cash_statements.from','petty_cash_statements.to','cb.name as created_by','petty_cash_statements.created_at','sab.name as station_approved_by','petty_cash_statements.station_approved_at','oab.name as operation_approved_by','petty_cash_statements.operation_approved_at','fab.name as finance_approved_by','petty_cash_statements.finance_approved_at','petty_cash_statements.status','petty_cash_statements.total_amount')
        ->where('petty_cash_statements.status','<',3);

        if (session('role_id') != 1) {
            $petty = $petty->whereIn('petty_cash_statements.hub_id', session('hubs'));
        }

        $petty = Datatables::of($petty)
            ->editColumn('statement_link', function ($petty){
                return '<button class="btn btn-sm btn-outline-info align-middle"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . $petty->statement_link . '</span></button>';
            })
            ->editColumn('total_amount', function($shipment){
                return number_format($shipment->total_amount);
            })
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

                $dropdown .= '<a href="'.$route.'" class="dropdown-item" ><i class="ft-eye"></i> View Details</a>';

//                if((session('role_id') == 1) || ($petty->status == 0 && (session('role_id') == 9) || session('role_id') == 10) || ($petty->status == 1 && (session('role_id') == 3) || session('role_id') == 8 || session('role_id') == 20) || ($petty->status == 2 && (session('role_id') == 2 || session('role_id') == 7 || session('role_id') == 14))){
                if((session('role_id') == 1) || in_array(173, session('permissions')) || in_array(190, session('permissions')) || in_array(191, session('permissions'))){
                    if((session('role_id') == 1) || ($petty->status == 0 && session('department_id') == 6) || ($petty->status == 1 && (session('department_id') == 6)) || ($petty->status == 2 && session('department_id') == 4)) {
                        $dropdown .= '<button type="button" class="dropdown-item approve" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div>Approve</button>';
                    }
                }
                return $dropdown;
            });
        if ($hub = $request->get('search_hub')) {
            $petty->where('h.id', '=', $hub);
        }
        if ($search_date = $request->get('search_creation_date')) {
            $petty->whereDate('petty_cash_statements.created_at', $search_date);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $petty->whereBetween('petty_cash_statements.created_at', [$from,$to]);
        }
        return $petty->make(true);
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
                    return response()->json(['status' => 1, 'success' => 'Petty Cash Statement Detail Successfully Rejected!']);
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

    public function edit_petty_cash_statements_submit(Request $request){
//        return $request;
        $selected_ids = explode(',', $request->input('selected_rows'));
        $statement_id = $request->petty_statement_id;
        $petty_cash = PettyCashStatement::find($statement_id);
        $total_amount = 0;
        if($petty_cash){
            foreach ($selected_ids as $selected_id) {
//                $hubId = "hub.$selected_id";
                $total_amount += $request->amount[$selected_id];
                $petty_detail = PettyCashStatementDetail::where('petty_cash_statement_id',$petty_cash->id)->where('id',$selected_id)->first();

                if(session('role_id') == 1 || (session('role_id') == 2 || session('role_id') == 7 || session('role_id') == 14)){
                    $petty_detail->account_head_id = $request->head[$selected_id];
                    $petty_detail->account_title_id = $request->title[$selected_id];
                    $petty_detail->hub_id = $request->hub[$selected_id];
//                    $petty_detail->hub_id = ($request->has($hubId) ? $request->hub[$selected_id] : null);

                }

                $petty_detail->expense_details = $request->expense[$selected_id];
                $petty_detail->amount = $request->amount[$selected_id];
                $petty_detail->reference_no = $request->reference[$selected_id];
                $petty_detail->remarks = $request->remarks[$selected_id];
                $petty_detail->save();
            }
            $petty_cash->total_amount = $total_amount;
            $petty_cash->save();
            return redirect()->back()->with(['status' => 1, 'success' => 'Petty Cash Statement Successfully Updated!']);
        }
        else{
            return redirect()->back()->with(['status' => 0, 'error' => 'Petty Cash Statement With This ID Not Found!']);
        }

    }

    public function approved_petty_cash_statements_index(){
        return view('admin.petty_cash.approved');
    }

    public function approved_petty_cash_statements_list(Request $request){
        $petty = PettyCashStatement::join('cities as h','h.id','=', 'petty_cash_statements.hub_id')
            ->join('admins as cb','cb.id','=', 'petty_cash_statements.created_by')
            ->leftjoin('admins as sab', 'sab.id', '=', 'petty_cash_statements.station_approved_by')
            ->leftjoin('admins as oab', 'oab.id', '=', 'petty_cash_statements.operation_approved_by')
            ->leftjoin('admins as fab', 'fab.id', '=', 'petty_cash_statements.finance_approved_by')
            ->select('petty_cash_statements.id as statement_id','petty_cash_statements.id as statement_link','h.name as hub_name','petty_cash_statements.reference_no','petty_cash_statements.from','petty_cash_statements.to','cb.name as created_by','petty_cash_statements.created_at','sab.name as station_approved_by','petty_cash_statements.station_approved_at','oab.name as operation_approved_by','petty_cash_statements.operation_approved_at','fab.name as finance_approved_by','petty_cash_statements.finance_approved_at','petty_cash_statements.status','petty_cash_statements.total_amount')
            ->whereIn('petty_cash_statements.status',[3,4,5]);

        if (session('role_id') != 1) {
            $petty = $petty->whereIn('petty_cash_statements.hub_id', session('hubs'));
        }

        $petty = Datatables::of($petty)
            ->editColumn('statement_link', function ($petty){
                return '<button class="btn btn-sm btn-outline-info align-middle"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . $petty->statement_link . '</span></button>';
            })
            ->editColumn('total_amount', function($shipment){
                return number_format($shipment->total_amount);
            })
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
                if($petty->status == 3){
                    $status = 'Finance Approved';
                }else if($petty->status == 4){
                    $status = 'Paid';

                }else if($petty->status == 5){
                    $status = 'Adjusted';
                }
                return $status;
            })
            ->addColumn('action',function ($petty){
                $dropdown = '';
                if(session('role_id') == 1 || (session('role_id') == 2 || session('role_id') == 7 || session('role_id') == 14)){
                    if($petty->status == 3){
                    $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                        $dropdown .= '<button type="button" class="dropdown-item paid" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Paid</div></button>';

                        $dropdown .= '<button type="button" class="dropdown-item adjusted" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-clipboard"></i></div><div class="col-9 offset-1">Adjusted</div></button>';
                    }

                }
                return $dropdown;
            })
            ->make(true);
        return $petty;
    }

    public function approved_petty_cash_statements_paid(Request $request){
        $id = $request->statement_id;
        if($id){
            $petty_details = PettyCashStatement::find($id);
            if($petty_details){
                if($petty_details->status == 3){
                    $petty_details->status = 4;
                    $petty_details->save();
                    return response()->json(['status' => 1, 'success' => 'Petty Cash Statement Successfully Paid!']);
                }else if($petty_details->status == 4){
                    return response()->json(['status' => 1, 'success' => 'Petty Cash Statement Already Paid!']);
                }else if($petty_details->status == 5){
                    return response()->json(['status' => 0, 'error' => 'Petty Cash Statement Is Adjust!']);
                }

            }else{
                return response()->json(['status' => 0, 'error' => 'Petty Cash Statement not found!']);
            }
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Petty Cash Statement ID not found!']);
        }
    }
    public function approved_petty_cash_statements_adjusted(Request $request){
        $id = $request->statement_id;
        if($id){
            $petty_details = PettyCashStatement::find($id);
            if($petty_details){
                if($petty_details->status == 3){
                    $petty_details->status = 5;
                    $petty_details->save();
                    return response()->json(['status' => 1, 'success' => 'Petty Cash Statement Successfully Adjust!']);
                }else if($petty_details->status == 5){
                    return response()->json(['status' => 1, 'success' => 'Petty Cash Statement Already Adjust!']);
                }else if($petty_details->status == 4){
                    return response()->json(['status' => 0, 'error' => 'Petty Cash Statement Is Paid!']);
                }

            }else{
                return response()->json(['status' => 0, 'error' => 'Petty Cash Statement not found!']);
            }
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Petty Cash Statement ID not found!']);
        }
    }

    public function statement_print(Request $request){
        $statement_id = $request->id;
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Petty Cash Statement</title>

                    <style>
                      @page {
                        size: A4 portrait;
                      }

                      * {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                      }

                      body {
                        background: none !important;
                        color: #09262e !important;
                        font-size: 0.9rem !important;
                      }

                      hr {
                        border-top: 1px dashed #000000;
                      }

                      table.table-bordered {
                        page-break-inside: avoid;
                      }

                      table.table-bordered tbody tr td {
                        border: 1px solid #09262e !important;
                      }

                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }

                      .border {
                        border: 1px solid #09262e !important;
                      }

                      td.replacement span {
                        width: 22px;
                      }

                      td.replacement span img {
                        display: block;
                        width: 100%;
                        margin: auto;
                        background: #c8c8c8;
                        border-radius: 25px;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
      ';
        $petty_cash_statement = PettyCashStatement::where('id', $statement_id);
        if ($petty_cash_statement->exists()) {
            $total_statements = 0;
            $statement_details = PettyCashStatementDetail::where('petty_cash_statement_id', $statement_id)->get();

            $petty_statement_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Creation Date</strong></td>
                            <td class="color primary"><strong>Chart of Account</strong></td>
                            <td class="color primary"><strong>Account Title</strong></td>
                            <td class="color primary"><strong>Details</strong></td>
                            <td class="color primary"><strong>Location</strong></td>
                            <td class="color primary"><strong>Reference No.</strong></td>
                            <td class="color primary"><strong>Amount</strong></td>
                            <td class="color primary"><strong>Remarks</strong></td>
                          </tr>
        ';


            foreach ($statement_details as $detail) {
                $total_statements++;


                $shipment_details_row_start = '
                          <tr>
                            <td>' . $total_statements . '</td>
                            <td>' . Carbon::parse($detail->date)->toDateString() . '</td>
                            <td>' . $detail->heads->name . '</td>
                            <td>' . $detail->titles->name . '</td>
                            <td>' . $detail->expense_details . '</td>
                            <td>' . $detail->location->name . '</td>
                            <td>' . $detail->reference_no . '</td>
                            <td>Rs ' . number_format($detail->amount) . '</td>
                            <td>' . $detail->remarks . '</td>
                ';

                $shipment_details_row_start .= '
                          </tr>
                ';
                $petty_statement_details .= $shipment_details_row_start;
            }
            $petty_statement_details .= '
                        </tbody>
                      </table>
        ';
            $petty_cash_statement = $petty_cash_statement->first();
            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Petty Cash Statement</strong></td>
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Statement ID</strong></td>
                            <td>' . $petty_cash_statement->id . '</td>
                            <td rowspan="7" class="pl-1 pr-1 text-center align-middle">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($request->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Hub</strong></td>
                            <td>' . $petty_cash_statement->hub->name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Reference No.</strong></td>
                            <td>' . $petty_cash_statement->reference_no . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Period</strong></td>
                            <td>' . Carbon::parse($petty_cash_statement->from)->toDateString() . ' -- ' . Carbon::parse($petty_cash_statement->to)->toDateString(). '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Amount</strong></td>
                            <td>' . $petty_cash_statement->total_amount . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Statement Details</strong></td>
                            <td>' . $total_statements . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';
            $html .= $main_details;
            $html .= $petty_statement_details;

        }


        $html .= '
                    </div>

                    <script>
                      window.onload = function() {
                        window.print();
                      }
                    </script>
                  </body>
                </html>
      ';

        return $html;
    }
}
