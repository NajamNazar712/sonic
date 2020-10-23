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
    }

    public function pettyCashSummaryReportProcess(Request $request)
    {
        $petty_cash_account_detail = [];
        if($request->has('petty_account_switch') && $request->get('petty_account_switch') == 'head'){
            $petty_cash_account_detail = DB::connection('reports')->table('petty_cash_account_heads as pca')
            ->join('petty_cash_statement_details as pcsd', 'pca.id', 'pcsd.account_head_id')
            ->select('pca.name as name', DB::raw('SUM(pcsd.amount) as amount'))
            ->groupBy('pca.id');
        }
        if($request->has('petty_account_switch') && $request->get('petty_account_switch') == 'title'){
            $petty_cash_account_detail = DB::connection('reports')->table('petty_cash_account_titles as pca')
            ->join('petty_cash_statement_details as pcsd', 'pca.id', 'pcsd.account_title_id')
//            ->select('pca.name as name', DB::raw('SUM(pcsd.amount) as amount'))
            ->select('pca.name as name', DB::raw('(SELECT SUM(amount) from petty_cash_statement_details as ptsd where ptsd.account_title_id = pca.id) as amount'))
            ->groupBy('pca.id');
        }

        $datatable = Datatables::of($petty_cash_account_detail);
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('pcsd.created_at', [$from,$to]);
        }
        return $datatable->make(true);
    }
}
