<?php

namespace App\Http\Controllers\Auth;

use App\EmployeeAdditionalDay;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\GlobalSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\EmployeeShift;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\HR\Employee;
use Illuminate\Support\Facades\Hash;

class AgentLoginController extends Controller
{


    public function __construct()
    {
        $this->middleware('guest:agent')->except('logout');
    }


    // Heading: Virtual RCP Agent Screen
    // Sidebar: N/A
    // URL: agent/dashboard
    // Description: this method is used for login index And For Otp

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


    // Heading: N/A
    // Sidebar: N/A
    // URL: agent/login
    // Description: this method is used for login whose staff_category_id is 3 And Check Login Days Login.
    public function login(Request $request)
    {
        $this->validate($request, [
            'phone_number' => 'required',
            'pin' => 'required|min:4'
        ]);
    
        $admin = Admin::where('phone_number', $request->phone_number)->first();
    
        if ($admin) {
            $employee = Employee::where('trax_id', $admin->trax_id)
                ->where('staff_category_id', 3)
                ->where('status_id', '!=', 2)
                ->first();
    
            if ($employee) {
                $today = Carbon::today();
                if ($today->dayOfWeek === Carbon::SUNDAY) {
                    $current_time = Carbon::now();
                    $is_sunday_exist = EmployeeAdditionalDay::where('working_days', $today->format('Y-m-d'))->where('employee_id', $employee->id)->exists();
    
                    if ($is_sunday_exist) {
                        $min_start_time = EmployeeShift::where('shift_type_id', 2)->orderBy('start_time', 'asc')->first();
                        $min_start_time = Carbon::parse($min_start_time->start_time);
    
                        $max_end_time = EmployeeShift::where('shift_type_id', 2)->orderBy('end_time', 'desc')->first();
                        $max_end_time = Carbon::parse($max_end_time->end_time);
    
                        if ($current_time->between($min_start_time, $max_end_time)) {
                            if (Auth::guard('agent')->attempt(['phone_number' => $request->phone_number, 'password' => $request->pin], $request->remember) || Auth::guard('agent')->attempt(['official_phone_number' => $request->phone_number, 'password' => $request->pin], $request->remember)) {
                                return redirect()->intended(route('agent.dashboard.index'));
                            }
                            $errors = [$this->username() => trans('auth.failed')];
                            return redirect()->back()->withErrors($errors);
                        } else {
                            $errors = 'Your Time Slot Does Not Match';
                            return redirect()->back()->withErrors($errors);
                        }
                    } else {
                        $errors = 'You are not allowed to login on Sunday';
                        return redirect()->back()->withErrors($errors);
                    }
                } else {
                    if (isset($employee->shift_id)) {
                        $current_time = Carbon::now();
                        $shift_exist = EmployeeShift::where('id', $employee->shift_id)->where('shift_type_id',2)->first();
                        if ($shift_exist) {
                            // $shift_exist =  EmployeeShift::where('id', $employee->shift_id)->first();
                            $start_time = Carbon::parse($shift_exist->start_time);
                            $end_time = Carbon::parse($shift_exist->end_time);

                            if($current_time->between($start_time, $end_time)){
                                if (Auth::guard('agent')->attempt(['phone_number' => $request->phone_number, 'password' => $request->pin], $request->remember) || Auth::guard('agent')->attempt(['official_phone_number' => $request->phone_number, 'password' => $request->pin], $request->remember)) {
                                    return redirect()->intended(route('agent.dashboard.index'));
                                }
                                $errors = [$this->username() => trans('auth.failed')];
                                return redirect()->back()->withErrors($errors);
                            }else{
                                $errors = 'You are not allowed in this Time Slot';
                                return redirect()->back()->withErrors($errors);
                            }

                        } else {
                            $errors = 'Employee Shift Doesnt Exist';
                            return redirect()->back()->withErrors($errors);                        
                        }
                    } else {
                        $errors = 'Shift Doesnt Exist';
                        return redirect()->back()->withErrors($errors);                          
                    }
                }
            } else {
                $errors = 'You Have To Be Contractual';
                return redirect()->back()->withErrors($errors);
            }
        } else {
            $errors = 'Employee Doesn\'t Exist';
            return redirect()->back()->withErrors($errors);
        }
    }
    
    // Heading: N/A
    // Sidebar: N/A
    // URL: N/A
    // Description: this method is used for error return phone number
    public function username()
    {
        return 'phone_number';
    }


    // Heading: N/A
    // Sidebar: N/A
    // URL: agent/logout
    // Description: this method is used for logout Agent

    public function logout(Request $request)
    {
        if (Auth::guard('agent')) {

            Auth::guard('agent')->logout();

            $request->session()->invalidate();

            return redirect()->route('agent.login');
        }
        return redirect()->route('agent.login');
    }



    // Heading: N/A
    // Sidebar: N/A
    // URL: agent/credentials
    // Description: this method is used for Send OTP via Notification Controller
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


    // Heading: N/A
    // Sidebar: N/A
    // URL: agent/verify_otp
    // Description: this method is used for Verify OTP
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
