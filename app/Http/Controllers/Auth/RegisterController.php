<?php

namespace App\Http\Controllers\Auth;

use App\Http\Models\AccountType;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\AverageShipmentCycle;
use App\Http\Models\BanksList;
use App\Http\Models\City;
use App\Http\Models\CRFTermsConditions;
use App\Http\Models\InvoicingCycle;
use App\Http\Models\Reference;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\Shipper\UserBankInfo;
use App\http\Models\UserDocumentAttachment;
use App\Mail\Notifications;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Models\Product;
use App\Http\Models\PickupType;
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
        $invoicing_cycle = InvoicingCycle::all();
        $account_type = AccountType::all();
        $products = Product::all();
        $banks = BanksList::all();
        $city_list = City::where('status',1)->get();
        $pickup_city_list = City::where('pickup',1)->where('status',1)->get();
        $references = Reference::all();
        $average_shipment_durations = AverageShipmentCycle::all();
        $sales_persons = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('admins.status', 1)->where('ar.department_id', 7)->get();
        // This needs to be modified to reflect the new Logic of Admin able to Select which City has Pickup enabled, which Booking Type is enabled and accordingly which Shipping Mode is enabled. PickupType is no longer valid.
        // $cities = PickupType::find(1)->cities()->orderBy('city_name')->get();

        return view('client.auth.register')->with(['products'=>$products,'cities'=>$city_list,'pickup_city_list'=>$pickup_city_list,'all_cities'=>$city_list,'banks'=>$banks,'account_types' => $account_type, 'invoicing_cycle' => $invoicing_cycle, 'references' => $references, 'sales_persons' => $sales_persons, 'average_shipment_durations' => $average_shipment_durations]);
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        if($data['nature_of_account'] == 1){
        	return Validator::make($data, [
                'name' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'shipper_poc'=>'required|regex:/^[a-zA-Z]+$/u|max:255',
                'company_address'=>'required|string|max:255',
                'shipper_phone'=>'required|string|max:255',
                'nature_of_account' => 'required',
                'average_shipment' => 'required',
                'average_shipment_duration' => 'required',
                'cnic'=>'required|string|max:255',
				'url'=>'required|string|max:255',
                'shipper_city'=>'required|int',
                'shipper_product_type'=>'required|int',
                'product_name' => 'required_if:shipper_product_type, ==, 24',
                'shipping_city.*'=>'required|string|max:255',
                'pickup_address.*'=>'required|string|max:255',
                'shipping_poc.*'=>'required|string|max:255',
                'shipping_phone.*'=>'required|string|max:255',
                'shipping_email.*'=>'required|string|max:255',
                'product_type.*'=>'required|max:255',
                'bank_city.*'=>'required|string|max:255',
                'bank_name.*'=>'required|max:255',
                'bank_branch.*'=>'required|string|max:255',
                'account_no.*'=>'required|string|max:255',
                'account_title.*'=>'required|string|max:255',
                'iban_no.*'=>'required|string|max:255',
                'cycle_of_payment'=>'required|string|max:255',
                'filled_and_signed_pdf' => 'mimes:pdf',
                'signed_acknowledgement_pdf' => 'mimes:pdf',
                'cnic_front_image' => 'mimes:png,jpeg,jpg',
                'cnic_back_image' => 'mimes:png,jpeg,jpg',
                'blank_cheque_image' => 'mimes:png,jpeg,jpg',
                'g-recaptcha-response' => 'required|captcha'
            ]);
        }else{
            return Validator::make($data, [
                'name' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'shipper_poc'=>'required|regex:/^[a-zA-Z]+$/u|max:255',
                'company_address'=>'required|string|max:255',
                'shipper_phone'=>'required|string|max:255',
                'nature_of_account' => 'required',
                'average_shipment' => 'required',
                'average_shipment_duration' => 'required',
                'cnic'=>'required|string|max:255',
				'url'=>'required|string|max:255',
                'shipper_city'=>'required|string|max:255',
                'shipper_product_type'=>'required|int',
                'product_name' => 'required_if:shipper_product_type, ==, 24',
                'shipping_city.*'=>'required|string|max:255',
                'pickup_address.*'=>'required|string|max:255',
                'shipping_poc.*'=>'required|string|max:255',
                'shipping_phone.*'=>'required|string|max:255',
                'shipping_email.*'=>'required|string|max:255',
                'product_type.*'=>'required|max:255',
                'bank_city'=>'required|string|max:255',
                'bank_name'=>'required|max:255',
                'bank_branch'=>'required|string|max:255',
                'account_no'=>'required|string|max:255',
                'account_title'=>'required|string|max:255',
                'iban_no'=>'required|string|max:255',
                'cycle_of_payment'=>'required|string|max:255',
                'cycle_of_invoicing' => 'required',
//                'generation_date' => 'required_if:cycle_of_invoicing,==,1|required_if:cycle_of_invoicing,==,3|numeric',
                'billing_person_name' => 'required|string|max:255',
                'billing_person_phone' => 'required|string|max:255',
                'billing_person_email' => 'required|string|email|max:255',
                'billing_address' => 'required|string|max:255',
                'filled_and_signed_pdf' => 'mimes:pdf',
                'signed_acknowledgement_pdf' => 'mimes:pdf',
                'cnic_front_image' => 'mimes:png,jpeg,jpg',
                'cnic_back_image' => 'mimes:png,jpeg,jpg',
                'blank_cheque_image' => 'mimes:png,jpeg,jpg',
                'g-recaptcha-response' => 'required|captcha'
            ]);
        }

    }

    public function register(Request $request)
    {

        // return var_dump($request);exit();
       // dd($request);
//        $products = implode(',',$request->product_type);
//
//        return $request;
        
        $this->validator($request->all())->validate();
        event(new Registered($user = $this->create($request->all())));

        //$this->guard()->login($user);
        
        $user_attachment = new UserDocumentAttachment();
        $user_attachment->user_id = $user->id;
        if ($request->hasFile('filled_and_signed_pdf')) {
            $filename = 'filled_and_signed_pdf_' . $user->id . '.pdf';
            $file = $request->file('filled_and_signed_pdf');
            Storage::disk('public')->putFileAs('users_attached_documents/'. $user->id .'', $file, $filename);
            $user_attachment->filled_and_signed_pdf = $filename;
        }
        
        if ($request->hasFile('signed_acknowledgement_pdf')) {
            $filename = 'signed_acknowledgement_pdf_' . $user->id . '.pdf';
            $file = $request->file('signed_acknowledgement_pdf');
            Storage::disk('public')->putFileAs('users_attached_documents/'. $user->id .'', $file, $filename);
            $user_attachment->signed_acknowledgement_pdf = $filename;
        }
        
        if ($request->hasFile('cnic_front_image')) {
            $filename = 'cnic_front_image_' . $user->id . '.png';
            $file = $request->file('cnic_front_image');
            Storage::disk('public')->putFileAs('users_attached_documents/'. $user->id .'', $file, $filename);
            $user_attachment->cnic_front_image = $filename;
        }
        
        if ($request->hasFile('cnic_back_image')) {
            $filename = 'cnic_back_image_' . $user->id . '.png';
            $file = $request->file('cnic_back_image');
            Storage::disk('public')->putFileAs('users_attached_documents/'. $user->id .'', $file, $filename);
            $user_attachment->cnic_back_image = $filename;
        }
        
        if ($request->hasFile('blank_cheque_image')) {
            $filename = 'blank_cheque_image_' . $user->id . '.png';
            $file = $request->file('blank_cheque_image');
            Storage::disk('public')->putFileAs('users_attached_documents/'. $user->id .'', $file, $filename);
            $user_attachment->blank_cheque_image = $filename;
        }
        
        $user_attachment->save();

        
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
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'address' => $data['company_address'],
            'poc' => $data['shipper_poc'],
            'phone'=>$data['shipper_phone'],
            'phone2'=>$data['shipper_phone2'],
            'cnic' => $data['cnic'],
            'ntn_no' => $data['ntn_no'],
            'strn_no' => $data['strn_no'],
            'url' => $data['url'],
            'city_id'=>$data['shipper_city'],
            'product_id'=>$data['shipper_product_type'],
            'other_product_name'=> (array_key_exists('product_name', $data))? $data['product_name']:null,
            'account_type_id' => $data['nature_of_account'],
            'average_shipments' => $data['average_shipment'],
            'average_shipment_duration_id' => $data['average_shipment_duration'],
            'reference_id' => $data['reference'],
            'email_verified' => 0,
            'api_token' => uniqid(base64_encode(str_random(60)))
        ]);
        $shipper = User::find($newUser->id);
//        $shipper->products()->attach($data['product_type']);


        if($data['sale_person']){
            $sale_person = new SalePersonTag();
            $sale_person->admin_id = $data['sale_person'];
            $sale_person->user_id = $newUser->id;
            $sale_person->status = 0;
            $sale_person->save();
        }
        $first = TRUE;

        foreach ($data['pickup_address'] as $index => $pickup_address) {
            if ($first) {
                UserShippingInfo::create([
                    'user_id' => $newUser->id,
                    'pickup_address' => $pickup_address,
                    'poc' => $data['shipping_poc'][$index],
                    'phone' => $data['shipping_phone'][$index],
                    'email' => $data['shipping_email'][$index],
                    'city_id' => $data['shipping_city'][$index],
                    'default_address' => TRUE
                ]);

                $first = FALSE;
            }
            else {
                UserShippingInfo::create([
                    'user_id' => $newUser->id,
                    'pickup_address' => $pickup_address,
                    'poc' => $data['shipping_poc'][$index],
                    'phone' => $data['shipping_phone'][$index],
                    'email' => $data['shipping_email'][$index],
                    'city_id' => $data['shipping_city'][$index]
                ]);
            }
        }

        $default_bank = TRUE;
        foreach($data['bank_name'] as $rowId => $bank){
            if($data['nature_of_account'] == 1){
                if($default_bank){
                    UserBankInfo::create([
                        'user_id'=>$newUser->id,
                        'bank_name'=> $bank,
                        'bank_branch'=>$data['bank_branch'][$rowId],
                        'account_no'=>$data['account_no'][$rowId],
                        'account_title'=>$data['account_title'][$rowId],
                        'iban'=> strtoupper($data['iban_no'][$rowId]),
                        'payment_cycle'=>$data['cycle_of_payment'],
                        'city_id'=>$data['bank_city'][$rowId],
                        'default_bank' => 1
                    ]);

                    $default_bank = FALSE;
                }else{
                    UserBankInfo::create([
                        'user_id'=>$newUser->id,
                        'bank_name'=> $bank,
                        'bank_branch'=>$data['bank_branch'][$rowId],
                        'account_no'=>$data['account_no'][$rowId],
                        'account_title'=>$data['account_title'][$rowId],
                        'iban'=> strtoupper($data['iban_no'][$rowId]),
                        'payment_cycle'=>$data['cycle_of_payment'],
                        'city_id'=>$data['bank_city'][$rowId]
                    ]);
                }

            }else{
                $generation_date = null;
                if($data['cycle_of_invoicing'] == 2){
                    $generation_date = null;
                }else{
                    $generation_date = $data['generation_date'];
                }

                UserBankInfo::create([
                    'user_id'=>$newUser->id,
                    'bank_name'=>$bank,
                    'bank_branch'=>$data['bank_branch'][$rowId],
                    'account_no'=>$data['account_no'][$rowId],
                    'account_title'=>$data['account_title'][$rowId],
                    'iban'=> strtoupper($data['iban_no'][$rowId]),
                    'payment_cycle'=> $data['cycle_of_payment'],
                    'city_id'=> $data['bank_city'][$rowId],
                    'invoicing_cycle_id' => $data['cycle_of_invoicing'],
                    'generation_date' => $generation_date,
                    'billing_person_name' => $data['billing_person_name'],
                    'billing_person_phone' => $data['billing_person_phone'],
                    'billing_person_email' => $data['billing_person_email'],
                    'billing_address' => $data['billing_address'],
                ]);
            }
        }
        

        $token = uniqid(base64_encode(str_random(60)));
        $crf_terms_and_conditions = new CRFTermsConditions();
        $crf_terms_and_conditions->user_id = $newUser->id;
        $crf_terms_and_conditions->token = $token;
        $crf_terms_and_conditions->save();

        $route = route('cod.email.verified', ['user_id' => $newUser->id]);

        $subject = 'Sonic - Account Verification';

        $html = '<div style="height: 100%; width: 100%; left: 0; top: 0; overflow: hidden; position: fixed;background-color: #F5F5F5">
                    <div align="center" style="overflow: hidden; display: flex; justify-content:space-around; margin-bottom: 20px;">
                        <img src="' . asset('img/sonic_logo.png') . '" alt="Sonic" style="display: inline-block; width: 10%;">
                        <img src="' . asset('img/trax_logo.png') . '" alt="Trax" style="display: inline-block; width: 15%">
                    </div>';
        $html .= '<div align="center" style="margin-bottom: 0px; background-color: #ffffff">
                    <h3 style="margin-top: 0px; margin-bottom: 0px;">Thank you for choosing Trax Logistics</h3>
                    <p>Dear '. $newUser->name .','. PHP_EOL .'You are almost ready to start working with us.'. PHP_EOL .'To finish signing up, simply click below to verify your email address.</p>
                    <div align="center" style="overflow: hidden; display: flex; justify-content:space-around;">
                        <a href="'.$route.'" target="_blank" style="background-color: #003399; color: white; padding: 1em 1.5em; text-decoration: none;">Verify Your Account</a>
                    </div>
                </div>
                    <p align="center" style="margin-top: 0px; margin-bottom: 0px;">Copyright © 2020 By Trax Logistics, All Rights Reserved.</p>
                </div>';
        $body = $html;
        $to = $newUser->email;
        $mail = Mail::to($to);

        $mail->send(new Notifications($subject, $body, null));

        return $newUser;
    }
    public function email_verified($id){
        $user = User::find($id);
        $user->email_verified = 1;
        $user->save();
        return view('client.register_success')->with(['verify' => 1]);
    }
    public function register_success(){
        return view('client.register_success')->with(['verify' => 0]);
    }
    public function addressView(){
        $products = Product::all();

        // This needs to be modified to reflect the new Logic of Admin able to Select which City has Pickup enabled, which Booking Type is enabled and accordingly which Shipping Mode is enabled. PickupType is no longer valid.
        // $cities = PickupType::find(1)->cities()->orderBy('city_name')->get();
        $cities = City::where('pickup',1)->get();
        return view('client.components.pickup_address')->with(['cities'=>$cities,'products'=>$products]);
    }
    public function bankView(){
        $banks = BanksList::all();
        $city_list = City::where('status',1)->get();
        return view('client.components.banks')->with(['banks'=>$banks,'all_cities'=>$city_list]);
    }
    public function checkCompanyName(Request $request){

//        dd($request);
           $name = $request->name;
           $res = User::where('name','LIKE',$name)->get();
            if(!$res->isEmpty()){
                return response()->json([
                    'message' => 'name already exists',
                    'status' => 0
                ]);
            }else{
                return response()->json([
                    'message' => 'name available',
                    'status' => 1
                ]);
            }
    }

    public function checkCompanyEmail(Request $request){

//        dd($request);
        $email = $request->email;
        $id = $request->id;
        $res = User::where('email',$email)->where('id','<>',$id)->get();
        if(!$res->isEmpty()){
//            return response()->json([
//                'message' => 'email already exists',
//                'status' => 0
//            ]);
            return 'false';
        }else{
//            return response()->json([
//                'message' => 'email available',
//                'status' => 1
//            ]);
            return 'true';
        }
    }

    public function checkCompanyNameProfile(Request $request){

//        dd($request);
        $name = $request->name;
        $id = $request->id;
        $res = User::where('name',$name)->where('id','<>',$id)->get();
        if(!$res->isEmpty()){
//            return response()->json([
//                'message' => 'name already exists',
//                'status' => 0
//            ]);
            return 'false';
        }else{
//            return response()->json([
//                'message' => 'name available',
//                'status' => 1
//            ]);
            return 'true';

        }
    }

}
