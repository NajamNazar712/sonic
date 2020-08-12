<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\Admin\VisionSoft\VisionSoftCity;
use App\Http\Models\Admin\VisionSoft\VisionSoftCustomer;
use App\Http\Models\Admin\VisionSoft\VisionSoftCustomerBank;
use App\Http\Models\Admin\VisionSoft\VisionSoftEmployee;
use App\Http\Models\Admin\VisionSoft\VisionSoftError;
use App\Http\Models\Admin\VisionSoft\VisionSoftHub;
use App\Http\Models\City;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserBankInfo;
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
            $status_code = $response->getStatusCode();
            if ($status_code != 200) {
                $response = $response->getBody()->getContents();
                $new_error = new VisionSoftError();
                $new_error->api_id = 1;
                $new_error->status_code = $status_code;
                $new_error->error = $response;
                $new_error->save();
            }
            else {
//                var_dump("YES");
            }
        }
        catch(RequestException $e){
            $new_error = new VisionSoftError();
            $new_error->api_id = 1;
            $new_error->error = 'API Error';
            $new_error->save();
        }
    }
    static public function customers(){
        $old_shippers = VisionSoftCustomer::pluck('shipper_id')->toArray();
        $shippers = User::where('status', 3)->whereNotIn('id', $old_shippers)->get();
        if (count($shippers) > 0) {
            $client = new Client(['base_uri' => 'http://traxapi.reactivelogix.com/api/TRAX/', 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
            foreach ($shippers as $shipper) {
                $sale_person = SalePersonTag::where('status', 0)->first();
                try {
                    $response = $client->post('Customers', [
                        'form_params' => [
                            'pin_code' => 6,
                            'pin_kp' => 'A',
                            'pin_loginid' => 'GB',
                            'pin_password' => 'SOFT',
                            'pin_account_id' => $shipper->id,
                            'pin_account_type' => $shipper->account_type->name,
                            'pin_company_name' => $shipper->name,
                            'pin_contact_person' => $shipper->poc,
                            'pin_phone_no' => $shipper->phone,
                            'pin_company_addr' => $shipper->address,
                            'pin_city_name' => $shipper->city->name,
                            'pin_sperson_tagged' => $sale_person->sales_person->name
                        ]
                    ]);
                    $status_code = $response->getStatusCode();
                    if ($status_code != 200) {
                        $response = $response->getBody()->getContents();
                        $new_error = new VisionSoftError();
                        $new_error->api_id = 2;
                        $new_error->status_code = $status_code;
                        $new_error->error = $response;
                        $new_error->save();
                    } else {
                        $new_shipper = new VisionSoftCustomer();
                        $new_shipper->shipper_id = $shipper->id;
                        $new_shipper->save();
                    }
                } catch (RequestException $e) {
                    $new_error = new VisionSoftError();
                    $new_error->api_id = 2;
                    $new_error->error = 'API Error';
                    $new_error->save();
                }
            }
        }
    }
    static public function customer_banks(){
        $old_banks = VisionSoftCustomerBank::pluck('bank_id')->toArray();
        $banks = UserBankInfo::whereNotIn('id', $old_banks)->get();
        if (count($banks) > 0) {
            $client = new Client(['base_uri' => 'http://traxapi.reactivelogix.com/api/TRAX/', 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
            foreach ($banks as $bank) {
                $shipper = $bank->user;
                if($shipper){
                    try {
                        $response = $client->post('CustomerBanks', [
                            'form_params' => [
                                'pin_code' => 6,
                                'pin_kp' => 'A',
                                'pin_loginid' => 'GB',
                                'pin_password' => 'SOFT',
                                'pin_account_id' => $shipper->id,
                                'pin_account_type' => $shipper->account_type->name,
                                'pin_company_name' => $shipper->name,
                                'pin_account_title' => $bank->account_title,
                                'pin_phone_no' => $shipper->phone,
                                'pin_iban_no' => $bank->iban,
                                'pin_bank_name' => $bank->name
                            ]
                        ]);
                        $status_code = $response->getStatusCode();
                        if ($status_code != 200) {
                            $response = $response->getBody()->getContents();
                            $new_error = new VisionSoftError();
                            $new_error->api_id = 3;
                            $new_error->status_code = $status_code;
                            $new_error->error = $response;
                            $new_error->save();
                        } else {
                            $new_shipper = new VisionSoftCustomerBank();
                            $new_shipper->bank_id = $bank->id;
                            $new_shipper->save();
                        }
                    } catch (RequestException $e) {
                        $new_error = new VisionSoftError();
                        $new_error->api_id = 3;
                        $new_error->error = 'API Error';
                        $new_error->save();
                    }
                }
            }
        }
    }
    static public function city_hub(){
        $old_hubs = VisionSoftHub::pluck('hub_id')->toArray();
        $hubs = City::where('status', 1)->where('hub', 1)->whereNotIn('id', $old_hubs)->get();
        if (count($hubs) > 0) {
            $client = new Client(['base_uri' => 'http://traxapi.reactivelogix.com/api/TRAX/', 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
            foreach ($hubs as $hub) {
                try {
                    $response = $client->post('CityHub', [
                        'form_params' => [
                            'pin_code' => 6,
                            'pin_kp' => 'A',
                            'pin_loginid' => 'GB',
                            'pin_password' => 'SOFT',
                            'pin_hub_id' => $hub->id,
                            'pin_hub_name' => $hub->name
                        ]
                    ]);
                    $status_code = $response->getStatusCode();
                    if ($status_code != 200) {
                        $response = $response->getBody()->getContents();
                        $new_error = new VisionSoftError();
                        $new_error->api_id = 4;
                        $new_error->status_code = $status_code;
                        $new_error->error = $response;
                        $new_error->save();
                    } else {
                        $new_shipper = new VisionSoftHub();
                        $new_shipper->hub_id = $hub->id;
                        $new_shipper->save();
                    }
                } catch (RequestException $e) {
                    $new_error = new VisionSoftError();
                    $new_error->api_id = 4;
                    $new_error->error = 'API Error';
                    $new_error->save();
                }
            }
        }
    }
    static public function employees(){
        $old_employees = VisionSoftEmployee::pluck('employee_id')->toArray();
        $employees = Admin::where('status', 1)->whereNotIn('id', $old_employees)->get();
        if (count($employees) > 0) {
            $client = new Client(['base_uri' => 'http://traxapi.reactivelogix.com/api/TRAX/', 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
            foreach ($employees as $employee) {
                try {
                    $response = $client->post('Employes', [
                        'form_params' => [
                            'pin_code' => 6,
                            'pin_kp' => 'A',
                            'pin_loginid' => 'GB',
                            'pin_password' => 'SOFT',
                            'pin_emp_id' => $employee->id,
                            'pin_emp_name' => $employee->name
                        ]
                    ]);
                    $status_code = $response->getStatusCode();
                    if ($status_code != 200) {
                        $response = $response->getBody()->getContents();
                        $new_error = new VisionSoftError();
                        $new_error->api_id = 5;
                        $new_error->status_code = $status_code;
                        $new_error->error = $response;
                        $new_error->save();
                    } else {
                        $new_shipper = new VisionSoftEmployee();
                        $new_shipper->employee_id = $employee->id;
                        $new_shipper->save();
                    }
                } catch (RequestException $e) {
                    $new_error = new VisionSoftError();
                    $new_error->api_id = 5;
                    $new_error->error = 'API Error';
                    $new_error->save();
                }
            }
        }
    }
    static public function cities(){
        $old_cities = VisionSoftCity::pluck('city_id')->toArray();
        $cities = City::where('status', 1)->whereNotIn('id', $old_cities)->get();
        if (count($cities) > 0) {
            $client = new Client(['base_uri' => 'http://traxapi.reactivelogix.com/api/TRAX/', 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
            foreach ($cities as $city) {
                try {
                    $response = $client->post('Cities', [
                        'form_params' => [
                            'pin_code' => 6,
                            'pin_kp' => 'A',
                            'pin_loginid' => 'GB',
                            'pin_password' => 'SOFT',
                            'pin_city_id' => $city->id,
                            'pin_city_name' => $city->name,
                            'pin_hub_id' => $city->hub_city->id,
                            'pin_hub_name' => $city->hub_city->name
                        ]
                    ]);
                    $status_code = $response->getStatusCode();
                    if ($status_code != 200) {
                        $response = $response->getBody()->getContents();
                        $new_error = new VisionSoftError();
                        $new_error->api_id = 12;
                        $new_error->status_code = $status_code;
                        $new_error->error = $response;
                        $new_error->save();
                    } else {
                        $new_shipper = new VisionSoftCity();
                        $new_shipper->city_id = $city->id;
                        $new_shipper->save();
                    }
                } catch (RequestException $e) {
                    $new_error = new VisionSoftError();
                    $new_error->api_id = 12;
                    $new_error->error = 'API Error';
                    $new_error->save();
                }
            }
        }
    }
    static public function cod_payable(){

    }
    static public function cod_receivable(){

    }
}
