<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Commission\SalesCommissionUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminTaggedAccountsController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index(){
        return view('admin.tagged_accounts.index');
    }

    public function list(Request $request){
        $accounts = SalesCommissionUser::join('sales_tiers as st','st.id','=','sales_commission_users.tier_id')
            ->join('sales_commissions as sc','sc.id','=','')
            ->select('st.commission')
        ->where('sales_commission_users.user_id',session('user_id'))
        ->whereIn('st.id',[2,3]);
    }
}
