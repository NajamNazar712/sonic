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
        ActivityTrailController::createActivityTrailLog(Auth::id(),287);
        return view('admin.tagged_accounts.index');
    }

    public function list(Request $request){

        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),288);
        }
       $accounts = SalesCommissionUser::leftjoin('sales_commissions as sc','sc.id','=','sales_commission_users.sales_commission_id')
           ->join('users as u','u.id','=','sc.shipper_id')
           ->join('cities as c', 'u.city_id', '=', 'c.id')
           ->leftjoin('products as p','p.id','=','u.product_id')
            ->select('sc.shipper_id as shipper_id','sales_commission_users.commission as commission','u.name as shipper_name','c.name as city','u.poc as poc','u.phone as phone','u.address as address', 'u.email as email','u.id as account_id','p.product_name as product_type','u.status as status')/*->whereIn('u.status',[0,1,2,5])*/->where('blacklist',0)->where('u.email_verified',1)
           ->whereIn('sales_commission_users.tier_id',[2,3])->where('sc.status',2);

       if(session('role_id') != 1){
           $accounts = $accounts ->where('sales_commission_users.user_id',Auth::id());
       }
       return  Datatables::of($accounts)
           ->editColumn('status', function ($users) {
               return $users->status == 0? 'Request Received': ($users->status == 1? 'Rates Added' : ($users->status == 2? 'Pending for Activation': ($users->status == 5? 'Rates Rejected':'')));
        })->make(true);
    }
}
