<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\PettyCashAccountHead;
use App\Http\Models\Admin\PettyCashStatement;
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
            return response()->json(['status' => 1,'error' => 'Reference Number already exists']);
        }
    }

    public function make_petty_cash_statement_submit(Request $request){
        return $request;
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
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= '<button type="button" class="dropdown-item" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Statement</div></button>';

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
}
