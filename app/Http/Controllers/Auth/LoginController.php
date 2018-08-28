<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

use App\Http\Models\PackagingCharge;
use App\Http\Models\Shipper\SubstituteUserPermission;

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

    protected function attemptLogin(Request $request)
    {
        $attempt = Auth::guard('web')->attempt($this->credentials($request), $request->filled('remember'));

        if ($attempt) {
            session(['user_type' => 1]);
        }
        else {
            $attempt = Auth::guard('substitute_users')->attempt($this->credentials($request), $request->filled('remember'));

            if ($attempt) {
                session(['user_type' => 2]);
            }
        }

        return $attempt;
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if (session('user_type') == 1) {
            $guard = Auth::guard('web');
        }
        else {
            $guard = Auth::guard('substitute_users');
        }

        $this->authenticated($request, $guard->user());
    }

    protected function authenticated(Request $request, $user)
    {
        $packaging_charges_check = FALSE;

        if (session('user_type') == 1) {
            if ($user->blacklist) {
                auth('web')->logout();
                return back()->with('info', 'Your Account is Blacklisted, Contact Admin');
            }
            else if ($user->status != 3) {
                auth('web')->logout();
                return back()->with('info', 'Your Account is Not Activated Yet, Contact Admin');
            }
            else {
                session(['user_id' => $user->id]);

                if (PackagingCharge::where('user_id', $user->id)->exists()) {
                    $packaging_charges_check = TRUE;
                }
            }
        }
        else {
            if (!$user->status) {
                auth('substitute_users')->logout();
                return back()->with('info', 'Your Account is Disabled');
            }
            else {
                $permissions = SubstituteUserPermission::where('substitute_user_id', $user->id)->pluck('permission_id')->toArray();

                session(['permissions' => $permissions]);
                session(['user_id' => $user->user_id]);

                if (PackagingCharge::where('user_id', $user->user_id)->exists()) {
                    $packaging_charges_check = TRUE;
                }
            }
        }

        session(['packaging_charges_check' => $packaging_charges_check]);

        return redirect()->route('cod.dashboard');
    }

    public function logout(Request $request)
    {
        if (session('user_type') == 1) {
            $guard = Auth::guard('web');
        }
        else {
            $guard = Auth::guard('substitute_users');
        }

        $guard->logout();

        $request->session()->invalidate();

        return redirect()->route('cod.login');
    }
}
