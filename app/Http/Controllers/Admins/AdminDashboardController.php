<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Shipper\User;
use App\Http\Models\CityInfo;
use App\Http\Models\PickupType;
use Yajra\Datatables\Datatables;
use Illuminate\Database\Eloquent\Collection;

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
        return view('admin.accounts.pending_accounts_list');
    }
    public function activeAccountsList(){
        return view('admin.accounts.active_accounts_list');

    }
    public function blockAccountsList(){
        return view('admin.accounts.block_accounts_list');
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
    public function activeAccountListAjax(){
       $users = User::join('city_infos', 'users.city_code', '=', 'city_infos.city_code')
            ->select(['users.id', 'users.name', 'city_infos.city_name' ,'users.poc','users.phone','users.address', 'users.email'])->where('active',1)->where('blacklist',0);

        return Datatables::of($users)->addColumn("action", function ($result) {
                                            return " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='#' class='dropdown-item' data-target-id='{$result->id}' data-toggle='modal' data-target='#BankInfoModal'><i class='ft-plus-circle primary'></i> View Bank Info</a>
                                              <a href='#' class='dropdown-item' data-target-id='{$result->id}' data-toggle='modal' data-target='#ShippingInfoModal'><i class='ft-plus-circle primary'></i> View Shipping Info</a>

                                            </div>
                                            </span>";
                                        })
                                      ->make(true);

    }
    public function pendingAccountListAjax(){
        $users = User::join('city_infos', 'users.city_code', '=', 'city_infos.city_code')
            ->select(['users.id', 'users.name', 'city_infos.city_name' ,'users.poc','users.phone','users.address', 'users.email'])->where('active',0)->where('blacklist',0);

        return Datatables::of($users)->addColumn("action", function ($result) {
                                            return " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='#' class='dropdown-item' data-target-id='{$result->id}' data-toggle='modal' data-target='#BankInfoModal'><i class='ft-plus-circle primary'></i> View Bank Info</a>
                                              <a href='#' class='dropdown-item' data-target-id='{$result->id}' data-toggle='modal' data-target='#ShippingInfoModal'><i class='ft-plus-circle primary'></i> View Shipping Info</a>

                                            </div>
                                            </span>";
                                        })
                                      ->make(true);

    }
    public function blockAccountListAjax(){
        $users = User::join('city_infos', 'users.city_code', '=', 'city_infos.city_code')
            ->select(['users.id', 'users.name', 'city_infos.city_name' ,'users.poc','users.phone','users.address', 'users.email'])->where('blacklist',1);

        return Datatables::of($users)->addColumn("action", function ($result) {
                                            return " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='#' class='dropdown-item' data-target-id='{$result->id}' data-toggle='modal' data-target='#BankInfoModal'><i class='ft-plus-circle primary'></i> View Bank Info</a>
                                              <a href='#' class='dropdown-item' data-target-id='{$result->id}' data-toggle='modal' data-target='#ShippingInfoModal'><i class='ft-plus-circle primary'></i> View Shipping Info</a>

                                            </div>
                                            </span>";
                                        })
                                      ->make(true);

    }

}
