<?php

namespace App\Http\Controllers\Rider\Logistic\Api;

use App\Http\Models\Admin\Logistic\TraxCnIssueToRider;
use App\Http\Models\Admin\Logistic\TraxParentProduct;
use App\Http\Models\Admin\Logistic\TraxProduct;
use App\Http\Models\Admin\Logistic\TraxService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RiderLogisticApiController extends Controller
{
    public  function logistic_data(Request $request)
    {

        $rider_id = $request->rider_id;
        $logistic_data = array();
        $parent_products = TraxParentProduct::select('id','parent_code','parent_name')
            ->where('status',1)->get();
        $products =  TraxProduct::select('id','product_code','product_name','parent_id')
            ->where('status',1)->get();
        $services = TraxService::select('id','service_code','service_name','product_id')
            ->where('status',1)->get();
        $rider_cn = TraxCnIssueToRider::select('product_id','cn_from','cn_to','quantity')
            ->where('rider_id',$rider_id)->where('status',1)->get();

        $logistic_data = [
            'parent_products' =>   $parent_products,
            'products'        =>   $products,
            'services'        =>   $services,
            'rider_cn'        =>   $rider_cn
        ];

        return response()->json(['status'=>1,'logistic_data'=> $logistic_data]);
    }
}
