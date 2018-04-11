<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Shipper\User;
use App\Http\Models\CityInfo;
class AdminDashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        return view('admin.dashboard');
    }
    public function ecommerce(){
        return view('admin.ecommerce');
    }
    public function orderList(){
        return view('admin.order_management');
    }
    public function orderPending(){
        return view('admin.pending_booked_orders');
    }
    public function pendingAccountsList(){
       $pendingAccounts =  User::where('active',0)->get();
//       $pendingAccounts = User::find(2);
//       return $pendingAccounts->city();
        return view('admin.accounts.pending_accounts_list')->with('accounts',$pendingAccounts);
    }
    public function cities(){
//        $city = CityInfo::find(11)->users;
        $city = confide::user();
        dd($city);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function viewBankInfo($id){
        $user = User::find($id)->bank;

        $returnHTML = view('admin/components/bank')->with('bank',$user)->render();
        return response()->json($returnHTML);
    }
}
