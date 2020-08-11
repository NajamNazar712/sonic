<?php

namespace App\Http\Controllers\Admins;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VisionSoftAPIController extends Controller
{
    static public function login(){
        try{
            $client = new Client(['base_uri' => 'http://traxapi.reactivelogix.com/api/TRAX/', 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
            $response = $client->post('Login', [
                'form_params' => [
                    'pin_cmp' => 6,
                    'pin_kp' => 'A',
                    'pin_loginid' => 'GB',
                    'pin_password' => 'SOFT'
                ]
            ]);

            if ($response->getStatusCode() != 200) {
                $response = $response->getBody()->getContents();

                var_dump("NO");
            }
            else {
                var_dump("YES");
            }
        }
        catch(RequestException $e){
            echo 1;
        }
    }
    static public function customers(){

    }
    static public function customer_banks(){

    }
    static public function city_hub(){

    }
    static public function employees(){

    }
    static public function cities(){

    }
    static public function cod_payable(){

    }
    static public function cod_receivable(){

    }
}
