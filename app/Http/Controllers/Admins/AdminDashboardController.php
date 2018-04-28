<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Shipper\User;
use App\Http\Models\CityInfo;
use App\Http\Models\PickupType;
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
    public function activeAccountsList(){
        $activeAccounts =  User::where('active',1)->where('blacklist',0)->get();
        return view('admin.accounts.active_accounts_list')->with('accounts',$activeAccounts);

    }
    public function blockAccountsList(){
        $blackAccounts = User::where('blacklist',1)->get();
        return view('admin.accounts.block_accounts_list')->with('accounts',$blackAccounts);
    }
    public function UserStatus(Request $request){
//        dd($request);
        $id = $request->shid;
        $status = $request->status;
//        $active = User::where('id',$id)->s
        if($status == 'unblock'){
            $user = User::where('id',$id)->where('blacklist',1)->update(['blacklist'=>0]);
            $active = User::find($id)->first()->active;
            if($user == 1){
                if($active == 1){
                    return redirect()->route('admin.accounts.active');
                }elseif($active == 0){
                    return redirect()->route('admin.accounts.pending');
                }
            }
        }
    }
    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function viewBankInfo($id){
        $user = User::find($id);
        $bank = $user->bank;
        $returnHTML = view('admin/components/bank')->with(['bank'=>$bank,'user'=>$user])->render();
        return response()->json($returnHTML);
    }
    public function viewShippingInfo($id){
        $user = User::find($id);
        $shipping = $user->shipping;
        $returnHTML = view('admin.components.shipping')->with(['shipping'=>$shipping,'user'=>$user])->render();
        return response()->json($returnHTML);
    }
    public function viewShipperRates($id){
        $user = User::find($id);
        $shipping = $user->shipping;
        $returnHTML = view('admin.components.shipping')->with(['shipping'=>$shipping,'user'=>$user])->render();
        return response()->json($returnHTML);
    }
    public function pickup(){
//        $cit = CityInfo::all()->where('city_code','202');
        $cit = PickupType::find(1)->cities()->orderBy('city_name')->get();
//        $cite = $cit->cities()->get();
//        return $cite;


//    return $cit;
    }
    public function addRatesView($id){
        $user = User::find($id);
        return view('admin.accounts.add_rates')->with('shipper',$user);
    }
    public function addRates(Request $request){
        return $request;
//        $user = User::find($id);
//        return view('admin.accounts.add_rates')->with('shipper',$user);
    }
}
