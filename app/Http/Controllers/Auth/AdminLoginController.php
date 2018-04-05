<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
        $this->validate($request, [
            'email' =>'required|email',
            'password' => 'required|min:6'
        ]);
        //Attempt to login
        if(Auth::guard('admin')->attempt(['email' => $request->email , 'password'=>$request->password], $request->remember)){
            //if Successfull then redirect to intended location
            return redirect()->intended(route('admin.dashboard'));
        }
        return redirect()->back()->withInput($request->only('email','remember'));

    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        return redirect('admin/login');
    }
}
