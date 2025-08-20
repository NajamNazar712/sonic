<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\BookingType;
use App\Http\Models\City;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\DeliveryType;
use App\Http\Models\PaymentMode;
use App\Http\Models\Product;
use App\Http\Models\ShippingMode;
use App\Http\Requests\ValidateShipmentIdRequest;
use App\RvAgentCallHistory;
use Illuminate\Http\Request;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\Shipper\UserBankInfo;
use Illuminate\Support\Facades\Validator;


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

    public function booking_resources(Request $request)
    {
        $shipping_modes = ShippingMode::all();
        $product_types = Product::all();
        $service_types = BookingType::where('id',1)->get();
        $delivery_types = DeliveryType::all();
        $payment_modes = PaymentMode::whereIn('id',[1,4])->get();
        $cities = City::select('id','name','hub_id')
            ->where('status',1)
            ->get();


        $booking_resoureces = [
            'shipping_mode' => $shipping_modes,
            'product_types' => $product_types,
            'service_types' => $service_types,
            'delivery_types' => $delivery_types,
            'payment_modes' =>$payment_modes,
            'cities' => $cities,


        ];
        return response()->json(['status' => 0 , 'message' => 'Success' , 'booking_resoureces' => $booking_resoureces]);

    }

    public function pickup_address(Request $request)
    {
        $shipper_id = $request->shipper_id;

        $pickup_addresses = UserShippingInfo::where('status',1)
            ->where('user_id',$shipper_id)->get();

        $return_addresses = null;

        $global_setting = GlobalSettings::where('type','omni_users')->first();
        if($global_setting) {
            $omni_user = explode(',', $global_setting->text);
           if(in_array($shipper_id,$omni_user)) {
               $return_addresses = UserShippingInfo::where('status',1)
                   ->where('user_id',$request->shipper_id)->get();
           }
        }

        return response()->json(['status' => 0 , 'message' => 'Success' , 'pickup_addresses' => $pickup_addresses,'return_addresses' => $return_addresses]);

    }

    public function shipment_call_status_history(ValidateShipmentIdRequest $request)
    {
        $mergedArray = [];
        $data = RvAgentCallHistory::with(['rv_call_finding' => function ($query) {
            $query->select('id', 'name');
        }, 'shipment.status_shipper' => function ($query) {
            $query->select('id', 'name');
        },  'updated_by'])->where('shipment_id', $request->shipment_id)->orderby('updated_at', 'desc')->get();

        if($data->isNotEmpty()){
            foreach ($data as $item) {
                $mergedArray[] = [
                    'data' => $item,
                    'user_name' => $item->updated_by->name ?? '-',
                ];
            }
            return response()->json([
                'status' => 0,
                'call_history' => $mergedArray,
            ]);
        }
        else{
            return response()->json([
                'status' => 1,
                'error' => 'Shipment call history does not exists',
            ]);
        }

    }

}
