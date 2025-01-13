<?php

namespace App\Http\Controllers;

use App\Http\Models\Shipper\User;
use App\Http\Models\UserDocumentAttachment;
use App\Http\Models\WalletUser;
use App\Models\FingaApiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FingaIntegrationController extends Controller
{

    public static function getToken($api) {

        $response = Http::withHeaders([
            'accept' => 'application/json',
            
        ])->post($api.'login/', [
            "username" => "sonic",
            "password" => "4TE7+r]7ddI2",
        ]);

        if($response->successful()) { 
           
            $body = $response->getBody();
            $body = json_decode($body);
            $token = $body->token;
            return $token;
            
        }

    }
    public function login(Request $request) {
        $wallet = auth()->user()->load('wallet');
        $user = isset($wallet->wallet)??null;
        if($user) {
            $api = config('app.FINGA_URL');
            $user = WalletUser::where('user_id', session('user_id'))->where('substitute_user_id', 0)->first();
            $token = $this->getToken($api);
            $url = $this->getLoginUrl($api, $token, $user->phone, $user->cnic, $user->email);

            if ($url) {
                return view('client.finja_dashboard')->with(['url' => $url]);
            }
        }
        return  redirect()->back();
    }

    public static function signUp($user = array()) {

        $user = (object) $user;
        $api = config('app.FINGA_URL');
        $token = self::getToken($api);
        $cnic_front = '';
        $cnic_back = '';
        $user_id = session('user_id');

        if($token) {
            $result = array();
            $user_documents = UserDocumentAttachment::where('user_id', $user_id)->first();
            if($user_documents) {
                $cnic_front = Storage::url('users_attached_documents/' . $user_id . '/' . $user_documents->cnic_front_image);
                $cnic_back = Storage::url('users_attached_documents/' . $user_id . '/' . $user_documents->cnic_back_image);
            }

            $requestPayload = [
                "client_id" => $user_id,
                "client_name" => $user->name,
                "users" => [
                    [
                        "cnic" => $user->cnic,
                        "email" => $user->email,
                        "mobile_no" => $user->phone,
                        "name" => $user->name,
                        "cnic_front_image_url" => $cnic_front,
                        "cnic_back_image_url" => $cnic_front
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'accept' => 'application/json',
                'Authorization' => "Bearer " . $token,
            ])->post($api . 'wallet/onboard-users/', $requestPayload);

            FingaApiLog::create([
                'nature' => 'request',
                'status' => 1,
                'details' => json_encode($requestPayload, JSON_PRETTY_PRINT), // Save as JSON
            ]);

            if($response->successful()) { 
           
                $body = $response->getBody();
                $body = json_decode($body);

                $mobile_no = $body->users[0]->mobile_no;
                $cnic = $body->users[0]->cnic;
                $email = $body->users[0]->email;

                $url = self::getLoginUrl($api, $token, $mobile_no, $cnic, $email);
                self::apiLog('on-boarding-response', 'success', $body ,null);

                $result['url'] = $url;
                $result['wallet_id'] = $body->wallet_id;

            } else {
                $body = $response->getBody();
                $body = json_decode($body);

                self::apiLog('on-boarding-response', 'error', $body ,null);
            
               $result['error'] = $body;
            }

            return $result;
            
        }
    } 

    private static function getLoginUrl($api, $token, $mobile_no, $cnic, $email ) {

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

    public static function apiLog($nature, $status, $details, $shipment_id) {

        FingaApiLog::create([
            'nature' => $nature,
            'status' => $status,
            'details' => $details ? json_encode($details, JSON_PRETTY_PRINT) : null, // Save as JSON
            'shipment_id' => $shipment_id
        ]);
    }
    
    public function on_boarding(Request  $request){
        if(session('user_type') == 1) {
            $user = User::find(session('user_id'));
            return view('client.finja_onboarding')->with(['user' => $user]);
        }else{
            return redirect()->back();
        }
    }

}
