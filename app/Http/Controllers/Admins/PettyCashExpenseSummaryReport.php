<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\PettyCashAccountHead;
use App\Http\Models\Admin\PettyCashAccountTitle;
use App\Http\Models\Admin\PettyCashStatementDetail;
use App\Http\Models\City;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use DB;

class PettyCashExpenseSummaryReport extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function index(){
        $petty_cash_account_title = PettyCashAccountTitle::select('id','name')->get();
        $petty_cash_account_head = PettyCashAccountHead::select('id','name')->get();
        return view('admin.petty_cash_expense.index')->with(['petty_cash_account_title' => $petty_cash_account_title,'petty_cash_account_head' => $petty_cash_account_head]);
   //        dd($petty_cash_account_title);
    }

    public function pettyCashSummaryReportProcess(Request $request)
    {
        $petty_cash_account_detail =PettyCashStatementDetail::join('petty_cash_account_titles', 'petty_cash_account_titles.id', '=', 'petty_cash_statement_details.account_title_id')
            ->leftjoin('petty_cash_account_heads', 'petty_cash_account_heads.id', '=', 'petty_cash_statement_details.account_head_id')
            /*->join('cities', 'cities.id', '=', 'petty_cash_statement_details.hub_id')*/->groupBy('petty_cash_account_titles.id')
            ->select('petty_cash_account_titles.name as account_title', 'petty_cash_account_heads.name as account_head',/*'cities.name as city',*/ 'petty_cash_statement_details.amount');
        $datatable = Datatables::of($petty_cash_account_detail);
        if($petty_cash_account_head = $request->get('petty_cash_account_head')){
            $petty_cash_account_detail = $petty_cash_account_detail->where('petty_cash_account_heads.id', '=',$petty_cash_account_head);
        }
        if($petty_cash_account_titles = $request->get('petty_cash_account_titles')){
            $petty_cash_account_detail = $petty_cash_account_detail->where('petty_cash_account_titles.id', '=',$petty_cash_account_titles);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('petty_cash_statement_details.created_at', [$from,$to]);
        }
        return $datatable->make(true);
    }
}
