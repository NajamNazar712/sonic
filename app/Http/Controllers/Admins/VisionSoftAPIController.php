<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\PettyCashStatement;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\Admin\StationDepositNote;
use App\Http\Models\Admin\VisionSoft\VisionSoftArrivalRevenue;
use App\Http\Models\Admin\VisionSoft\VisionSoftBankDeposit;
use App\Http\Models\Admin\VisionSoft\VisionSoftCity;
use App\Http\Models\Admin\VisionSoft\VisionSoftCodPayable;
use App\Http\Models\Admin\VisionSoft\VisionSoftCodPayment;
use App\Http\Models\Admin\VisionSoft\VisionSoftCodPaymentClear;
use App\Http\Models\Admin\VisionSoft\VisionSoftCodReceivable;
use App\Http\Models\Admin\VisionSoft\VisionSoftCustomer;
use App\Http\Models\Admin\VisionSoft\VisionSoftCustomerBank;
use App\Http\Models\Admin\VisionSoft\VisionSoftDailyExp;
use App\Http\Models\Admin\VisionSoft\VisionSoftDellRetRevenue;
use App\Http\Models\Admin\VisionSoft\VisionSoftEmployee;
use App\Http\Models\Admin\VisionSoft\VisionSoftError;
use App\Http\Models\Admin\VisionSoft\VisionSoftHub;
use App\Http\Models\City;
use App\Http\Models\DonePaymentCalculation;
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
                    'pin_loginid' => 'aeiouyh',
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
                $sale_person = SalePersonTag::where('status', 0)->where('user_id', $shipper->id)->first();
                try {
                    $response = $client->post('Customers', [
                        'form_params' => [
                            'pin_code' => 6,
                            'pin_kp' => 'A',
                            'pin_loginid' => 'aeiouyh',
                            'pin_password' => 'meaumaur',
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
                                'pin_loginid' => 'aeiouyh',
                                'pin_password' => 'meaumaur',
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
                            'pin_loginid' => 'aeiouyh',
                            'pin_password' => 'meaumaur',
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
        $date = Carbon::yesterday();
        $today = Carbon::today();
        $shipments = Shipment::join('user_shipping_infos as usi', 'usi.id', '=', 'shipments.pickup_address_id')
            ->join('users as u', 'u.id', '=', 'shipments.user_id')
            ->join('shipments_journey as sj', function($join) use($date){
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.shipper_status_id', DB::raw(14))
                    ->whereDate('sj.created_at', $date)
                    ->where('sj.verification', DB::raw(1));
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
                                                'pin_loginid' => 'aeiouyh',
                                                'pin_password' => 'meaumaur',
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
        $date = Carbon::yesterday();
        $today = Carbon::today();
        $shippers = User::join('shipments as s', 's.user_id', '=', 'users.id')
            ->join('shipments_journey as sj', function($join) use($date){
                $join->on('sj.shipment_id', '=', 's.id')
                    ->where('sj.shipper_status_id', DB::raw(14))
                    ->whereDate('sj.created_at', $date)
                    ->where('sj.verification', DB::raw(1));
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
                            'pin_loginid' => 'aeiouyh',
                            'pin_password' => 'meaumaur',
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
                            'pin_loginid' => 'aeiouyh',
                            'pin_password' => 'meaumaur',
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
    //8
    static public function del_ret_revenue(){
        $date = Carbon::yesterday();
        $today = Carbon::today();
        $shipments = Shipment::join('user_shipping_infos as usi', 'usi.id', '=', 'shipments.pickup_address_id')
            ->join('users as u', 'u.id', '=', 'shipments.user_id')
            ->join('shipments_journey as sj', function($join) use($date){
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where(function ($sub_query) {
                        $sub_query->where('sj.shipper_status_id', DB::raw(14))
                            ->orWhere('sj.shipper_status_id', DB::raw(31));
                    })
                    ->whereDate('sj.created_at', $date)
                    ->where('sj.verification', DB::raw(1));
            })
            ->select('u.id as account_id', 'u.name as account_name', 'shipments.booking_type_id as service_type_id', 'usi.city_id as origin_city_id', 'shipments.try_and_buy_charges as try_and_buy_charges', 'shipments.nsa_osa_charges as nsa_osa_charges', 'shipments.replacement_charges as replacement_charges', 'shipments.cash_handling_charges as cash_handling_charges', 'shipments.gst as gst', 'shipments.return_charges as return_charges')
            ->get();
        VisionSoftDellRetRevenue::truncate();
        if(count($shipments) > 0){
            $user_shipments = array();
            foreach ($shipments as $shipment){
                if(array_key_exists($shipment->account_id, $user_shipments)){
                    if(array_key_exists($shipment->origin_city_id, $user_shipments[$shipment->account_id])){
                        if(array_key_exists($shipment->service_type_id, $user_shipments[$shipment->account_id][$shipment->origin_city_id])){
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['try_and_buy_charges'] += $shipment->try_and_buy_charges;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['nsa_osa_charges'] += $shipment->nsa_osa_charges;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['replacement_charges'] += $shipment->replacement_charges;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['cash_handling_charges'] += $shipment->cash_handling_charges;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['gst'] += $shipment->gst;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['return_charges'] += $shipment->return_charges;
                        }
                        else{
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['account_id'] = $shipment->account_id;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['account_name'] = $shipment->account_name;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['service_type_id'] = $shipment->service_type_id;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['origin_city_id'] = $shipment->origin_city_id;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['try_and_buy_charges'] = $shipment->try_and_buy_charges;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['nsa_osa_charges'] = $shipment->nsa_osa_charges;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['replacement_charges'] = $shipment->replacement_charges;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['cash_handling_charges'] = $shipment->cash_handling_charges;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['gst'] = $shipment->gst;
                            $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['return_charges'] = $shipment->return_charges;
                        }
                    }
                    else{
                        $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['account_id'] = $shipment->account_id;
                        $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['account_name'] = $shipment->account_name;
                        $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['service_type_id'] = $shipment->service_type_id;
                        $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['origin_city_id'] = $shipment->origin_city_id;
                        $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['try_and_buy_charges'] = $shipment->try_and_buy_charges;
                        $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['nsa_osa_charges'] = $shipment->nsa_osa_charges;
                        $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['replacement_charges'] = $shipment->replacement_charges;
                        $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['cash_handling_charges'] = $shipment->cash_handling_charges;
                        $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['gst'] = $shipment->gst;
                        $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['return_charges'] = $shipment->return_charges;
                    }
                }
                else{
                    $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['account_id'] = $shipment->account_id;
                    $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['account_name'] = $shipment->account_name;
                    $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['service_type_id'] = $shipment->service_type_id;
                    $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['origin_city_id'] = $shipment->origin_city_id;
                    $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['try_and_buy_charges'] = $shipment->try_and_buy_charges;
                    $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['nsa_osa_charges'] = $shipment->nsa_osa_charges;
                    $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['replacement_charges'] = $shipment->replacement_charges;
                    $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['cash_handling_charges'] = $shipment->cash_handling_charges;
                    $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['gst'] = $shipment->gst;
                    $user_shipments[$shipment->account_id][$shipment->origin_city_id][$shipment->service_type_id]['return_charges'] = $shipment->return_charges;
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
                                                'pin_loginid' => 'aeiouyh',
                                                'pin_password' => 'meaumaur',
                                                'pin_tr_date' => $today,
                                                'pin_account_id' => $shipment_charges['account_id'],
                                                'pin_origin_city' => $shipment_charges['origin_city_id'],
                                                'pin_service_type' => $shipment_charges['service_type_id'],
                                                'pin_try_buy_charges' => ($shipment_charges['try_and_buy_charges'] != null) ? $shipment_charges['try_and_buy_charges'] : 0,
                                                'pin_nsa_osa_charges' => ($shipment_charges['nsa_osa_charges'] != null) ? $shipment_charges['nsa_osa_charges'] : 0,
                                                'pin_replacement_charges' => ($shipment_charges['replacement_charges'] != null) ? $shipment_charges['replacement_charges'] : 0,
                                                'pin_cash_handling_charges' => ($shipment_charges['cash_handling_charges'] != null) ? $shipment_charges['cash_handling_charges'] : 0,
                                                'pin_srb_pra_bra_kpra' => ($shipment_charges['gst'] != null) ? $shipment_charges['gst'] : 0,
                                                'pin_return_charges' => ($shipment_charges['return_charges'] != null) ? $shipment_charges['return_charges'] : 0
                                            ]
                                        ]);
                                        $status_code = $response->getStatusCode();
                                        if ($status_code != 200) {
                                            $response = $response->getBody()->getContents();
                                            $new_error = new VisionSoftError();
                                            $new_error->api_id = 8;
                                            $new_error->status_code = $status_code;
                                            $new_error->error = $response;
                                            $new_error->save();
                                        } else {
                                            $new_charges = new VisionSoftDellRetRevenue();
                                            $new_charges->shipper_id = $shipment_charges['account_id'];
                                            $new_charges->origin_city_id = $shipment_charges['origin_city_id'];
                                            $new_charges->service_type_id = $shipment_charges['service_type_id'];
                                            $new_charges->try_and_buy_charges = ($shipment_charges['try_and_buy_charges'] != null) ? $shipment_charges['try_and_buy_charges'] : 0;
                                            $new_charges->nsa_osa_charges = ($shipment_charges['nsa_osa_charges'] != null) ? $shipment_charges['nsa_osa_charges'] : 0;
                                            $new_charges->replacement_charges = ($shipment_charges['replacement_charges'] != null) ? $shipment_charges['replacement_charges'] : 0;
                                            $new_charges->cash_handling_charges = ($shipment_charges['cash_handling_charges'] != null) ? $shipment_charges['cash_handling_charges'] : 0;
                                            $new_charges->gst = ($shipment_charges['gst'] != null) ? $shipment_charges['gst'] : 0;
                                            $new_charges->return_charges = ($shipment_charges['return_charges'] != null) ? $shipment_charges['return_charges'] : 0;
                                            $new_charges->save();
                                        }
                                    } catch (RequestException $e) {
                                        $new_error = new VisionSoftError();
                                        $new_error->api_id = 8;
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
    //10
    static public function cod_payment(){
        $date = Carbon::yesterday();
        $today = Carbon::today();
        $payments = DonePaymentCalculation::join('done_payments as dp', 'dp.id', '=', 'done_payment_calculations.done_payment_id')
            ->join('users as u', 'u.id', '=', 'dp.user_id')
            ->join('cities as c', 'c.id', '=', 'u.city_id')
            ->leftjoin('banks_lists as bl', 'bl.id', '=', 'dp.company_bank_id')
            ->select('dp.id as payment_id', 'u.id as account_id', 'c.name as city_name', 'done_payment_calculations.amount as amount', 'done_payment_calculations.charges as charges', 'done_payment_calculations.gst as gst', 'done_payment_calculations.payable as payable', 'bl.name as bank_name', 'dp.status as status')
            ->whereDate('done_payment_calculations.created_at', $date)
            ->get();
        if(count($payments) > 0){
            $client = new Client(['base_uri' => 'http://traxapi.reactivelogix.com/api/TRAX/', 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
            foreach ($payments as $payment){
                $gst = ($payment->gst) ? $payment->gst : 0;
                $charges = ($payment->charges) ? $payment->charges : 0;
                $deductable = $gst + $charges;
                if ($payment->status == 0) {
                    $status = 'Processed';
                }
                else if ($payment->status == 1) {
                    $status = 'Paid';
                }
                else if ($payment->status == 2) {
                    $status = 'Reverted';
                }
                else {
                    $status = 'Unknown';
                }
                try {
                    $response = $client->post('CodPayment', [
                        'form_params' => [
                            'pin_code' => 6,
                            'pin_kp' => 'A',
                            'pin_loginid' => 'aeiouyh',
                            'pin_password' => 'meaumaur',
                            'pin_tr_date' => $today,
                            'pin_payment_id' => $payment->payment_id,
                            'pin_account_id' => $payment->account_id,
                            'pin_cityname' => $payment->city_name,
                            'pin_tot_amount' => ($payment->amount) ? $payment->amount : 0,
                            'pin_tot_deductable' => $deductable,
                            'pin_tot_payable' => ($payment->payable) ? $payment->payable : 0,
                            'pin_company_bank' => $payment->bank_name,
                            'pin_status' => $status,
                        ]
                    ]);
                    $status_code = $response->getStatusCode();
                    if ($status_code != 200) {
                        $response = $response->getBody()->getContents();
                        $new_error = new VisionSoftError();
                        $new_error->api_id = 10;
                        $new_error->status_code = $status_code;
                        $new_error->error = $response;
                        $new_error->save();
                    } else {
                        $new_shipper = new VisionSoftCodPayment();
                        $new_shipper->payment_id = $payment->payment_id;
                        $new_shipper->shipper_id = $payment->account_id;
                        $new_shipper->city_name = $payment->city_name;
                        $new_shipper->amount = ($payment->amount) ? $payment->amount : 0;
                        $new_shipper->deductable = $deductable;
                        $new_shipper->payable = ($payment->payable) ? $payment->payable : 0;
                        $new_shipper->company_bank = $payment->bank_name;
                        $new_shipper->status = $status;
                        $new_shipper->save();
                    }
                } catch (RequestException $e) {
                    $new_error = new VisionSoftError();
                    $new_error->api_id = 10;
                    $new_error->error = 'API Error';
                    $new_error->save();
                }
            }
        }
    }
    //11
    static public function cod_payment_clear(){
        $date = Carbon::yesterday();
        $today = Carbon::today();
        $payments_clear = VisionSoftCodPaymentClear::whereDate('vision_soft_cod_payment_clears.created_at', $date)->get();
        if(count($payments_clear) > 0){
            $client = new Client(['base_uri' => 'http://traxapi.reactivelogix.com/api/TRAX/', 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
            foreach ($payments_clear as $payment_clear){
                if ($payment_clear->status == 0) {
                    $status = 'Processed';
                }
                else if ($payment_clear->status == 1) {
                    $status = 'Paid';
                }
                else if ($payment_clear->status == 2) {
                    $status = 'Reverted';
                }
                else {
                    $status = 'Unknown';
                }
                try {
                    $response = $client->post('CodPaymentClear', [
                        'form_params' => [
                            'pin_code' => 6,
                            'pin_kp' => 'A',
                            'pin_loginid' => 'aeiouyh',
                            'pin_password' => 'meaumaur',
                            'pin_tr_date' => $today,
                            'pin_payment_id' => $payment_clear->payment_id,
                            'pin_status' => $status,
                        ]
                    ]);
                    $status_code = $response->getStatusCode();
                    if ($status_code != 200) {
                        $response = $response->getBody()->getContents();
                        $new_error = new VisionSoftError();
                        $new_error->api_id = 11;
                        $new_error->status_code = $status_code;
                        $new_error->error = $response;
                        $new_error->save();
                    }
                    else{
                        $payment_clear->api_status = 1;
                        $payment_clear->save();
                    }
                } catch (RequestException $e) {
                    $new_error = new VisionSoftError();
                    $new_error->api_id = 11;
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
                            'pin_loginid' => 'aeiouyh',
                            'pin_password' => 'meaumaur',
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
    //13
    static public function bank_deposits(){
        $date = Carbon::yesterday();
        $today = Carbon::today();
        $station_deposit_notes = StationDepositNote::whereDate('created_at', $date)->where('status', '=', 2);
        if($station_deposit_notes->exists()){
            $client = new Client(['base_uri' => 'http://traxapi.reactivelogix.com/api/TRAX/', 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
            $station_deposit_notes = $station_deposit_notes->get();
            foreach ($station_deposit_notes as $station_deposit_note){
                if($station_deposit_note->banks_list_id != null){
                    try{
                        $response = $client->post('BankDeposit', [
                            'form_params' => [
                                'pin_code' => 6,
                                'pin_kp' => 'A',
                                'pin_loginid' => 'aeiouyh',
                                'pin_password' => 'meaumaur',
                                'pin_tr_date' => $today,
                                'pin_hub_id' => $station_deposit_note->hub_id,
                                'pin_sdn_number' => $station_deposit_note->id,
                                'pin_bank' => $station_deposit_note->banks_list_id,
                                'pin_amount' => $station_deposit_note->sdn_amount,
                                'pin_adj_amount' => $station_deposit_note->adjustment_amount,
                                'pin_adj_stmt_head_id' => $station_deposit_note->petty_cash_statement_id
                            ]
                        ]);
                        $status_code = $response->getStatusCode();
                        if ($status_code != 200) {
                            $response = $response->getBody()->getContents();
                            $new_error = new VisionSoftError();
                            $new_error->api_id = 13;
                            $new_error->status_code = $status_code;
                            $new_error->error = $response;
                            $new_error->save();
                        } else {
                            $bank_deposit = new VisionSoftBankDeposit();
                            $bank_deposit->sdn_id = $station_deposit_note->id;
                            $bank_deposit->bank_id = $station_deposit_note->banks_list_id;
                            $bank_deposit->amount = $station_deposit_note->sdn_amount;
                            $bank_deposit->save();
                        }
                    } catch (RequestException $e) {
                        $new_error = new VisionSoftError();
                        $new_error->api_id = 13;
                        $new_error->error = 'API Error';
                        $new_error->save();
                    }
                }else{
                    $station_deposit_note_slips = $station_deposit_note->deposit_note_slips;
                    foreach ($station_deposit_note_slips as $slip){
                        try{
                            $response = $client->post('BankDeposit', [
                                'form_params' => [
                                    'pin_code' => 6,
                                    'pin_kp' => 'A',
                                    'pin_loginid' => 'aeiouyh',
                                    'pin_password' => 'meaumaur',
                                    'pin_tr_date' => $today,
                                    'pin_hub_id' => $station_deposit_note->hub_id,
                                    'pin_sdn_number' => $station_deposit_note->id,
                                    'pin_bank' => $slip->bank_id,
                                    'pin_amount' => $slip->amount,
                                    'pin_adj_amount' => 0,
                                    'pin_adj_stmt_head_id' => 0
                                ]
                            ]);
                            $status_code = $response->getStatusCode();
                            if ($status_code != 200) {
                                $response = $response->getBody()->getContents();
                                $new_error = new VisionSoftError();
                                $new_error->api_id = 13;
                                $new_error->status_code = $status_code;
                                $new_error->error = $response;
                                $new_error->save();
                            } else {
                                $bank_deposit = new VisionSoftBankDeposit();
                                $bank_deposit->sdn_id = $station_deposit_note->id;
                                $bank_deposit->bank_id = $station_deposit_note->banks_list_id;
                                $bank_deposit->amount = $station_deposit_note->sdn_amount;
                                $bank_deposit->save();
                            }
                        } catch (RequestException $e) {
                            $new_error = new VisionSoftError();
                            $new_error->api_id = 13;
                            $new_error->error = 'API Error';
                            $new_error->save();
                        }
                    }

                    try{
                        $response = $client->post('BankDeposit', [
                            'form_params' => [
                                'pin_code' => 6,
                                'pin_kp' => 'A',
                                'pin_loginid' => 'aeiouyh',
                                'pin_password' => 'meaumaur',
                                'pin_tr_date' => $today,
                                'pin_hub_id' => $station_deposit_note->hub_id,
                                'pin_sdn_number' => $station_deposit_note->id,
                                'pin_bank' => 0,
                                'pin_amount' => 0,
                                'pin_adj_amount' => $station_deposit_note->adjustment_amount,
                                'pin_adj_stmt_head_id' => $station_deposit_note->petty_cash_statement_id
                            ]
                        ]);
                        $status_code = $response->getStatusCode();
                        if ($status_code != 200) {
                            $response = $response->getBody()->getContents();
                            $new_error = new VisionSoftError();
                            $new_error->api_id = 13;
                            $new_error->status_code = $status_code;
                            $new_error->error = $response;
                            $new_error->save();
                        } else {
                            $bank_deposit = new VisionSoftBankDeposit();
                            $bank_deposit->sdn_id = $station_deposit_note->id;
                            $bank_deposit->petty_cash_id = $station_deposit_note->petty_cash_statement_id;
                            $bank_deposit->save();
                        }
                    } catch (RequestException $e) {
                        $new_error = new VisionSoftError();
                        $new_error->api_id = 13;
                        $new_error->error = 'API Error';
                        $new_error->save();
                    }

                }
            }
        }


    }
    //14
    static public function daily_exp(){
        $date = Carbon::today();
        $today = Carbon::today();
        $vision_daily_exp_ids = VisionSoftDailyExp::groupBy('petty_cash_statement_id')->pluck('petty_cash_statement_id')->toArray();
        $petty_cash_statements = PettyCashStatement::where('status','>=', 3)->whereDate('created_at', $date)->whereNotIn('id',$vision_daily_exp_ids);
        if($petty_cash_statements->exists()){
            $client = new Client(['base_uri' => 'http://traxapi.reactivelogix.com/api/TRAX/', 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
            $petty_cash_statements = $petty_cash_statements->get();
            if(count($petty_cash_statements) > 0){
                foreach ($petty_cash_statements as $petty_cash_statement){
                    $petty_cash_statement_details = $petty_cash_statement->petty_cash_statement_details()->where('status', 1);
                    if($petty_cash_statement_details){
                        foreach ($petty_cash_statement_details as $petty_cash_statement_detail) {
                            try{
                                $response = $client->post('DailyExp', [
                                    'form_params' => [
                                        'pin_code' => 6,
                                        'pin_kp' => 'A',
                                        'pin_loginid' => 'aeiouyh',
                                        'pin_password' => 'meaumaur',
                                        'pin_tr_date' => $today,
                                        'pin_ref_stmt_no' => $petty_cash_statement->reference_no,
                                        'pin_amount' => $petty_cash_statement_detail->amount,
                                        'pin_hub_id' => $petty_cash_statement_detail->hub_id,
                                        'pin_account_head' => $petty_cash_statement_detail->heads->name,
                                        'pin_account_title' => $petty_cash_statement_detail->titles->name,
                                        'pin_details' => $petty_cash_statement_detail->expense_details,
                                        'pin_stmt_id' => $petty_cash_statement_detail->id,
                                        'pin_stmt_ref_no' => $petty_cash_statement_detail->reference_no,
                                        'pin_tracking_number' => $petty_cash_statement->shipment->tracking_number,
                                        'pin_creation_date' => $petty_cash_statement->created_at,
                                        'pin_period_from_date' => $petty_cash_statement->from,
                                        'pin_period_to_date' => $petty_cash_statement->to,
                                        'pin_statement_head_id' => $petty_cash_statement->id
                                    ]
                                ]);
                                $status_code = $response->getStatusCode();
                                if ($status_code != 200) {
                                    $response = $response->getBody()->getContents();
                                    $new_error = new VisionSoftError();
                                    $new_error->api_id = 14;
                                    $new_error->status_code = $status_code;
                                    $new_error->error = $response;
                                    $new_error->save();
                                } else {
                                    $daily_exp = new VisionSoftDailyExp();
                                    $daily_exp->petty_cash_statement_id = $petty_cash_statement->id;
                                    $daily_exp->statement_id = $petty_cash_statement_detail->id;
                                    $daily_exp->save();
                                }
                            } catch (RequestException $e) {
                                $new_error = new VisionSoftError();
                                $new_error->api_id = 14;
                                $new_error->error = 'API Error';
                                $new_error->save();
                            }
                        }
                    }
                }
            }
        }

    }
    //15
    static public function cod_receivable(){
        $date = Carbon::yesterday();
        $today = Carbon::today();
        $cities = City::join('shipments as s', 's.consignee_city_id', '=', 'cities.id')
            ->join('cities as hc', 'hc.id', '=', 'cities.hub_id')
            ->join('shipments_journey as sj', function($join) use($date){
                $join->on('sj.shipment_id', '=', 's.id')
                    ->where('sj.shipper_status_id', DB::raw(14))
                    ->whereDate('sj.created_at', $date)
                    ->where('sj.verification', DB::raw(1));
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
                            'pin_loginid' => 'aeiouyh',
                            'pin_password' => 'meaumaur',
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
    //9
    static public function prc_load_api_data(){
        try{
            $client = new Client(['base_uri' => 'http://traxapi.reactivelogix.com/api/TRAX/', 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
            $response = $client->post('PRCLoadApiData', [
                'form_params' => [
                    'pin_c' => 6,
                    'pin_k' => 'A',
                    'pin_loginid' => 'aeiouyh',
                    'pin_password' => 'meaumaur'
                ]
            ]);
            $status_code = $response->getStatusCode();
            if ($status_code != 200) {
                $response = $response->getBody()->getContents();
                $new_error = new VisionSoftError();
                $new_error->api_id = 9;
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
            $new_error->api_id = 9;
            $new_error->error = 'API Error';
            $new_error->save();
        }
    }
}
