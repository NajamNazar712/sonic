<?php

namespace App\Http\Controllers\Auth;

use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteImage;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\Commission\SalesCommissionUser;
use App\Http\Models\MultipleSaleLead;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;

use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\AdminRoleModulePermission;

class AdminLoginController extends Controller
{
    //protected $guard = 'admin';

    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function showLoginForm(){
        return view('admin.login');
    }
    public function login(Request $request){
        //validate the form
//        $errors = new MessageBag;
        $this->validate($request, [
            'email' =>'required|email',
            'password' => 'required|min:6'
        ]);
        //Attempt to login
        if(Auth::guard('admin')->attempt(['email' => $request->email , 'password'=>$request->password], $request->remember)){
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
            $permissions = AdminRoleModulePermission::where('role_id', $role_id)->pluck('permission_id')->toArray();
            $department = AdminRole::find($role_id)->department_id;
            $sales_coordinator = SalesCommissionUser::where('user_id',$id)->whereIn('sales_commission_users.tier_id',[2,3])->exists();
            session(['role_id' => $role_id, 'hubs' => $hubs, 'permissions' => $permissions, 'department_id' => $department, 'tagged_shippers' => $shippers,'sales_coordinator' => $sales_coordinator,'first_login' => $first_login]);

            return redirect()->intended(route('admin.dashboard.index'));
        }
        $errors = [$this->username() => trans('auth.failed')];
//        $errors = new MessageBag(['password' => ['Email and/or password invalid.']]);
        return redirect()->back()->withInput($request->only('email','remember'))->withErrors($errors);

    }

    public function username()
    {
        return 'email';
    }


    public function logout(Request $request)
    {
        if(Auth::guard('admin')){
            Auth::guard('admin')->logout();

            $request->session()->invalidate();

            return redirect()->route('admin.login');
        }
        return redirect()->route('admin.login');

    }
}
