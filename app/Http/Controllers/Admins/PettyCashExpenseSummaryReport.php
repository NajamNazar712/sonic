<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\PettyCashAccountTitle;
use App\Http\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
    }
}
