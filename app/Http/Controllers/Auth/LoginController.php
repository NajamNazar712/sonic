<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
        }elseif (!$user->active) {
            auth()->logout();
            return back()->with('info', 'Your Account is Not Activated Yet, Contact Admin');
        }
        return redirect()->intended($this->redirectPath());

    }
}
