<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\Admin\VisionSoft\VisionSoftArrivalRevenue;
use App\Http\Models\Admin\VisionSoft\VisionSoftCity;
use App\Http\Models\Admin\VisionSoft\VisionSoftCodPayable;
use App\Http\Models\Admin\VisionSoft\VisionSoftCodReceivable;
use App\Http\Models\Admin\VisionSoft\VisionSoftCustomer;
use App\Http\Models\Admin\VisionSoft\VisionSoftCustomerBank;
use App\Http\Models\Admin\VisionSoft\VisionSoftEmployee;
use App\Http\Models\Admin\VisionSoft\VisionSoftError;
use App\Http\Models\Admin\VisionSoft\VisionSoftHub;
use App\Http\Models\City;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserBankInfo;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class VisionSoftAPIController extends Controller
{
    //1
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
    //2
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
    //3
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
    //4
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
    //5
    static public function arrival_revenue(){
        $start_day = Carbon::tomorrow()->startOfDay();
        $end_day = Carbon::tomorrow()->endOfDay();
        $today = Carbon::today();
        $shipments = Shipment::join('user_shipping_infos as usi', 'usi.id', '=', 'shipments.pickup_address_id')
            ->join('users as u', 'u.id', '=', 'shipments.user_id')
            ->join('shipments_journey as sj', function($join) use($start_day, $end_day){
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.shipper_status_id', DB::raw(14))
                    ->where('sj.created_at', '>=', $start_day)
                    ->where('sj.created_at', '<=', $end_day);
            })
            ->select('u.id as account_id', 'u.name as account_name', 'shipments.booking_type_id as service_type_id', 'usi.city_id as origin_city_id', 'shipments.weight_charges as weight_charges', 'shipments.insurance_charges as insurance_charges', 'shipments.fuel_surcharge as fuel_surcharge', 'shipments.packaging_charges as packing_charges', 'shipments.packaging_material_charges as packaging_charges')
            ->get();
        if(count($shipments) > 0){
            VisionSoftArrivalRevenue::truncate();
            $user_shipments = array();
            foreach ($shipments as $shipment){
                if(array_key_exists($shipment->account_id, $user_shipments)){
                    if(array_key_exists($shipment->origin_city_id, $user_shipments[$shipment->account_id])){
                        if(array_key_exists($shipment->service_type_id, $user_shipments[$shipment->account_id][$shipment->origin_city_id])){
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['weight_charges'] += $shipment->weight_charges;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['insurance_charges'] += $shipment->insurance_charges;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['fuel_surcharge'] += $shipment->fuel_surcharge;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['packing_charges'] += $shipment->packing_charges;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['packaging_charges'] += $shipment->packaging_charges;
                        }
                        else{
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['account_id'] = $shipment->account_id;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['account_name'] = $shipment->account_name;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['service_type_id'] = $shipment->service_type_id;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['origin_city_id'] = $shipment->origin_city_id;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['weight_charges'] = $shipment->weight_charges;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['insurance_charges'] = $shipment->insurance_charges;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['fuel_surcharge'] = $shipment->fuel_surcharge;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['packing_charges'] = $shipment->packing_charges;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['packaging_charges'] = $shipment->packaging_charges;
                        }
                    }
                    else{
                        $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['account_id'] = $shipment->account_id;
                        $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['account_name'] = $shipment->account_name;
                        $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['service_type_id'] = $shipment->service_type_id;
                        $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['origin_city_id'] = $shipment->origin_city_id;
                        $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['weight_charges'] = $shipment->weight_charges;
                        $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['insurance_charges'] = $shipment->insurance_charges;
                        $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['fuel_surcharge'] = $shipment->fuel_surcharge;
                        $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['packing_charges'] = $shipment->packing_charges;
                        $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['packaging_charges'] = $shipment->packaging_charges;
                    }
                }
                else{
                    $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['account_id'] = $shipment->account_id;
                    $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['account_name'] = $shipment->account_name;
                    $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['service_type_id'] = $shipment->service_type_id;
                    $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['origin_city_id'] = $shipment->origin_city_id;
                    $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['weight_charges'] = $shipment->weight_charges;
                    $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['insurance_charges'] = $shipment->insurance_charges;
                    $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['fuel_surcharge'] = $shipment->fuel_surcharge;
                    $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['packing_charges'] = $shipment->packing_charges;
                    $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['packaging_charges'] = $shipment->packaging_charges;
                }
            }
            if(count($user_shipments) > 0){
                $client = new Client(['base_uri' => 'http://traxapi.reactivelogix.com/api/TRAX/', 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
                foreach ($user_shipments as $user_shipment_origin){
                    if(count($user_shipment_origin) > 0){
                        foreach ($user_shipment_origin as $user_shipment_service){
                            if(count($user_shipment_service) > 0){
                                foreach ($user_shipment_service as $shipment_charges){
                                    try {
                                        $response = $client->post('ArrivalRevenue', [
                                            'form_params' => [
                                                'pin_code' => 6,
                                                'pin_kp' => 'A',
                                                'pin_loginid' => 'GB',
                                                'pin_password' => 'SOFT',
                                                'pin_tr_date' => $today,
                                                'pin_account_id' => $shipment_charges['account_id'],
                                                'pin_origin_city' => $shipment_charges['origin_city_id'],
                                                'pin_service_type' => $shipment_charges['service_type_id'],
                                                'pin_weight_charges' => ($shipment_charges['weight_charges'] != null) ? $shipment_charges['weight_charges'] : 0,
                                                'pin_insurance_charges' => ($shipment_charges['insurance_charges'] != null) ? $shipment_charges['insurance_charges'] : 0,
                                                'pin_packing_charges' => ($shipment_charges['packing_charges'] != null) ? $shipment_charges['packing_charges'] : 0,
                                                'pin_fuel_surcharge' => ($shipment_charges['fuel_surcharge'] != null) ? $shipment_charges['fuel_surcharge'] : 0,
                                                'pin_packaging_charges' => ($shipment_charges['packaging_charges'] != null) ? $shipment_charges['packaging_charges'] : 0
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
                                            $new_charges = new VisionSoftArrivalRevenue();
                                            $new_charges->shipper_id = $shipment_charges['account_id'];
                                            $new_charges->origin_city_id = $shipment_charges['origin_city_id'];
                                            $new_charges->service_type_id = $shipment_charges['service_type_id'];
                                            $new_charges->weight_charges = ($shipment_charges['weight_charges'] != null) ? $shipment_charges['weight_charges'] : 0;
                                            $new_charges->insurance_charges = ($shipment_charges['insurance_charges'] != null) ? $shipment_charges['insurance_charges'] : 0;
                                            $new_charges->packing_charges = ($shipment_charges['packing_charges'] != null) ? $shipment_charges['packing_charges'] : 0;
                                            $new_charges->fuel_surcharge = ($shipment_charges['fuel_surcharge'] != null) ? $shipment_charges['fuel_surcharge'] : 0;
                                            $new_charges->packaging_charges = ($shipment_charges['packaging_charges'] != null) ? $shipment_charges['packaging_charges'] : 0;
                                            $new_charges->save();
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
                    }
                }
            }
        }
    }
    //6
    static public function cod_payable(){
        $start_day = Carbon::tomorrow()->startOfDay();
        $end_day = Carbon::tomorrow()->endOfDay();
        $today = Carbon::today();
        $shippers = User::join('shipments as s', 's.user_id', '=', 'users.id')
            ->join('shipments_journey as sj', function($join) use($start_day, $end_day){
                $join->on('sj.shipment_id', '=', 's.id')
                    ->where('sj.shipper_status_id', DB::raw(14))
                    ->where('sj.created_at', '>=', $start_day)
                    ->where('sj.created_at', '<=', $end_day);
            })
            ->select('users.id as account_id', 'users.name as account_name', DB::raw('(select sum(s.amount)) as amount'))
            ->groupBy('users.id')
            ->get();
        VisionSoftCodPayable::truncate();
        if(count($shippers) > 0){
            $client = new Client(['base_uri' => 'http://traxapi.reactivelogix.com/api/TRAX/', 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
            foreach ($shippers as $shipper){
                try {
                    $response = $client->post('CodPayable', [
                        'form_params' => [
                            'pin_code' => 6,
                            'pin_kp' => 'A',
                            'pin_loginid' => 'GB',
                            'pin_password' => 'SOFT',
                            'pin_tr_date' => $today,
                            'pin_account_id' => $shipper->account_id,
                            'pin_account_name' => $shipper->account_name,
                            'pin_hub_name' => $shipper->amount
                        ]
                    ]);
                    $status_code = $response->getStatusCode();
                    if ($status_code != 200) {
                        $response = $response->getBody()->getContents();
                        $new_error = new VisionSoftError();
                        $new_error->api_id = 6;
                        $new_error->status_code = $status_code;
                        $new_error->error = $response;
                        $new_error->save();
                    } else {
                        $new_shipper = new VisionSoftCodPayable();
                        $new_shipper->shipper_id = $shipper->account_id;
                        $new_shipper->amount = $shipper->amount;
                        $new_shipper->save();
                    }
                } catch (RequestException $e) {
                    $new_error = new VisionSoftError();
                    $new_error->api_id = 6;
                    $new_error->error = 'API Error';
                    $new_error->save();
                }
            }
        }
    }
    //7
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
                        $new_error->api_id = 7;
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
                    $new_error->api_id = 7;
                    $new_error->error = 'API Error';
                    $new_error->save();
                }
            }
        }
    }
    //12
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
    //15
    static public function cod_receivable(){
        $start_day = Carbon::tomorrow()->startOfDay();
        $end_day = Carbon::tomorrow()->endOfDay();
        $today = Carbon::today();
        $cities = City::join('shipments as s', 's.consignee_city_id', '=', 'cities.id')
            ->join('cities as hc', 'hc.id', '=', 'cities.hub_id')
            ->join('shipments_journey as sj', function($join) use($start_day, $end_day){
                $join->on('sj.shipment_id', '=', 's.id')
                    ->where('sj.shipper_status_id', DB::raw(14))
                    ->where('sj.created_at', '>=', $start_day)
                    ->where('sj.created_at', '<=', $end_day);
            })
            ->select('hc.id as hub_id', DB::raw('(select sum(s.amount)) as amount'))
            ->groupBy('hc.id')
            ->get();
        VisionSoftCodReceivable::truncate();
        if(count($cities) > 0){
            $client = new Client(['base_uri' => 'http://traxapi.reactivelogix.com/api/TRAX/', 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
            foreach ($cities as $city){
                try {
                    $response = $client->post('CodReceivable', [
                        'form_params' => [
                            'pin_code' => 6,
                            'pin_kp' => 'A',
                            'pin_loginid' => 'GB',
                            'pin_password' => 'SOFT',
                            'pin_tr_date' => $today,
                            'pin_hub_id' => $city->hub_id,
                            'pin_hub_name' => $city->amount
                        ]
                    ]);
                    $status_code = $response->getStatusCode();
                    if ($status_code != 200) {
                        $response = $response->getBody()->getContents();
                        $new_error = new VisionSoftError();
                        $new_error->api_id = 15;
                        $new_error->status_code = $status_code;
                        $new_error->error = $response;
                        $new_error->save();
                    } else {
                        $new_city = new VisionSoftCodReceivable();
                        $new_city->hub_id = $city->hub_id;
                        $new_city->amount = $city->amount;
                        $new_city->save();
                    }
                } catch (RequestException $e) {
                    $new_error = new VisionSoftError();
                    $new_error->api_id = 15;
                    $new_error->error = 'API Error';
                    $new_error->save();
                }
            }
        }
    }

    
}
