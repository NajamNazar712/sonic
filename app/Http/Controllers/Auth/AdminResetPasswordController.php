<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\HR\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

use Password;
use Auth;
class AdminResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/admin/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin');
    }

    public function reset_pin(Request $request)
    {
        $admin = Admin::where('phone_number', $request->phone_number)->orWhere('official_phone_number',$request->phone_number);
        if ($admin->exists()) {
            $admin = $admin->first();
            $environment = config('app.env');
            if($environment == 'production' || $environment == 'staging') {
                if ($admin->reset_pin_otp == $request->otp) {
                    $admin->dummy_pin = $request->pin;
                    $admin->password = bcrypt($request->pin);
                    $admin->reset_pin_otp = null;
                    $admin->reset_pin_status = 1;
                    $admin->save();

                    $employee = Employee::where('trax_id',$admin->trax_id)->where('trax_id','!=',null);
                    if($employee->exists())
                    {
                        $employee = $employee->first();
                        $employee->pin = $request->pin;
                        $employee->update();
                    }
                    event(new PasswordReset($admin));
                    NotificationsController::send(159, $admin->id);
                    return redirect()->route('admin.login')->with('success', 'Pin Reset Successfully');
                } else {
                    return back()->with(['error' => 'Invalid OTP']);
                }
            }
            else{
                $admin->dummy_pin = $request->pin;
                $admin->password = bcrypt($request->pin);
                $admin->save();
                $employee = Employee::where('trax_id',$admin->trax_id)->where('trax_id','!=',null);
                if($employee->exists())
                {
                    $employee = $employee->first();
                    $employee->pin = $request->pin;
                    $employee->update();
                }
                event(new PasswordReset($admin));
                NotificationsController::send(159, $admin->id);
                return redirect()->route('admin.login')->with('success','Pin Reset Successfully');
            }
        } else {
            return back()->with(['error' => 'Invalid Credentials']);
        }
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('admin.auth.passwords.reset')->with(['token' => $token, 'email' => $request->email]);
    }
    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function broker(){
        return Password::broker('admins');
    }
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);

        $user->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));

        NotificationsController::send(159, $user->id);
        //return redirect(route('cod.login'))->with('success','Your password has reset!');
        //$this->guard()->login($user);
    }
    protected function sendResetResponse($response)
    {
        return redirect($this->redirectPath())
            ->with('success', trans($response));
    }
}
