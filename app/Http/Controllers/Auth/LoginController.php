<?php

namespace App\Http\Controllers\Auth;
use App\Http\Models\PackagingCharge;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'cod/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('client.auth.login');
    }
    protected function authenticated(Request $request, $user)
    {
        if ($user->blacklist) {
            auth()->logout();
            return back()->with('info', 'Your Account is Blacklisted, Contact Admin');
        }elseif ($user->status != 3) {
            auth()->logout();
            return back()->with('info', 'Your Account is Not Activated Yet, Contact Admin');
        }
        $packaging_charges_check = false;
        if(PackagingCharge::where(['user_id'=>Auth::id(),'shipping_mode_id'=>1])->exists()){
            $packaging_charges_check = true;
        }else if(PackagingCharge::where(['user_id'=>Auth::id(),'shipping_mode_id'=>2])->exists()){
            $packaging_charges_check = true;
        }else if(PackagingCharge::where(['user_id'=>Auth::id(),'shipping_mode_id'=>3])->exists()){
            $packaging_charges_check = true;
        }else if(PackagingCharge::where(['user_id'=>Auth::id(),'shipping_mode_id'=>4])->exists()){
            $packaging_charges_check = true;
        }
        session(['packaging_charges_check' => $packaging_charges_check]);
        return redirect()->intended($this->redirectPath());

    }
}
