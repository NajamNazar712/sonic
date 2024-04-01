<?php

namespace App\Http\Controllers\Rider\Logistic\Api;

use App\Http\Models\Admin\Logistic\TraxBookingPiece;
use App\Http\Models\Admin\Logistic\TraxCnIssueToRider;
use App\Http\Models\Admin\Logistic\TraxItemRefernce;
use App\Http\Models\Admin\Logistic\TraxLogisticBooking;
use App\Http\Models\Admin\Logistic\TraxParentProduct;
use App\Http\Models\Admin\Logistic\TraxProduct;
use App\Http\Models\Admin\Logistic\TraxService;
use App\Http\Models\Admin\Logistic\TraxStation;
use App\Http\Models\Shipper\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RiderLogisticApiController extends Controller
{
    public function logistic_data(Request $request)
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



    public function logistic_booking_store(Request $request)
    {

        $bookig_data=$request->booking_data;
        $rider_id = $request->rider_id;

        try {
            DB::beginTransaction();

            if (isset($bookig_data))
            {
                foreach ($bookig_data as $booking)
                {

                    $logistic_booking=TraxLogisticBooking::create([
                        'shipper_id' => $booking['shipper_id'],
                        'cn_number' => $booking['cn_number'],
                        'product_id' => $booking['product_id'],
                        'service_id' => $booking['service_id'],
                        'destination_id' => $booking['destination_id'],
                        'shipper_reference' => $booking['shipper_reference'],
                        'consignee_name' => $booking['consignee_name'],
                        'total_pieces' => $booking['total_pieces'],
                        'consignee_phone_1' => $booking['consignee_phone_1'],
                        'total_dense_weight' => $booking['total_dense_weight'],
                        'total_volumetric_weight' => $booking['total_volumetric_weight']
                    ]);
                    if (isset($booking['booking_pieces_data']))
                    {
                        foreach ($booking['booking_pieces_data'] as $pieces_data) {
                            TraxBookingPiece::create([
                                'booking_id'=>$logistic_booking->id,
                                'from_pieces'=>$pieces_data['from_pieces'],
                                'to_pieces'=>$pieces_data['to_pieces'],
                                'quantity'=>$pieces_data['quantity']
                            ]);
                        }
                    }
                    if (isset($booking['item_refernces_data']))
                    {
                        foreach ($booking['item_refernces_data'] as  $item_data) {
                            TraxItemRefernce::create([
                                'booking_id' => $logistic_booking->id,
                                'item_code' => $item_data['item_code'],
                                'width' => $item_data['width'],
                                'height' => $item_data['height'],
                                'length' => $item_data['length'],
                                'weight' => $item_data['weight'],
                                'no_piece' => $item_data['no_piece'],
                                'created_by'=>$rider_id,
                                'updated_by'=>$rider_id,
                                'user_type'=>2
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return response()->json(['success'=>'Booking Completed Successfully']);

        }catch (\Exception $ex) {
            DB::rollback();
            dd($ex->getMessage());
            return response()->json(['error'=>'Something went wrong!']);
        }
    }
}
