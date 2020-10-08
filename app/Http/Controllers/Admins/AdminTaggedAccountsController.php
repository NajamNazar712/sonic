<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Commission\SalesCommission;
use App\Http\Models\Commission\SalesCommissionUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

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
       $accounts = SalesCommission::join('sales_commission_users as scu','sales_commissions.id','=','scu.sales_commission_id')
           ->join('users as u','u.id','=','sales_commissions.shipper_id')
            ->select('sales_commissions.shipper_id as shipper_id','scu.commission as commission','u.name as shipper_name');

       if(session('role_id') != 1){
           $accounts = $accounts ->where('scu.user_id',Auth::id())
               ->whereIn('scu.tier_id',[1,2,3,4]);
       }
        return Datatables::of($accounts)->make(true);
    }
}
