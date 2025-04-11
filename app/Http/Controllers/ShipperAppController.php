<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use Validator;
use App\Http\Models\Shipper\UserBankInfo;


class ShipperAppController extends Controller
{
    

    public function profile(Request $request) {

        $data = User::with(['products', 'city', 'account_type', 'segment', 'sub_segment', 'payment_cycle', 'reference'])->where('id' , $request->shipper_id)->first();
        $record['personal_information'] = [
            'name' => $data->name,
            'email' => $data->email,
            'address' => $data->address,
            'poc' => $data->poc,
            'phone' => $data->phone,
            'phone2' => $data->phone2,
            'cnic' => $data->cnic,
            'ntn_no' => $data->ntn_no,
            'strn_no' => $data->strn_no,
            'url' => $data->url,
            'product' => optional($data->products)->product_name,
            'city' => optional($data->city)->name ?? null,
            'account_type' => optional($data->account_type)->name ?? null,
            'segment' => optional($data->segment)->name ?? null,
            'sub_segment' => optional($data->sub_segment)->name ?? null,
            'payment_cycle' => optional($data->payment_cycle)->name ?? null,
            'reference' => optional($data->reference)->name ?? null,
        ];

        $record['shipping_information'] =  UserShippingInfo::join('cities as c', 'user_shipping_infos.city_id', '=', 'c.id')
        ->leftjoin('city_areas as ca', 'user_shipping_infos.city_area_id', '=', 'ca.id')
        ->leftjoin('user_shipping_info_store_addresses as usisa', 'user_shipping_infos.id', '=', 'usisa.user_shipping_infos_id')
        ->select(['user_shipping_infos.id as id','user_shipping_infos.pickup_brand_name as pickup_brand_name','user_shipping_infos.pickup_address as pickup_address','user_shipping_infos.poc as poc','user_shipping_infos.phone as phone','user_shipping_infos.email as email','user_shipping_infos.status as status','user_shipping_infos.default_address as default_address','c.name as city_name', 'user_shipping_infos.vendor','ca.name as city_area_name', 'user_shipping_infos.default_return_address'])
        ->where('user_shipping_infos.user_id', $request->shipper_id)
        ->where('hidden', 0)->get();

        $record['banks'] = UserBankInfo::leftJoin('cities as c','user_bank_infos.city_id','=','c.id')
        ->leftJoin('banks_lists as bl','bl.id','=','user_bank_infos.bank_name')
        ->select(['user_bank_infos.id as bank_row_id','user_bank_infos.bank_branch','user_bank_infos.account_no','user_bank_infos.account_title','user_bank_infos.iban','c.name as city','bl.name as bank_name','user_bank_infos.default_bank'])
        ->where('user_bank_infos.user_id', $request->shipper_id)->get();
        
        return response()->json(['status' => 0 , 'message' => 'Success' , 'data' => $record]);

    }
}
