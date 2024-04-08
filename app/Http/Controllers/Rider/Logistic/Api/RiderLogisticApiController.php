<?php

namespace App\Http\Controllers\Rider\Logistic\Api;

use App\Http\Controllers\Admins\Logistic\LogisticToShipmentSyncController;
use App\Http\Models\Admin\Logistic\TraxBookingPiece;
use App\Http\Models\Admin\Logistic\TraxChildCnIssueToRider;
use App\Http\Models\Admin\Logistic\TraxCnIssueToRider;
use App\Http\Models\Admin\Logistic\TraxItemInsurance;
use App\Http\Models\Admin\Logistic\TraxItemRefernce;
use App\Http\Models\Admin\Logistic\TraxLogisticBooking;
use App\Http\Models\Admin\Logistic\TraxParentProduct;
use App\Http\Models\Admin\Logistic\TraxProduct;
use App\Http\Models\Admin\Logistic\TraxService;
use App\Http\Models\Admin\Logistic\TraxShipperDetail;
use App\Http\Models\Admin\Logistic\TraxSpecialHandlingList;
use App\Http\Models\Admin\Logistic\TraxStation;
use App\Http\Models\Shipment;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use Carbon\Carbon;
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
            ->select('users.id as shipper_id','users.name as shipper_name','users.phone as shipper_phone','users.address','users.city_id','sd.trax_product_id','sd.trax_service_id','sd.piece_setting_id')
            ->where('users.status',3)->where('sd.status',1)
            ->where('sd.rider_id',$rider_id)
            ->get();

//        $pickup_address_list = TraxShipperDetail::join('route_locations as rl','rl.route_id','trax_shipper_details.route_id')
//            ->join('user_shipping_infos as usi','usi.id','rl.pickup_address_id')
//            ->select('rl.pickup_address_id','usi.pickup_address','usi.poc as contact_person','usi.phone as contact_number','usi.email as contact_email','trax_shipper_details.user_id as shipper_id')
//            ->where('usi.status',1)->where('trax_shipper_details.status',1)
//            ->where('trax_shipper_details.rider_id',$rider_id)->groupBy('rl.pickup_address_id')->get();

        $pickup_address_list = TraxShipperDetail::join('riders as r', 'r.id', '=', 'trax_shipper_details.rider_id')
            ->join('route_locations as rl', 'rl.route_id', '=', 'r.route_id')
            ->join('user_shipping_infos as usi', function ($join) {
                $join->on('usi.id', '=', 'rl.pickup_address_id')
                    ->where('trax_shipper_details.user_id', '=', DB::raw('usi.user_id'));
            })
            ->select('rl.pickup_address_id', 'usi.pickup_address', 'usi.poc as contact_person', 'usi.phone as contact_number', 'usi.email as contact_email', 'usi.user_id as shipper_id')
            ->where('usi.status', 1)
            ->where('trax_shipper_details.status', 1)
            ->where('trax_shipper_details.rider_id', $rider_id)
            ->get();


        $parent_products = TraxParentProduct::select('id','parent_code','parent_name')
            ->where('status',1)->get();

        $products =  TraxProduct::select('id','product_code','product_name','parent_id')
            ->where('status',1)->get();

        $services = TraxService::select('id','service_code','service_name','product_id')
            ->where('status',1)->get();

        $rider_cn = TraxCnIssueToRider::select('product_id','cn_from','cn_to','quantity')
            ->where('rider_id',$rider_id)->where('status',1)->get();

        $rider_child_cn = TraxChildCnIssueToRider::select('cn_from','cn_to','quantity')
            ->where('rider_id',$rider_id)->where('status',1)->get();

        $destination_list = TraxStation::select('id as destination_id','name as destination_name','station_code as destination_code')
            ->where('status',1)->get();

        $special_handling_list = TraxSpecialHandlingList::select('id as handling_id','description','rate','pay_mode')
            ->where('status',1)->get();



        $logistic_data = [
            'shipper_list'        =>   $shipper_list,
            'pickup_address_list' => $pickup_address_list,
            'parent_products'   =>   $parent_products,
            'products'          =>   $products,
            'services'          =>   $services,
            'rider_cn'          =>   $rider_cn,
            'rider_child_cn'     =>   $rider_child_cn,
            'destination_list'  =>   $destination_list,
            'special_handling_list' => $special_handling_list
        ];

        return response()->json(['status'=>0,'logistic_data'=> $logistic_data]);
    }

        public function logistic_booking_store(Request $request) {

            $bookig_data=$request->booking_data;
            $rider_id = $request->rider_id;

            try {

                if (isset($bookig_data))
                {
                    DB::beginTransaction();

                    foreach ($bookig_data as $booking)
                    {

                        $logistic_booking=TraxLogisticBooking::create([
                            'shipper_id' => $booking['shipper_id'],
                            'cn_number' => $booking['cn_number'],
                            'pickup_address_id'=>$booking['pickup_address_id'],
                            'booking_date' => $booking['booking_date'],
                            'product_id' => $booking['product_id'],
                            'service_id' => $booking['service_id'],
                            'destination_id' => $booking['destination_id'],
                            'shipper_reference' => $booking['shipper_reference'],
                            'consignee_name' => $booking['consignee_name'],
                            'consignee_phone_1' => $booking['consignee_phone_1'],
                            'total_pieces' => $booking['total_pieces'],
                            'total_dense_weight' => $booking['total_dense_weight'],
                            'total_volumetric_weight' => $booking['total_volumetric_weight'],
                            'user_type'=>2,
                            'created_by'=>$rider_id,
                        ]);

                        $shipment=Shipment::where('tracking_number');
                        if(!$shipment->exists())
                        {
                            //send data to shipments table
                            LogisticToShipmentSyncController::shipments_book($booking['shipper_id'],$booking['cn_number'],$booking['pickup_address_id'],1,1,$booking['destination_id'],$booking['consignee_name'],'Address',$booking['consignee_phone_1'],$booking['booking_date'],45,5323,0,1,1500,1,1,1,4,1,1,0.0,null,2);
                        }

                        if (isset($booking['booking_pieces_data']))
                        {
                            foreach ($booking['booking_pieces_data'] as $pieces_data) {
                                TraxBookingPiece::create([
                                    'booking_id'=>$logistic_booking->id,
                                    'from_pieces'=>$pieces_data['from_pieces'],
                                    'to_pieces'=>$pieces_data['to_pieces'],
                                    'quantity'=>$pieces_data['quantity'],
                                    'user_type'=>2,
                                    'created_by'=>$rider_id,
                                ]);
                            }
                        }
                        if (isset($booking['item_refernces_data']))
                        {
                            foreach ($booking['item_refernces_data'] as  $item_data) {

                                foreach ($item_data['item_detail'] as $detail){
                                    TraxItemRefernce::create([
                                        'booking_id' => $logistic_booking->id,
                                        'item_code' => $item_data['item_code'],
                                        'width' => $detail['width'],
                                        'height' => $detail['height'],
                                        'length' => $detail['length'],
                                        'weight' => $detail['weight'],
                                        'no_piece' => $detail['no_piece'],
                                        'user_type'=>2,
                                        'created_by'=>$rider_id,

                                    ]);
                                }
                            }
                        }
                        if (isset($booking['item_insurance_data']))
                        {
                            foreach ($booking['item_insurance_data'] as $item_insurance)
                            {
                                TraxItemInsurance::create([
                                    'booking_id' => $logistic_booking->id,
                                    'special_handling_id' => $item_insurance['special_handling_id'],
                                    'insurance' => $item_insurance['insurance'],
                                    'item_code' => $item_insurance['item_code'],
                                    'user_type'=>2,
                                    'created_by'=>$rider_id,
                                ]);
                            }
                        }
                    }
                    DB::commit();
                    return response()->json(['status'=>0,'success'=>'Booking Completed Successfully']);
                } else {
                    return response()->json(['status'=>1,'error'=>'Logistic Booking empty not add']);
                }

            } catch (\Exception $ex) {
                DB::rollback();
                return response()->json(['status'=>1,'error'=> $ex->getMeesage()]);
            }
        }

        public function get_shipper_detail(Request  $request)
        {
                $hub_id = $request->rider_hub;
                $shipper_id = $request->shipper_id;
                if(isset($request->shipper_id))
                {
                    $shipper = User::join('trax_shipper_details as sd','sd.user_id','=','users.id')
                        ->select('users.id as shipper_id','users.name as shipper_name','users.phone as shipper_phone','users.address','users.city_id','sd.trax_product_id','sd.trax_service_id')
                        ->where('users.status',3)->where('sd.status',1)
                        ->where('users.id', $shipper_id)
                        ->get();

                    $pickup_address_list = UserShippingInfo::select('id as pickup_address_id', 'pickup_address', 'poc as contact_person', 'phone as contact_number', 'email as contact_email', 'user_id as shipper_id')
                        ->where('user_id',$shipper_id)->where('city_id',$hub_id)->get();

                    $shipper_detail = [
                        'shipper'        =>   $shipper,
                        'pickup_address_list' => $pickup_address_list
                    ];
                    return response()->json(['status'=>0,'shipper_detail'=> $shipper_detail]);

                }
                return response()->json(['status'=>1,'error'=>'Shipper ID not found!']);

        }


//    public function logistic_booking_store(Request $request)
//    {
//
//        $bookig_data=$request->booking_data;
//        $rider_id = $request->rider_id;
//
//        try {
//            DB::beginTransaction();
//
//            $currentTimestamp = Carbon::now();
//
//            $logisticBookings = [];
//            $bookingPieces = [];
//            $itemReferences = [];
//            $itemInsurances = [];
//
//            if (isset($bookig_data))
//            {
//                foreach ($bookig_data as $booking) {
//                    $logisticBookings[] = [
//                        'shipper_id' => $booking['shipper_id'],
//                        'cn_number' => $booking['cn_number'],
//                        'booking_date' => $booking['booking_date'],
//                        'product_id' => $booking['product_id'],
//                        'service_id' => $booking['service_id'],
//                        'destination_id' => $booking['destination_id'],
//                        'shipper_reference' => $booking['shipper_reference'],
//                        'consignee_name' => $booking['consignee_name'],
//                        'total_pieces' => $booking['total_pieces'],
//                        'consignee_phone_1' => $booking['consignee_phone_1'],
//                        'total_dense_weight' => $booking['total_dense_weight'],
//                        'total_volumetric_weight' => $booking['total_volumetric_weight'],
//                        'user_type' => 2,
//                        'created_by' => $rider_id,
//                        'created_at' => $currentTimestamp,
//                        'updated_at' => $currentTimestamp,
//                    ];
//
//                    if (isset($booking['booking_pieces_data'])) {
//                        foreach ($booking['booking_pieces_data'] as $pieces_data) {
//                            $bookingPieces[] = [
//                                'from_pieces' => $pieces_data['from_pieces'],
//                                'to_pieces' => $pieces_data['to_pieces'],
//                                'quantity' => $pieces_data['quantity'],
//                                'user_type' => 2,
//                                'created_by' => $rider_id,
//                                'created_at' => $currentTimestamp,
//                                'updated_at' => $currentTimestamp,
//                            ];
//                        }
//                    }
//
//                    if (isset($booking['item_refernces_data'])) {
//                        foreach ($booking['item_refernces_data'] as $item_data) {
//                            foreach ($item_data['item_detail'] as $detail) {
//                                $itemReferences[] = [
//                                    'item_code' => $item_data['item_code'],
//                                    'width' => $detail['width'],
//                                    'height' => $detail['height'],
//                                    'length' => $detail['length'],
//                                    'weight' => $detail['weight'],
//                                    'no_piece' => $detail['no_piece'],
//                                    'user_type' => 2,
//                                    'created_by' => $rider_id,
//                                    'created_at' => $currentTimestamp,
//                                    'updated_at' => $currentTimestamp,
//                                ];
//                            }
//                        }
//                    }
//
//                    if (isset($booking['item_insurance_data'])) {
//                        foreach ($booking['item_insurance_data'] as $item_insurance) {
//                            $itemInsurances[] = [
//                                'special_handling_id' => $item_insurance['special_handling_id'],
//                                'insurance' => $item_insurance['insurance'],
//                                'item_code' => $item_insurance['item_code'],
//                                'user_type' => 2,
//                                'created_by' => $rider_id,
//                                'created_at' => $currentTimestamp,
//                                'updated_at' => $currentTimestamp,
//                            ];
//                        }
//                    }
//                }
//            }
//
//            TraxLogisticBooking::insert($logisticBookings);
//            TraxBookingPiece::insert($bookingPieces);
//            TraxItemRefernce::insert($itemReferences);
//            TraxItemInsurance::insert($itemInsurances);
//
//            DB::commit();
//            return response()->json(['status'=>0,'success'=>'Booking Completed Successfully']);
//
//        }catch (\Exception $ex) {
//            dd($ex->getMessage());
//            DB::rollback();
//            return response()->json(['status'=>1,'error'=>'Something went wrong!']);
//        }
//    }



}
