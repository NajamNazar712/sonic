<?php

namespace App\Http\Controllers\Auth;

use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\Shipper\UserBankInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Models\Product;
use App\Http\Models\CityInfo;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/cod/register/success';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        $products = Product::all();
        $cities = CityInfo::all();
//        return $cities;
        return view('client.auth.register')->with(['products'=>$products,'cities'=>$cities]);
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        return Validator::make($data, [
            'company_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'shipper_poc'=>'required|string|max:255',
            'company_address'=>'required|string|max:255',
            'shipper_phone'=>'required|string|max:255',
//            'shipper_phone2'=>'string|max:255',
            'cnic'=>'required|string|max:255',
//            'ntn_no'=>'string|max:255',
//            'url'=>'string|max:255',
            'shipper_city'=>'required|string|max:255',
            'shipping_city'=>'required|string|max:255',
            'bank_city'=>'required|string|max:255',
            'pickup_address'=>'required|string|max:255',
            'shipping_poc'=>'required|string|max:255',
            'shipping_phone'=>'required|string|max:255',
            'shipping_email'=>'required|string|max:255',
            'product_type'=>'required|string|max:255',
            'bank_name'=>'required|string|max:255',
            'bank_branch'=>'required|string|max:255',
            'account_no'=>'required|string|max:255',
            'account_title'=>'required|string|max:255',
            'iban_no'=>'required|string|max:255',
            'mode_of_payment'=>'required|string|max:255',
            'cycle_of_payment'=>'required|string|max:255',

        ]);
    }

    public function register(Request $request)
    {
//        $products = implode(',',$request->product_type);
//
//        return $request;
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        //$this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $newUser = User::create([
            'name' => $data['company_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'address' => $data['company_address'],
            'poc' => $data['shipper_poc'],
            'phone'=>$data['shipper_phone'],
            'phone2'=>$data['shipper_phone2'],
            'cnic' => $data['cnic'],
            'ntn_no' => $data['ntn_no'],
            'url' => $data['url'],
            'city_code'=>$data['shipper_city'],
        ]);
        $shipper = User::find($newUser->id);
        $shipper->products()->attach($data['product_type']);
        UserShippingInfo::create([
                'user_id'=>$newUser->id,
                'pickup_address'=>$data['pickup_address'],
                'poc'=>$data['shipping_poc'],
                'phone'=>$data['shipping_phone'],
                'email'=>$data['shipping_email'],
                'city_code'=>$data['shipping_city'],
        ]);
        UserBankInfo::create([
                'user_id'=>$newUser->id,
                'bank_name'=>$data['bank_name'],
                'bank_branch'=>$data['bank_branch'],
                'account_no'=>$data['account_no'],
                'account_title'=>$data['account_title'],
                'iban'=>$data['iban_no'],
                'payment_mode'=>$data['mode_of_payment'],
                'payment_cycle'=>$data['cycle_of_payment'],
                'city_code'=>$data['bank_city'],
        ]);

        return $newUser;
    }
    public function register_success(){
        return view('client.register_success');
    }
}
