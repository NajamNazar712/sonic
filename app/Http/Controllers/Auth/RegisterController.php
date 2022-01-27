<?php

namespace App\Http\Controllers\Auth;

use App\Http\Models\AccountType;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AreaTerritory;
use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\Admin\Territory;
use App\Http\Models\AverageShipmentCycle;
use App\Http\Models\BanksList;
use App\Http\Models\City;
use App\Http\Models\Commission\SalesCommission;
use App\Http\Models\Commission\SalesCommissionUser;
use App\Http\Models\CRFTermsConditions;
use App\Http\Models\DuplicateUser;
use App\Http\Models\InvoicingCycle;
use App\Http\Models\Reference;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\Shipper\UserBankInfo;
use App\Http\Models\UserDocumentAttachment;
use App\Mail\Notifications;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Models\Product;
use App\Http\Models\PickupType;
use App\Http\Models\Segment;
use App\Http\Models\SubCategorySegment;

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
    protected $redirectTo = '/cod/register/user/success';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm($lead_id = NULL)
    {
        /*if($lead_id == NULL){
            return redirect()->route('cod.getstarted');
        }*/
        if($lead_id != NULL){
            $shipper = User::where('lead_id', $lead_id);
            if($shipper->exists()){
                return redirect()->route('cod.getstarted.success');
            }
            $lead = Lead::find($lead_id);
        }
        else{
            $lead = NULL;
        }
        $account_type = AccountType::all();
        $products = Product::all();
        $banks = BanksList::all();
        $city_list = City::where('status',1)->where('business_category_id' ,1)->where('id','!=',1244)->get();
        $pickup_city_list = City::where('pickup',1)->where('status',1)->get();
        $references = Reference::all();
        $average_shipment_durations = AverageShipmentCycle::all();
//        $sales_persons = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('admins.status', 1)->where('ar.department_id', 7);
        $segments = Segment::all();
        $sub_segments = SubCategorySegment::all();
        

        // This needs to be modified to reflect the new Logic of Admin able to Select which City has Pickup enabled, which Booking Type is enabled and accordingly which Shipping Mode is enabled. PickupType is no longer valid.
        // $cities = PickupType::find(1)->cities()->orderBy('city_name')->get();
        $invoicing_cycle = InvoicingCycle::all();
        return view('client.auth.register')->with(['products'=>$products,'cities'=>$city_list,'pickup_city_list'=>$pickup_city_list,'all_cities'=>$city_list,'banks'=>$banks,'account_types' => $account_type, 'references' => $references, 'average_shipment_durations' => $average_shipment_durations, 'segments' => $segments,'sub_segments' => $sub_segments, 'lead' => $lead,'invoicing_cycle' => $invoicing_cycle]);
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
                'shipper_poc'=>'required|regex:/^[a-zA-Z ]+$/u|max:255',
                'company_address'=>'required|string|max:255',
                'phone'=>'required|string|max:255|unique:users',
                'nature_of_account' => 'required',
                'average_shipment' => 'required',
                'sale_person' => 'required',
                'average_shipment_duration' => 'required',
                'cnic'=>'required|string|max:255',
				'url'=>'required|string|max:255',
                'shipper_city'=>'required|int',
                'shipper_product_type'=>'required|int',
                'product_name' => 'required_if:shipper_product_type, ==, 24',
                'shipping_city.*'=>'required|string|max:255',
                'pickup_address.*'=>'required|string|max:255',
                'pickup_brand_name.*'=>'required|string|max:255',
                'shipping_poc.*'=>'required|regex:/^[a-zA-Z ]+$/u|max:255',
                'shipping_phone.*'=>'required|string|max:255',
                'shipping_email.*'=>'required|string|max:255',
                'product_type.*'=>'required|max:255',
                'bank_city.*'=>'required|max:255',
                'bank_name.*'=>'required|max:255',
                'bank_branch.*'=>'required|string|max:255',
                'account_no.*'=>'required|string|max:255',
                'account_title.*'=>'required|string|max:255',
                'iban_no.*'=>'required|string|max:255',
                'cnic_front_image' => 'mimes:png,jpeg,jpg',
                'cnic_back_image' => 'mimes:png,jpeg,jpg',
                'blank_cheque_image' => 'mimes:png,jpeg,jpg',
                'g-recaptcha-response' => 'required|captcha',
                'segments' => 'required',
                'sub_segments' => 'required'
            ]);
        }else{
            return Validator::make($data, [
                'name' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'shipper_poc'=>'required|regex:/^[a-zA-Z ]+$/u|max:255',
                'company_address'=>'required|string|max:255',
                'phone'=>'required|string|max:255|unique:users',
                'nature_of_account' => 'required',
                'average_shipment' => 'required',
                'sale_person' => 'required',
                'average_shipment_duration' => 'required',
                'cnic'=>'required|string|max:255',
				'url'=>'required|string|max:255',
                'shipper_city'=>'required|string|max:255',
                'shipper_product_type'=>'required|int',
                'product_name' => 'required_if:shipper_product_type, ==, 24',
                'shipping_city.*'=>'required|string|max:255',
                'pickup_address.*'=>'required|string|max:255',
                'pickup_brand_name.*'=>'required|string|max:255',
                'shipping_poc.*'=>'required|regex:/^[a-zA-Z ]+$/u|max:255',
                'shipping_phone.*'=>'required|string|max:255',
                'shipping_email.*'=>'required|string|max:255',
                'product_type.*'=>'required|max:255',
                'bank_city.*'=>'required|max:255',
                'bank_name.*'=>'required|max:255',
                'bank_branch.*'=>'required|string|max:255',
                'account_no.*'=>'required|string|max:255',
                'account_title.*'=>'required|string|max:255',
                'iban_no.*'=>'required|string|max:255',
//                'generation_date' => 'required_if:cycle_of_invoicing,==,1|required_if:cycle_of_invoicing,==,3|numeric',
                'billing_person_name' => 'required|string|max:255',
                'billing_person_phone' => 'required|string|max:255',
                'billing_person_email' => 'required|string|email|max:255',
                'billing_address' => 'required|string|max:255',
                'cnic_front_image' => 'mimes:png,jpeg,jpg',
                'cnic_back_image' => 'mimes:png,jpeg,jpg',
                'blank_cheque_image' => 'mimes:png,jpeg,jpg',
                'g-recaptcha-response' => 'required|captcha',
                'segments' => 'required',
                'sub_segments' => 'required',
                'cycle_of_invoicing' => 'required'
            ]);
        }

    }

    public function register(Request $request)
    {
//        return $request;
//         return var_dump($request);exit();
       // dd($request);
//        $products = implode(',',$request->product_type);
//
//        return $request;

        $this->validator($request->all())->validate();
        event(new Registered($user = $this->create($request->all())));

        //$this->guard()->login($user);
        
        $user_attachment = new UserDocumentAttachment();
        $user_attachment->user_id = $user->id;
        $date = Carbon::now()->format('Y_m_d');

        if ($request->hasFile('cnic_front_image')) {
            if($user_attachment->cnic_front_image != NULL) {
                Storage::disk('public')->delete('users_attached_documents/' . $request->user_id . '/' . $user_attachment->cnic_front_image);
            }
            $filename = 'cnic_front_image_' . $date . '_' . $user->id . '.png';
            $file = $request->file('cnic_front_image');
            Storage::disk('public')->putFileAs('users_attached_documents/'. $user->id .'', $file, $filename);
            $user_attachment->cnic_front_image = $filename;
        }
        
        if ($request->hasFile('cnic_back_image')) {
            if($user_attachment->cnic_back_image != NULL) {
                Storage::disk('public')->delete('users_attached_documents/' . $request->user_id . '/' . $user_attachment->cnic_back_image);
            }
            $filename = 'cnic_back_image_' . $date . '_' . $user->id . '.png';
            $file = $request->file('cnic_back_image');
            Storage::disk('public')->putFileAs('users_attached_documents/'. $user->id .'', $file, $filename);
            $user_attachment->cnic_back_image = $filename;
        }
        
        if ($request->hasFile('blank_cheque_image')) {
            if($user_attachment->blank_cheque_image != NULL){
                Storage::disk('public')->delete('users_attached_documents/'. $request->user_id .'/'. $user_attachment->blank_cheque_image);
            }
            $filename = 'blank_cheque_image_' . $date . '_' . $user->id . '.png';
            $file = $request->file('blank_cheque_image');
            Storage::disk('public')->putFileAs('users_attached_documents/'. $user->id .'', $file, $filename);
            $user_attachment->blank_cheque_image = $filename;
        }
        $user_attachment->uploaded_at = Carbon::now();
        $user_attachment->save();

        
        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    public function duplicate_user_info($user_id, $name, $phone1, $phone2, $cnic, $ibans){
        $new_name = explode(' ', $name);
        $name_flag = false;
        $phone_flag = false;
        $cnic_flag = false;
        $iban_flag = false;

        foreach ($new_name as $n){
            $user_name = User::where('id','<>', $user_id)->where('name', 'like', '%' . $n . '%');
            if($user_name->exists()){
                $name_flag = true;
            }
        }
        $user_phone = NULL;
        $phone_number = User::where('id', '<>', $user_id);
        $phone_number = $phone_number->where(function ($query) use ($phone1) {
            $query->where(function ($sub_query) use ($phone1) {
                $sub_query->where('users.phone',  $phone1);
            })
                ->orWhere(function ($sub_query) use ($phone1) {
                    $sub_query->where('users.phone2', $phone1);
                });
        });
        if($phone_number->exists()){
            $phone_flag = true;
            $user_phone = $phone1;
        }else{
            if($phone2 != null){
                $phone_number2 = User::where('id', '<>', $user_id);
                $phone_number2 = $phone_number2->where(function ($query) use ($phone2) {
                    $query->where(function ($sub_query) use ($phone2) {
                        $sub_query->where('users.phone',  $phone2);
                    })
                        ->orWhere(function ($sub_query) use ($phone2) {
                            $sub_query->where('users.phone2', $phone2);
                        });
                });
                if($phone_number2->exists()){
                    $phone_flag = true;
                    $user_phone = $phone2;
                }
            }

        }

        $user_cnic = User::where('id','<>', $user_id)->where('cnic', $cnic);
        if($user_cnic->exists()){
            $cnic_flag = true;
        }
        $iban_no = NULL;
        foreach ($ibans as $iban) {
            $bank = UserBankInfo::where('user_id', '<>', $user_id)->where('iban', $iban);
            if($bank->exists()){
                $iban_flag = true;
                $iban_no = $iban;
            }
        }

        if($name_flag == true || $phone_flag == true || $cnic_flag == true || $iban_flag == true){

            $duplicate = new DuplicateUser();
            $duplicate->user_id = $user_id;
            if($name_flag){
                $duplicate->name = $name;
            }
            if($phone_flag){
                $duplicate->phone = $user_phone;
            }
            if($cnic_flag){
                $duplicate->cnic = $cnic;
            }
            if($iban_flag){
                $duplicate->iban = $iban_no;
            }
            $duplicate->save();
        }

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {      
        if(array_key_exists('lead_id', $data)){
            $lead_id = $data['lead_id'];
        }
        else{
            $lead_id = null;
        }
        if(array_key_exists('territory_id', $data)){
            $territory_id = $data['territory_id'];
        }
        else{
            $territory_id = null;
        }

        $newUser = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'address' => $data['company_address'],
            'poc' => $data['shipper_poc'],
            'phone'=>$data['phone'],
            'phone2'=>$data['phone2'],
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
            'brand_name' => $data['brand_name'],
            'segment_id' => $data['segments'],
            'sub_segment_id' => $data['sub_segments'],
            'lead_id' => $lead_id,
            'api_token' => uniqid(base64_encode(str_random(60))),
            'territory_id' =>  $territory_id
        ]);
        $shipper = User::find($newUser->id);
//        $shipper->products()->attach($data['product_type']);


        if($data['sale_person']){
            $sale_person = new SalePersonTag();
            $sale_person->admin_id = $data['sale_person'];
            $sale_person->user_id = $newUser->id;
            $sale_person->status = 0;
            $sale_person->save();

            $sales_commission = new SalesCommission();
            $sales_commission->shipper_id = $newUser->id;
            $sales_commission->commission_users_count = 1;
            $sales_commission->commission = 2.5;
            $sales_commission->save();
            $sales_commission_id = $sales_commission->id;
            $sales_commission_user = new SalesCommissionUser();
            $sales_commission_user->sales_commission_id = $sales_commission_id;
            $sales_commission_user->tier_type_id = 1;
            $sales_commission_user->tier_id = 1;
            $sales_commission_user->user_id = $data['sale_person'];
            $sales_commission_user->commission = 2.5;
            $sales_commission_user->save();
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
                    'pickup_brand_name' => $data['pickup_brand_name'][$index],
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
                    'city_id' => $data['shipping_city'][$index],
                    'pickup_brand_name' => $data['pickup_brand_name'][$index],
                    
                ]);
            }
        }
        $iban_array = array();
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
                        'city_id'=>$data['bank_city'][$rowId],
                        'default_bank' => 1
                    ]);
                    $iban_array[] = $data['iban_no'][$rowId];
                    $default_bank = FALSE;
                }else{
                    UserBankInfo::create([
                        'user_id'=>$newUser->id,
                        'bank_name'=> $bank,
                        'bank_branch'=>$data['bank_branch'][$rowId],
                        'account_no'=>$data['account_no'][$rowId],
                        'account_title'=>$data['account_title'][$rowId],
                        'iban'=> strtoupper($data['iban_no'][$rowId]),
                        'city_id'=>$data['bank_city'][$rowId]
                    ]);
                    $iban_array[] = $data['iban_no'][$rowId];
                }

            }else{
                $generation_date = $data['generation_date'];
                $invoicing_cycle = $data['cycle_of_invoicing'];

                UserBankInfo::create([
                    'user_id'=>$newUser->id,
                    'bank_name'=>$bank,
                    'bank_branch'=>$data['bank_branch'][$rowId],
                    'account_no'=>$data['account_no'][$rowId],
                    'account_title'=>$data['account_title'][$rowId],
                    'iban'=> strtoupper($data['iban_no'][$rowId]),
                    'city_id'=> $data['bank_city'][$rowId],
                    'invoicing_cycle_id' => $invoicing_cycle,
                    'generation_date' => $generation_date,
                    'billing_person_name' => $data['billing_person_name'],
                    'billing_person_phone' => $data['billing_person_phone'],
                    'billing_person_email' => $data['billing_person_email'],
                    'billing_address' => $data['billing_address'],
                    'default_bank' => 1
                ]);
            }
        }
        self::duplicate_user_info($newUser->id, $data['name'], $data['phone'], $data['phone2'], $data['cnic'], $iban_array);

        $token = uniqid(base64_encode(str_random(60)));
        $crf_terms_and_conditions = new CRFTermsConditions();
        $crf_terms_and_conditions->user_id = $newUser->id;
        $crf_terms_and_conditions->token = $token;
        $crf_terms_and_conditions->save();


        $banks_infos =array();
        $user_bank_infos = UserBankInfo::where('user_id', $newUser->id)->get();

        $route = route('cod.email.verified', ['user_id' => $newUser->id]);

        $subject = 'Sonic - Account Verification';

        $html = '<div style="height: 100%; width: 100%; left: 0; top: 0; overflow: hidden; position: fixed;background-color: #F5F5F5">
                    <div align="center" style="overflow: hidden; display: flex; justify-content:space-around; margin-bottom: 20px;">
                        <img src="' . asset('img/sonic_logo_new.png') . '" alt="Sonic" style="display: inline-block; width: 10%;">
                        <img src="' . asset('img/trax_logo_new.png') . '" alt="Trax" style="display: inline-block; width: 15%">
                    </div>';
        $html .= '<div align="center" style="margin-bottom: 0px; background-color: #ffffff">
                    <h3 style="margin-top: 0px; margin-bottom: 0px;">Thank you for choosing Trax Logistics</h3>
                    <p>Dear '. $newUser->name .','. PHP_EOL .'You are almost ready to start working with us.'. PHP_EOL .'You have entered Your Contact number is: '. $newUser->phone .', address: '. $newUser->address .''. PHP_EOL .'Your Bank information is:';
                    if(count($user_bank_infos) > 0){
                        $html .='<table style="width:100%;">';
                        $html .= '<thead><tr>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Account #</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Account Title</th>
                                           <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Branch Name</th></tr></thead><tbody>';
                        foreach($user_bank_infos as $banks_info){
                            $html .='<tr>';
                            $html .='<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">'.$banks_info->account_no.'</td>';
                            $html .='<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">'.$banks_info->account_title.'</td>';
                            $html .='<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">'.$banks_info->bank_branch.'</td>';
                            $html .='</tr>';
                        }

                        $html .= '</tbody></table>';
                    }else{
                        $html .= '<p>No bank information found.</p>';
                    }

        $html .= 'To finish signing up, simply click below to verify your email address.</p>  <div align="center" style="overflow: hidden; display: flex; justify-content:space-around;">
                        <a href="'.$route.'" target="_blank" style="background-color: #003399; color: white; padding: 1em 1.5em; text-decoration: none;">Verify Your Account</a>
                    </div>
                </div>
                    <p align="center" style="margin-top: 0px; margin-bottom: 0px;">Copyright © 2020 By Trax Logistics, All Rights Reserved.</p>
                </div>';
        $body = $html;
        $to = array();
        $to[] = $newUser->email;
        $admins_sales = Admin::where('role_id', 4)->where('status', 1);
        if ($admins_sales->exists()) {
            $to = array_merge($to, $admins_sales->pluck('email')->toArray());
        }
        
        $city_id = $shipper->city_id;
        $hub_id = City::find($city_id)->hub_id;
        $managers = Admin::whereIn('role_id',[31,44])->where('status', 1)->pluck('id','email')->toArray();
        foreach($managers as $rms => $index){
            $admin_hubs = AdminHub::where('admin_id',$index);
            if($admin_hubs->exists()){
                $admin_hubs = $admin_hubs->pluck('hub_id')->toArray();
                if(in_array($hub_id , $admin_hubs)) {
                    $to[] = $rms;
                }
            }
        }
        $to = array_values(array_filter($to));
        if(!empty($to)){
            $mail = Mail::to($to);

            $mail->send(new Notifications($subject, $body, null));
        }


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

    public function sales_person(Request $request){
//        dd($request);
        $id = $request->id;
        if($id){
            $sales_persons_city = City::where('id', $id);
            if ($sales_persons_city->exists()){
                $sales_persons_city = $sales_persons_city->first();
                $hub_id = $sales_persons_city->hub_id;
                $admin_ids = AdminHub::where('hub_id', $hub_id)->pluck('admin_id')->toArray();
                $sale_persons = Admin::join('admin_roles as ar','admins.role_id', '=','ar.id')->select(['admins.id', 'admins.name'])->where('admins.status', 1)->where('ar.department_id', 7)->whereIn('admins.id', $admin_ids)->where('ar.id','!=' ,4)->get();
                return response()->json(['status' => 0, 'sale_persons' => $sale_persons]);
            }else{
                $sale_person_admin = City::find($id)->name;
                return response()->json(['status' => 1, 'error' => 'No sales person found for the selected city: ' . $sale_person_admin]);
            }
        }
    }

    public function territory(Request $request)
    {
        $city_id = $request->id;
        if ($city_id) {
            $territory = Territory::where('city_id', $city_id);
            if ($territory->exists()) {
                $territory = $territory->get();
                return response()->json(['status' => 0, 'territory' => $territory]);
            }
        } else {
            return response()->json(['status' => 1, 'error' => "No Territory found for the selected city"]);
        }
    }
    public function area(Request $request)
    {
        $territory_id = $request->id;
        if ($territory_id) {
            $area_territory = AreaTerritory::where('territory_id', $territory_id);
            if ($area_territory->exists()) {
                $area_territory = $area_territory->get();
                return response()->json(['status' => 0, 'area' => $area_territory]);
            }
        } else {
            return response()->json(['status' => 1, 'error' => "No Area found for the selected territory"]);
        }
    }

    public function get_sub_segment(Request $request){
        $sub_segments = SubCategorySegment::where('segment_id',$request->segment_id);
        if($sub_segments->exists()){
            $sub_segments = $sub_segments->get();
            return response()->json(['status' => 0, 'sub_segments' => $sub_segments]);
        }else{
            return response()->json(['status' => 1]);
        }
    }

}
