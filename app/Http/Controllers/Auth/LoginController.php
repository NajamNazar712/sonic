<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Models\RateStatus;
use App\Http\Models\Shipper\User;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\Admin\NpsSurvey;
use App\Http\Models\PackagingCharge;
use App\Http\Models\ShipmentPrebook;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\NpsShipperRatting;
use App\Http\Models\CRFTermsConditions;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\NpsShipperSkipSurvey;
use App\Http\Models\Admin\BackgroundImage;

use App\Http\Models\ProjectArrivalShipper;
use App\Http\Models\Shipper\SubstituteUser;
use App\Http\Models\CorporateDefaultRateStatus;
use App\Http\Models\Shipper\UserOtpVerification;
use App\Http\Models\Admin\Settings\GeneralSetting;
use App\Http\Models\InternationalUsersInformation;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Models\Shipper\SubstituteUserPermission;
use App\Http\Models\Shipper\ShipperAirWaybillSettings;
use App\Http\Controllers\Admins\GlobalSettingsController;
use App\Http\Models\Sister_account\MergedSisterAccountMapping;
use App\Http\Models\Sister_account\Substitute_user\SubstituteUserMergeSisterAccountMapping;
use App\Models\WalletShipperSetting;



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
        $background_image = [];
        $background_images = BackgroundImage::where('background_image_screen_id', 2);
        if ($background_images->exists()) {
            $background_image = $background_images->first();
            $background_image['path'] = 'storage/' . $background_image->picture_path;
            $background_image['version'] = $background_image->version;
        } else {
            $background_image['path'] = "/img/promo-background-11-07-2023.png";
            $background_image['version'] = "2.6";

        }

        return view('client.auth.login')->with(['background_image' => $background_image]);
    }


    public function showLeadWordPressLoginForm($id, $token)
    {
        $background_image = [];
        $background_images = BackgroundImage::where('background_image_screen_id', 2);
        if ($background_images->exists()) {
            $background_image = $background_images->first();
            $background_image['path'] = 'storage/' . $background_image->picture_path;
            $background_image['version'] = $background_image->version;
        } else {
            $background_image['path'] = "/img/promo-background-11-07-2023.png";
            $background_image['version'] = "2.6";
        }

        $lead = Lead::find($id);

        if(isset($lead) && $token == $lead->activation_code){
            return view('client.auth.lead_wordpress_register')->with(['lead' => $lead , 'background_image' => $background_image]);
        }else{
            return view('errors.404');
        }

    }

    protected function attemptLogin(Request $request)
    {
        // dd(1);
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
        $packaging_charges_check = TRUE;
        $user_info = SubstituteUser::where('email',$request->email)->first();
        $user_info_id = ($user_info) ? $user_info->id : NULL;  
       
        session(['substitute_user_id' => $user_info_id]);

        if (session('user_type') == 1) {
            if ($user->blacklist) {
                auth('web')->logout();
                return back()->with('info', 'Your Account is Blacklisted, Contact Admin');
            }
            else if (!in_array($user->status, [0,1,3,2,5,6]) || $user->status == 4) {
                auth('web')->logout();
                return back()->with('info', 'Your Account is Not Activated Yet, Contact Admin');
            }
            else if ($user->phone_number_verified == 0){
                auth('web')->logout();
                return back()->with('info', 'Your Account phone number is not verified, Contact Admin');
            }
            else if ($user->id == 8761) {
                auth('web')->logout();
                return back()->with('info', 'Access Denied!');
            }
            else if ($user->id == 9358) {
                auth('web')->logout();
                return back()->with('info', 'Access Dernied!');
            }
            else {
                $sister_users = MergedSisterAccountMapping::where('head_user_id', $user->id)->pluck('sister_user_id')->toArray();
                session(['sister_users' => $sister_users]);
                session(['user_id' => $user->id]);
                session(['status' => $user->status]);
                session(['request_custom_quotation' => $user->request_custom_quotation]);
                session(['on_board_status' => $user->on_board_status]);

                session(['lead' => $user->lead_id ? true : false]);

                $token = CRFTermsConditions::where('user_id', $user->id)->first()->token ?? null;

                session(['token' => $token]);

                if (SalePersonTag::where('user_id', session('user_id'))->where('status', 0)->exists()){
                    session(['sale_person_status' => 1]);
                }
                else{
                    session(['sale_person_status' => 0]);
                }
                session(['account_type' => $user->account_type_id]);
                //if (PackagingCharge::where('user_id', $user->id)->exists()) {
                //$packaging_charges_check = TRUE;
                //}

                $project_arrival_shipper = ProjectArrivalShipper::where('user_id', $user->id);
                if($project_arrival_shipper->exists()){
                    session(['project_arrival_shipper' => 1]);
                }
                else{
                    session(['project_arrival_shipper' => 0]);
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

                if($user->restrict_order_id == 1){
                    session(['restrict_order_id' => true]);
                }

                $agreement_signed = $user->agreement_signed;
                session(['agreement_signed' => $agreement_signed]);

            }

            $shipper_user_id = $user->id;

            session(['special_dashboard_user' => 0]);
            session(['special_dashboard_sister_user' => []]);
        }
        else {

            if($user_info_id && $user_info->is_created_by_admin == 1)
            {
                session(['special_dashboard_user' => 1]);
                
                $sister_accounts = SubstituteUserMergeSisterAccountMapping::where('substitute_user_id',$user_info_id)->pluck('sister_user_id')->toArray();

                session(['special_dashboard_sister_user' => $sister_accounts]);
            }
            else{
                session(['special_dashboard_user' => 0]);
                session(['special_dashboard_sister_user' => []]);
            }
            
            $shipper = User::find($user->user_id);

            if ($shipper->blacklist) {
                auth('substitute_users')->logout();
                return back()->with('info', 'Your Shipper\'s Account is Blacklisted, Contact Admin');
            }
            else if ($shipper->status != 3) {
                auth('substitute_users')->logout();
                return back()->with('info', 'Your Shipper\'s Account is Not Activated Yet, Contact Admin');
            }
            else if ($shipper->phone_number_verified == 0){
                auth('web')->logout();
                return back()->with('info', 'Your Account phone number is not verified, Contact Admin');
            }
            else if (!$user->status) {
                auth('substitute_users')->logout();
                return back()->with('info', 'Your Account is Disabled');
            }
            else {
                $substitute_user = SubstituteUser::find($user->id);
                $permissions = SubstituteUserPermission::where('substitute_user_id', $user->id)->pluck('permission_id')->toArray();
                $sister_users = MergedSisterAccountMapping::where('head_user_id', $user->user_id)->pluck('sister_user_id')->toArray();
                session(['sister_users' => $sister_users]);
                session(['permissions' => $permissions]);
                session(['user_id' => $user->user_id]);
                session(['account_type' => $shipper->account_type_id]);
                session(['restriction' => $substitute_user->restriction]);
                session(['status' => $shipper->status]);
                session(['request_custom_quotation' => $shipper->request_custom_quotation]);
                session(['on_board_status' => $shipper->on_board_status]);

                $token = CRFTermsConditions::where('user_id', $shipper->id)->first()->token ?? null;

                session(['token' => $token]);


//                if (PackagingCharge::where('user_id', $user->user_id)->exists()) {
//                    $packaging_charges_check = TRUE;
//                }

                $air_waybill_settings = ShipperAirWaybillSettings::where('user_id', $user->user_id);

                if ($air_waybill_settings->exists()) {
                    $air_waybill_settings = $air_waybill_settings->first();

                    session(['air_waybill_type' => $air_waybill_settings->type]);
                }
                else {
                    session(['air_waybill_type' => 1]);
                }

                if($shipper->restrict_order_id == 1){
                    session(['restrict_order_id' => true]);
                }
            }

            $shipper_user_id = $user->user_id;
        }
        // $shipment_pre_book = ShipmentPrebook::where('user_id', $shipper_user_id);
        // if($shipment_pre_book->exists()){
        //     $shipment_pre_book = $shipment_pre_book->first();
        //     session(['prefix' => $shipment_pre_book->prefix]);
        // }
        $shipment_pre_book = ShipmentPrebook::where('user_id', $shipper_user_id)->get();
        if($shipment_pre_book->isNotEmpty()){
            $shipment_pre_book = $shipment_pre_book->pluck('prefix')->toArray();
            session(['prefix' => $shipment_pre_book]);
        }
        session(['packaging_charges_check' => $packaging_charges_check]);

        $rate_status = RateStatus::where('user_id', $shipper_user_id)->where('status', 1);
        if($rate_status->exists() && $user->account_type_id == 1){
            session(['rate_status' => TRUE]);
        }
        else{
            $rate_type_id = User::find($shipper_user_id)->corporate_rate_type_id;
            if($rate_type_id != 3){
                $corporate_rate_status = CorporateRateStatus::where('user_id', $shipper_user_id)->where('status', 1);
                if($corporate_rate_status->exists()){
                    session(['rate_status' => TRUE]);
                }
            }
            else{
                $corporate_default__rate_status = CorporateDefaultRateStatus::where('user_id', $shipper_user_id)->where('status', 1);
                if($corporate_default__rate_status->exists()){
                    session(['rate_status' => TRUE]);
                }
            }
            session(['rate_type_id' => $rate_type_id]);

        }
        $international_rate_status = InternationalUsersInformation::where('user_id', $shipper_user_id)->where('status', 1);
        if($international_rate_status->exists()){
            session(['international_rates' => TRUE]);
        }
        $user_otp = UserOtpVerification::where('user_id', $shipper_user_id);
        if($user_otp->exists()){
            session(['phone_number_unverified' => TRUE]);
        }
        $settings = GlobalSettings::where('type', 'foc_account_tag');
        if ($settings->exists()) {
            $foc_account_tags = array();
            $settings = $settings->first();
            $foc_account_tags = array_map('intval', explode(',', $settings->text));
            if(in_array($shipper_user_id, $foc_account_tags)){
                session(['foc_account' => TRUE]);
            }
        }
        $settings = GlobalSettings::where('type', 'shipper_origin_change');
        if($settings->exists()){
            $settings = $settings->first();
            $origin_shippers = array();
            $origin_shippers = array_map('intval', explode(',' , $settings->text));
            if(in_array($shipper_user_id, $origin_shippers)){
                session(['shipper_origin_change' => TRUE]);
            }
        }
        $settings = GlobalSettings::where('type', 'shipper_return_address');
        if($settings->exists()){
            $settings = $settings->first();
            $return_shippers = array();
            $return_shippers = array_map('intval', explode(',' , $settings->text));
            if(in_array($shipper_user_id, $return_shippers)){
                session(['shipper_return_address' => TRUE]);
            }
        }

        $settings = GlobalSettings::where('type', 'return_shipments_address_change_shippers');
        if($settings->exists()){
            $settings = $settings->first();
            $return_address_change_shippers = array();
            $return_address_change_shippers = array_map('intval', explode(',' , $settings->text));
            if(in_array($shipper_user_id, $return_address_change_shippers)){
                session(['shipment_return_address_change' => TRUE]);
            }
        }

        //Nps Survey check
        $skip_count = 0;
        $hours= 24; //set default 24 hour to by pass the condition
        $now = date('Y-m-d H:i:s',strtotime(Carbon::now()));
        $nps_survey = NpsSurvey::where('start_time','<=',$now)->where('end_time','>=',$now)->where('status',1)->select('id');
        if($nps_survey->exists()){
            $nps =  $nps_survey->first();
            $nps_shipper_rattings = NpsShipperRatting::where('nps_survey_id',$nps->id)->where('user_id',session('user_id'));
            $nps_shipper_skip_surveys = NpsShipperSkipSurvey::where('nps_survey_id',$nps->id)->where('user_id',session('user_id'));
            if($nps_shipper_skip_surveys->exists()){
                $nps_shipper_skip_surveys = $nps_shipper_skip_surveys->first();
                $skip_count = $nps_shipper_skip_surveys->skip_count;
                $skip_time = date('Y-m-d H:i:s',strtotime($nps_shipper_skip_surveys->updated_at));
                $start_date = Carbon::parse($now);
                $end_date = Carbon::parse($skip_time);
                $hours = $end_date->diffInHours($start_date);
            }
//                            dd($nps->id. ' ' .$hours .' '.$skip_count);
            if(!$nps_shipper_rattings->exists() && $skip_count <2 && $hours>=24) {
                session(['nps_survey' => $nps->id]);
            }
        }

        $mms_shippers = array();
        $setting = GlobalSettings::where('type', 'mms_setting')->select('text')->first();
        if ($setting) {
            $mms_shippers = array_map('intval',explode(',' , $setting->text));
          
        }

        session(['mms_shippers' => $mms_shippers]);
        $mms_excel_booking_shippers = array();
        $setting = GeneralSetting::where('type', 'mms_excel_booking_setting')->select('description')->first();
        if ($setting) {
            $mms_excel_booking_shippers = array_map('intval',explode(',' , $setting->description));
          
        }
        session(['mms_excel_booking_shippers' => $mms_excel_booking_shippers]);

        $wallet_sign_up_allow = WalletShipperSetting::where('user_id', $shipper_user_id)->where('status', 1);
        if($wallet_sign_up_allow->exists()){
            session(['wallet_sign_up_allow' => TRUE]);
        }

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
