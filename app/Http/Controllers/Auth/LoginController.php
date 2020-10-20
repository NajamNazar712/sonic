<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Admins\GlobalSettingsController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\ShipmentPrebook;
use App\Http\Models\Shipper\SubstituteUser;
use App\Http\Models\Sister_account\MergedSisterAccountMapping;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\SubstituteUserPermission;
use App\Http\Models\PackagingCharge;
use App\Http\Models\Shipper\ShipperAirWaybillSettings;

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

        return $this->authenticated($request, $guard->user());
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
                $sister_users = MergedSisterAccountMapping::where('head_user_id', $user->id)->pluck('sister_user_id')->toArray();
                session(['sister_users' => $sister_users]);
                session(['user_id' => $user->id]);
                if (SalePersonTag::where('user_id', session('user_id'))->where('status', 0)->exists()){
                    session(['sale_person_status' => 1]);
                }
                else{
                    session(['sale_person_status' => 0]);
                }
                session(['account_type' => $user->account_type_id]);
                if (PackagingCharge::where('user_id', $user->id)->exists()) {
                    $packaging_charges_check = TRUE;
                }

                $air_waybill_settings = ShipperAirWaybillSettings::where('user_id', $user->id);

                if ($air_waybill_settings->exists()) {
                    $air_waybill_settings = $air_waybill_settings->first();

                    session(['air_waybill_type' => $air_waybill_settings->type]);
                }
                else {
                    session(['air_waybill_type' => 1]);
                }

                $pickup_wise_account = GlobalSettings::where('type', 'pickup_wise_payment_accounts');
                if($pickup_wise_account->exists()){
                    $pickup_wise_accounts_array = array();
                    $pickup_wise_account = $pickup_wise_account->first();
                    $pickup_wise_accounts_array = array_map('intval', explode(',', $pickup_wise_account->text));
                    if(in_array($user->id, $pickup_wise_accounts_array)){
                        session(['pickup_wise_account' => 1]);
                    }
                }
            }
        }
        else {
            $shipper = User::find($user->user_id);

            if ($shipper->blacklist) {
                auth('substitute_users')->logout();
                return back()->with('info', 'Your Shipper\'s Account is Blacklisted, Contact Admin');
            }
            else if ($shipper->status != 3) {
                auth('substitute_users')->logout();
                return back()->with('info', 'Your Shipper\'s Account is Not Activated Yet, Contact Admin');
            }
            else if (!$user->status) {
                auth('substitute_users')->logout();
                return back()->with('info', 'Your Account is Disabled');
            }
            else {
                $substitute_user = SubstituteUser::find($user->id);
                $permissions = SubstituteUserPermission::where('substitute_user_id', $user->id)->pluck('permission_id')->toArray();
                $sister_users = MergedSisterAccountMapping::where('head_user_id', $user->id)->pluck('sister_user_id')->toArray();
                session(['sister_users' => $sister_users]);
                session(['permissions' => $permissions]);
                session(['user_id' => $user->user_id]);
                session(['account_type' => $shipper->account_type_id]);
                session(['restriction' => $substitute_user->restriction]);

                if (PackagingCharge::where('user_id', $user->user_id)->exists()) {
                    $packaging_charges_check = TRUE;
                }

                $air_waybill_settings = ShipperAirWaybillSettings::where('user_id', $user->user_id);

                if ($air_waybill_settings->exists()) {
                    $air_waybill_settings = $air_waybill_settings->first();

                    session(['air_waybill_type' => $air_waybill_settings->type]);
                }
                else {
                    session(['air_waybill_type' => 1]);
                }
            }
        }
        $shipment_pre_book = ShipmentPrebook::where('user_id', $user->id);
        if($shipment_pre_book->exists()){
            $shipment_pre_book = $shipment_pre_book->first();
            session(['prefix' => $shipment_pre_book->prefix]);
        }
        session(['packaging_charges_check' => $packaging_charges_check]);
        return redirect()->route('cod.welcome');
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
    public function not_found(){
        return view('errors.404');
    }
}
