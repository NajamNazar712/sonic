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
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\HR\Employee;
use Illuminate\Support\Facades\Hash;

class AgentLoginController extends Controller
{
    //protected $guard = 'admin';

    public function __construct()
    {
        $this->middleware('guest:agent')->except('logout');
    }

    public function showLoginForm()
    {
        $settings = GlobalSettings::where('type', 'admin_otp');
        if ($settings->doesntExist()) {
            $settings = new GlobalSettings();
            $settings->setting_value = 1;
            $settings->type = "admin_otp";
            $settings->save();
        } else {
            $settings = $settings->first();
        }
        return view('agent.login')->with(['setting' => $settings]);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'phone_number' => 'required',
            'pin' => 'required|min:4'
        ]);

        $employee = Admin::where('phone_number', $request->phone_number)->first();
        if (gettype($employee) != 'NULL') {

            $employee_id = Employee::where('trax_id', $employee->trax_id)->first();
            
            if (gettype($employee_id) != 'NULL') {
                if ($employee_id->staff_category_id === 3) {

                    if (Auth::guard('agent')->attempt(['phone_number' => $request->phone_number, 'password' => $request->pin], $request->remember) || Auth::guard('agent')->attempt(['official_phone_number' => $request->phone_number, 'password' => $request->pin], $request->remember)) {
                        return redirect()->intended(route('agent.dashboard.index'));
                    }
                    $errors = [$this->username() => trans('auth.failed')];

                    return redirect()->back()->withErrors($errors);
                } else {
                    $errors = 'You Have To Be Contractual';

                    return redirect()->back()->withErrors($errors);
                }
            } else {

                $errors = 'Employee Doesnt Exists';

                return redirect()->back()->withErrors($errors);
            }

        }else{

            $errors = 'Employee Doesnt Exist';

            return redirect()->back()->withErrors($errors);
        }
    }

    public function username()
    {
        return 'phone_number';
    }

    public function logout(Request $request)
    {
        if (Auth::guard('agent')) {
            //mark logout start
            $admin = Auth::guard('agent');
            $check_logout = AgentReturnConfirmation::where('admin_id', $admin->id())->where('current_date', Carbon::now()->format("Y-m-d"));
            if ($check_logout->exists()) {
                $agent_role = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
                    ->where('admin_roles.department_id', 3)->where('a.id', $admin->id());
                if ($agent_role->exists()) {
                    $agent_logout = $check_logout->first();
                    $agent_logout->logout_time = Carbon::now();
                    $agent_logout->save();
                }
            }
            //mark logout end
            Auth::guard('agent')->logout();

            $request->session()->invalidate();

            return redirect()->route('agent.login');
        }
        return redirect()->route('agent.login');
    }
    
    public function credentials(Request $request)
    {
        $admin = Admin::where('phone_number', $request->phone_number)->orWhere('official_phone_number', $request->phone_number);
        if ($admin->exists()) {
            $admin = $admin->first();
        } else {
            return response()->json(['status' => 0, 'error' => 'Invalid Credentials']);
        }
        if (Hash::check($request->input('pin'), $admin->password)) {
            if ($admin->status) {
                $environment = config('app.env');

                if ($environment == 'production' || $environment == 'staging') {
                    $otp = mt_rand(100000, 999999);
                    $admin->otp = $otp;
                    $admin->last_login_attempt = Carbon::now();
                    $admin->save();
                    $data = array("otp" => $otp, "phone_number" => $request->phone_number);
                    NotificationsController::send(138, $admin, $data);
                }

                return response()->json(['status' => 1]);
            } else {
                return response()->json(['status' => 0, 'error' => 'Your Account is Disabled, Contact Admin']);
            }
        } else {
            return response()->json(['status' => 0, 'error' => 'Invalid Credentials']);
        }
    }

    public function verify_otp(Request $request)
    {
        $environment = config('app.env');
        if ($environment == 'production' || $environment == 'staging') {
            $admin = Admin::where('phone_number', $request->phone_number)->orWhere('official_phone_number', $request->phone_number);
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
        } else {
            return response()->json(['status' => 1]);
        }
    }
}
