<?php

namespace App\Http\Controllers\Auth;

use App\Http\Models\Admin\AdminRole;
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

            $hubs = AdminHub::where('admin_id', $id)->pluck('hub_id')->toArray();
            $permissions = AdminRoleModulePermission::where('role_id', $role_id)->pluck('permission_id')->toArray();
            $department = AdminRole::find($role_id)->department_id;
            session(['role_id' => $role_id, 'hubs' => $hubs, 'permissions' => $permissions, 'department_id' => $department]);

            return redirect()->intended(route('admin.dashboard'));
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
