<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\UserDocumentAttachment;
use App\Models\FingaApiLog;

class FingaIntegrationController extends Controller
{

    private static function getToken($api) {

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

        $api = env('FINGA_URL');
        $user = Auth::user();
        $token = $this->getToken($api);
        $url = $this->getLoginUrl($api, $token, $user->phone, $user->cnic, $user->email);

        if($url) {
            return view('client.finga_dashboard')->with(['url'=> $url]);
        }
    }

    public function signUp(Request $request) {

        $api = env('FINGA_URL');
        $token = $this->getToken($api); 
        $cnic_front = '';
        $cnic_back = '';

        if($token) {

            $user = Auth::user();
            $user_documents = UserDocumentAttachment::where('user_id', $user->id)->first();
            if($user_documents) {
                $cnic_front = Storage::url('users_attached_documents/' . $user->id . '/' . $user_documents->cnic_front_image);
                $cnic_back = Storage::url('users_attached_documents/' . $user->id . '/' . $user_documents->cnic_back_image);
            }

            $requestPayload = [
                "client_id" => $user->id,
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
                $user->wallet_id = $body->wallet_id;
                $user->save();

                $mobile_no = $body->users[0]->mobile_no;
                $cnic = $body->users[0]->cnic;
                $email = $body->users[0]->email;

                $url = $this->getLoginUrl($api, $token, $mobile_no, $cnic, $email);

                if($url) {
                    return view('client.finga_dashboard')->with(['url'=> $url]);
                }
                
            } else {
                $body = $response->getBody();
                $body = json_decode($body);
                FingaApiLog::create([
                    'nature' => 'on-boarding',
                    'status' => isset($body->status) ? $body->status : 0,
                    'details' => isset($body->users) ? json_encode($body->users): json_encode($body),
                ]);

                return redirect()->back()->with('finga_error', 'Unable to Process Wallet Request. Please contact with your Sales Person');
            }
            
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

}
