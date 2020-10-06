<?php

namespace App\Http\Controllers\Admins;

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
        return view('admin.petty_cash_expense.index')->with(['petty_cash_account_title' => $petty_cash_account_title]);
//        dd($petty_cash_account_title);
    }

    public function pettyCashSummaryReportProcess(Request $request)
    {
        $from = date('Y-m-d 00:00:00', strtotime($request->date_from));
        $to = date('Y-m-d 00:00:00', strtotime($request->date_to));

        $data['statements'] = DB::table('petty_cash_statement_details')
            ->join('petty_cash_account_titles', 'petty_cash_account_titles.id', '=', 'petty_cash_statement_details.account_title_id')
            ->join('petty_cash_account_heads', 'petty_cash_account_heads.id', '=', 'petty_cash_statement_details.account_head_id')
            ->join('cities', 'cities.hub_id', '=', 'petty_cash_statement_details.hub_id')
            ->where('account_title_id', $request->search_acount_title)->whereBetween('date', [$from, $to])
            ->select('petty_cash_account_titles.name as account_title', 'petty_cash_account_heads.name as account_head','cities.name as city', 'petty_cash_statement_details.amount')
            ->get();

        $datatable = Datatables::of($data['statements']);
        return $datatable->make(true);

    }
}
