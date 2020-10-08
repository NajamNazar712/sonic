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
           ->join('cities as c', 'u.city_id', '=', 'c.id')
           ->leftjoin('products as p','p.id','=','u.product_id')
            ->select('sales_commissions.shipper_id as shipper_id','scu.commission as commission','u.name as shipper_name','c.name as city','u.rejected_reason as rejected_reason','u.poc as poc','u.phone as phone','u.address as address', 'u.email as email','u.id as account_id','p.product_name as product_type','u.rate_status as rate_status','u.status as status')->whereIn('u.status',[0,1,2,5])->where('blacklist',0)->where('u.email_verified',1);;

       if(session('role_id') != 1){
           $accounts = $accounts ->where('scu.user_id',Auth::id())
               ->whereIn('scu.tier_id',[2,3]);
       }
       return  Datatables::of($accounts)
           ->editColumn('status', function ($users) {
               return $users->status == 0? 'Request Received': ($users->status == 1? 'Rates Added' : ($users->status == 2? 'Pending for Activation': ($users->status == 5? 'Rates Rejected':'')));
        })->make(true);
    }
}
