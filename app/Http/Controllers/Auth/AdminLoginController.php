<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\City;
use App\Http\Models\Commission\SalesCommissionUser;
use App\Http\Models\Commission\SalesTier;
use App\Http\Models\MultipleSaleLead;
use App\Http\Models\SaleTierTag;
use App\Http\Models\Shipper\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\AdminRoleModulePermission;
use App\Http\Models\AgentReturnConfirmation;
use Illuminate\Support\Facades\Hash;

class AdminLoginController extends Controller
{
    //protected $guard = 'admin';

    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function showLoginForm(){
        $settings = GlobalSettings::where('type','admin_otp');
        if($settings->doesntExist())
        {
            $settings = new GlobalSettings();
            $settings->setting_value = 1;
            $settings->type = "admin_otp";
            $settings->save();
        }
        else{
            $settings = $settings->first();
        }
        return view('admin.login')->with(['setting'=>$settings]);
    }
    public function login(Request $request){
        //validate the form
//        $errors = new MessageBag;
        $this->validate($request, [
            'phone_number' =>'required',
            'pin' => 'required|min:4'
        ]);

        //Attempt to login
        if(Auth::guard('admin')->attempt(['phone_number' => $request->phone_number , 'password'=>$request->pin], $request->remember) || Auth::guard('admin')->attempt(['official_phone_number' => $request->phone_number , 'password'=>$request->pin], $request->remember)){
            //if Successfull then redirect to intended location

            $admin = Auth::guard('admin');

            if ($admin->user()->status == 0) {
                auth('admin')->logout();
                return back()->with('info', 'Your Account is Disabled, Contact Admin');
            }

            $id = $admin->id();
            $role_id = $admin->user()->role_id;
            $first_login = $admin->user()->first_login;

            $hubs = AdminHub::where('admin_id', $id)->pluck('hub_id')->toArray();
            $shippers = SalePersonTag::where('admin_id', $id)->where('status', 0)->pluck('user_id')->toArray();
            $assigned_admins = MultipleSaleLead::leftjoin('multiple_sale_taggings as mst', 'mst.lead_id', '=', 'multiple_sale_leads.id')
                ->leftjoin('sale_person_tags as spt', 'spt.admin_id', '=', 'mst.admin_id')
                ->where('multiple_sale_leads.admin_id', $id)
                ->where('spt.status', 0)
                ->whereNotNull('spt.user_id')->select('spt.user_id');
            if($assigned_admins->exists()) {
                $assigned_admins = $assigned_admins->pluck('spt.user_id')->toArray();
                $shippers = array_merge($shippers, $assigned_admins);
            }
            $KAE = SaleTierTag::where('kam',$id);
            if($KAE->exists()){
                $KAE = $KAE->pluck('user_id')->toArray();
                $shippers = array_merge($shippers,$KAE);
                //$shippers = array_unique($shippers);
            }

            $permissions = AdminRoleModulePermission::where('role_id', $role_id)->pluck('permission_id')->toArray();
            $department = AdminRole::find($role_id)->department_id;
            $sales_coordinator = SalesCommissionUser::where('user_id',$id)->whereIn('sales_commission_users.tier_id',[2,3])->exists();
            if(in_array($role_id, [44])){
                if(count($hubs) > 0){
                    $hub_cities = City::whereIn('hub_id', $hubs)->where('status', 1)->pluck('id')->toArray();
                    $region_shippers = User::whereNotIn('id', $shippers)->whereIn('city_id', $hub_cities)->pluck('id')->toArray();
                    if(count($region_shippers) > 0){
                        $shippers = array_merge($shippers,$region_shippers);
                    }
                }
            }
//mark login start
            $check_login = AgentReturnConfirmation::where('admin_id',$id)->where('current_date',Carbon::now()->format("Y-m-d"));
            if(!$check_login->exists()){
               $agent_role = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
                ->where('admin_roles.department_id',3)->where('a.id',$id);
                if($agent_role->exists()){
                    $agent_login = new AgentReturnConfirmation;
                    $agent_login->login_time = Carbon::now();
                    $agent_login->admin_id = $id;
                    $agent_login->current_date = Carbon::now()->format("Y-m-d");
                    $agent_login->save();
                }
            }else{
                $check_login = $check_login->get()->first();
                if($check_login->login_time == NULL){
                    $check_login->login_time = Carbon::now();
                    $check_login->save();
                }
            }
//mark login end
            $sale_users_bypass = array();
            $settings = GlobalSettings::where('type', 'sales_user_restriction_bypass');

            if ($settings->exists()) {
                $settings = $settings->first();
                $sale_users_bypass = array_map('intval', explode(',', $settings->text));
            }

            session(['role_id' => $role_id, 'hubs' => $hubs, 'permissions' => $permissions, 'department_id' => $department, 'tagged_shippers' => $shippers,'sales_coordinator' => $sales_coordinator,'first_login' => $first_login, 'id' => $id, 'sale_users_bypass' => $sale_users_bypass]);

            return redirect()->intended(route('admin.dashboard.index'));
        }
        $errors = [$this->username() => trans('auth.failed')];
//        $errors = new MessageBag(['password' => ['Email and/or password invalid.']]);
        return redirect()->back()->withInput($request->only('email','remember'))->withErrors($errors);

    }

    public function username()
    {
        return 'phone_number';
    }


    public function logout(Request $request)
    {
        if(Auth::guard('admin')){
            //mark logout start
            $admin = Auth::guard('admin');
            $check_logout = AgentReturnConfirmation::where('admin_id',$admin->id())->where('current_date',Carbon::now()->format("Y-m-d"));
            if($check_logout->exists()){
                $agent_role = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
                ->where('admin_roles.department_id',3)->where('a.id',$admin->id());
                if($agent_role->exists()){
                    $agent_logout = $check_logout->first();
                    $agent_logout->logout_time = Carbon::now();
                    $agent_logout->save();
                }
            }
            //mark logout end
            Auth::guard('admin')->logout();

            $request->session()->invalidate();

            return redirect()->route('admin.login');
        }
        return redirect()->route('admin.login');

    }
    public function credentials(Request $request){
        $admin = Admin::where('phone_number', $request->phone_number)->orWhere('official_phone_number',$request->phone_number);
        if ($admin->exists()) {
            $admin = $admin->first();
        } else {
            return response()->json(['status' => 0, 'error' => 'Invalid Credentials']);
        }
        if (Hash::check($request->input('pin'), $admin->password)) {
            if($admin->status){
                $environment = config('app.env');

                if ($environment == 'production' || $environment == 'staging') {
                    $otp = mt_rand(100000, 999999);
                    $admin->otp = $otp;
                    $admin->last_login_attempt = Carbon::now();
                    $admin->save();
                    $data = array("otp"=>$otp,"phone_number"=>$request->phone_number);
                    NotificationsController::send(138, $admin, $data);
                }

                return response()->json(['status' => 1]);
            }else{
                return response()->json(['status' => 0, 'error' => 'Your Account is Disabled, Contact Admin']);
            }
        } else {
            return response()->json(['status' => 0, 'error' => 'Invalid Credentials']);
        }
    }

    public function verify_otp(Request $request){
        $environment = config('app.env');
        if($environment == 'production' || $environment == 'staging') {
            $admin = Admin::where('phone_number', $request->phone_number)->orWhere('official_phone_number',$request->phone_number);
            if ($admin->exists()) {
                $admin = $admin->first();
                if ($admin->otp == $request->otp) {
                    return response()->json(['status' => 1]);
                } else {
                    return response()->json(['status' => 0, 'error' => 'Invalid OTP']);
                }
            } else {
                return response()->json(['status' => 0, 'error' => 'Invalid Credentials']);
            }
        }
        else{
            return response()->json(['status' => 1]);
        }
    }
}
