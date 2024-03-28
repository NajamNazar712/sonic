<?php

namespace App\Http\Controllers\Rider\Logistic\Api;

use App\Http\Models\Admin\Logistic\TraxCnIssueToRider;
use App\Http\Models\Admin\Logistic\TraxParentProduct;
use App\Http\Models\Admin\Logistic\TraxProduct;
use App\Http\Models\Admin\Logistic\TraxService;
use App\Http\Models\Admin\Logistic\TraxStation;
use App\Http\Models\Shipper\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RiderLogisticApiController extends Controller
{
    public  function logistic_data(Request $request)
    {

        $rider_id = $request->rider_id;
        $logistic_data = array();

        $shipper_list = User::join('trax_shipper_details as sd','sd.user_id','=','users.id')
            ->select('users.id as shipper_id','users.name as shipper_name','users.phone as shipper_phone','users.address','users.city_id','sd.trax_product_id','sd.trax_service_id')
            ->where('users.status',3)->where('sd.status',1)->get();

        $parent_products = TraxParentProduct::select('id','parent_code','parent_name')
            ->where('status',1)->get();

        $products =  TraxProduct::select('id','product_code','product_name','parent_id')
            ->where('status',1)->get();

        $services = TraxService::select('id','service_code','service_name','product_id')
            ->where('status',1)->get();

        $rider_cn = TraxCnIssueToRider::select('product_id','cn_from','cn_to','quantity')
            ->where('rider_id',$rider_id)->where('status',1)->get();

        $destination_list = TraxStation::select('id as destination_id','name as destination_name','station_code as destination_code')
            ->where('status',1)->get();



        $logistic_data = [
            'shipper_list'      =>   $shipper_list,
            'parent_products'   =>   $parent_products,
            'products'          =>   $products,
            'services'          =>   $services,
            'rider_cn'          =>   $rider_cn,
            'destination_list'  =>   $destination_list
        ];

        return response()->json(['status'=>1,'logistic_data'=> $logistic_data]);
    }
}
