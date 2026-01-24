<?php

namespace App\Http\Controllers;

use App\Http\Models\Shipper\User;
use App\Http\Models\UserDocumentAttachment;
use App\Http\Models\WalletUser;
use App\Models\FingaApiLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Auth;
use Illuminate\Support\Facades\App;
use App\Models\WalletUserDisable;
class FingaIntegrationController extends Controller
{

    public function __construct() {
        $this->middleware('auth:web,substitute_users');
        $this->middleware('Permission')->except('wordpress_access_denied', 'wordpressAddressView','wordpressBankView');
    }
    public static function getToken($api)
    {
        // Define cache key for token
        $cacheKey = 'wallet_api_token';

        // Check if token exists in cache and is still valid
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        $password = config('app.FINGA_PASSWORD');
//        $password = App::environment(['local', 'staging']) ? "" : $env_password;

        // Make API request for new token
        $response = Http::withHeaders([
            'accept' => 'application/json',
        ])->post($api . 'login/', [
            "username" => "sonic",
            "password" => $password,
        ]);

        // If the request is successful, cache the token for 4 minutes
        if ($response->successful()) {
            $body = json_decode($response->body());

            if (isset($body->token)) {
                Cache::put($cacheKey, $body->token, Carbon::now()->addMinutes(3));
                return $body->token;
            }
        }

        return null; // Return null if request fails
    }
    public function login(Request $request) {

        $api = config('app.FINGA_URL');
        $token = $this->getToken($api);
        if(session('user_id') == 25425) {
            $user = WalletUserDisable::where('user_id', session('user_id'));
        }else{
            $user = WalletUser::where('user_id', session('user_id'));
        }

        if (session('user_type') == 1) {
            $user->where('substitute_user_id', 0);
        } else {
            $user->where('substitute_user_id', session('substitute_user_id'));
        }
        $user = $user->first();
        if(!empty($user)) {

            $phone = $user->phone;
            $cnic = $user->cnic;
            $email = $user->email;

            $url = $this->getLoginUrl($api, $token, $phone, $cnic, $email);

            return view('client.finja_dashboard')->with(['url' => $url]);
        }else{
            return redirect()->back();
        }

    }

    public static function signUp($user = array(), $shipper_id = null) {

        $api = config('app.FINGA_URL');
        $token = self::getToken($api);
        $cnic_front = '';
        $cnic_back = '';
        $user_id = session('user_id') ?? $shipper_id;
        $parent_user = User::find($user_id);

        if($token) {
            $result = array();
            $user_documents = UserDocumentAttachment::where('user_id', $user_id)->first();
            if($user_documents) {
                $cnic_front = Storage::url('users_attached_documents/' . $user_id . '/' . $user_documents->cnic_front_image);
                $cnic_back = Storage::url('users_attached_documents/' . $user_id . '/' . $user_documents->cnic_back_image);
            }

            $requestPayload = [
                "client_id" => $parent_user->id,
                "client_name" => $parent_user->name,
            ];
            foreach ($user as $key2=> $data){
                $requestPayload['users'][] =  [
                    "cnic" => $data['cnic'],
                    "email" => $data['email'],
                    "mobile_no" => $data['phone'],
                    "name" => $data['name'],
                    "cnic_front_image_url" => $cnic_front,
                    "cnic_back_image_url" => $cnic_back
                ];
            }


            $response = Http::withHeaders(['accept' => 'application/json','Authorization' => "Bearer " . $token,])->post($api . 'wallet/onboard-users/', $requestPayload);
            FingaApiLog::create(['nature_id' => 1,'status' => 1,'details' => json_encode($requestPayload, JSON_PRETTY_PRINT)]);

            if ($response->successful()) {
                $body = $response->getBody();
                $body = json_decode($body);

                self::apiLog(2, 'success', $body, null);


                // Ensure the error structure is an array
                $success = [
                    'status' => $body->status ?? 'success',
                    'wallet_id' => $body->wallet_id ?? null,
                    'users' => []
                ];

                if (!empty($body->users)) {
                    foreach ($body->users as $user) {
                        $success['users'][] = [
                            'mobile_no' => $user->mobile_no ?? null,
                            'email' => $user->email ?? null,
                            'cnic' => $user->cnic ?? null,
                            'id' => $user->id ?? null,
                        ];
                    }
                }
                $result = $success;


            } else {
                $body = $response->getBody();
                $body = json_decode($body);

                // Ensure the error structure is an array
                $errorData = [
                    'status' => $body->status ?? 'error',
                    'wallet_id' => $body->wallet_id ?? null,
                    'users' => []
                ];

                if (!empty($body->users)) {
                    foreach ($body->users as $user) {
                        $errorData['users'][] = [
                            'mobile_no' => $user->mobile_no ?? null,
                            'status' => $user->status ?? 'error',
                            'message' => (array)($user->message ?? []),
                        ];
                    }
                }

                self::apiLog(2, 'error', $body, null);
                $result['error'] = $errorData;
            }
            return $result;
        }

    }



    public static function getLoginUrl($api, $token, $mobile_no, $cnic, $email ) {

        $mobile_no = str_replace('-', '', $mobile_no);
        $mobile_no = ltrim($mobile_no, '0');
        $mobile_no = '92' .$mobile_no;  

        $cnic = str_replace('-', '', $cnic);
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => "Bearer " . $token,
        
        ])->post($api.'wallet/check/', [

            "cnic" => $cnic,
            "email" => $email,
            "mobile_no" => $mobile_no,
        ]);

        if($response->successful()) { 
    
            $body = $response->getBody();
            $body = json_decode($body);
            return $body->url;
        }

    }

    public static function apiLog($nature, $status, $details, $shipment_id,$request_id = null, $batch_id = null) {

        $newLog = FingaApiLog::create([
            'nature_id' => $nature,
            'status' => $status,
            'details' => $details ? json_encode($details, JSON_PRETTY_PRINT) : null, // Save as JSON
            'shipment_id' => $shipment_id,
            'request_id'=>$request_id,
            'batch_id' => $batch_id
        ]);
        return $newLog->id;

    }
    
    public function on_boarding(Request  $request){
        if(session('user_type') == 1) {
            $user = User::find(session('user_id'));
            return view('client.finja_onboarding')->with(['user' => $user]);
        }else{
            return redirect()->back();
        }
    }
    public function wallet_user(Request  $request){
        if(session('user_type') == 1) {
            $user = User::find(session('user_id'));
            if(isset($user->sub_users)) {
                return view('client.finja_onboarding_substitute')->with(['user' => $user]);
            }else{
                return redirect()->back();
            }
        }else{
            return redirect()->back();
        }
    }

    public function finja_dashboard(Request  $request) {

        $url = $request->input('url'); // Get 'url' from the request

        if ($url) {
            return view('client.finja_dashboard')->with(['url' => $url]);
        }
    }

}
