<?php

namespace App\Http\Controllers\Rider\Logistic\Api;

use App\Http\Controllers\Admins\Logistic\AdminBatchController;
use App\Http\Controllers\Admins\Logistic\LogisticToShipmentSyncController;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Logistic\TraxBookingBatch;
use App\Http\Models\Admin\Logistic\TraxBookingPiece;
use App\Http\Models\Admin\Logistic\TraxChildCnIssueToRider;
use App\Http\Models\Admin\Logistic\TraxCnIssueToRider;
use App\Http\Models\Admin\Logistic\TraxItemInsurance;
use App\Http\Models\Admin\Logistic\TraxItemRefernce;
use App\Http\Models\Admin\Logistic\TraxLogisticBooking;
use App\Http\Models\Admin\Logistic\TraxParentProduct;
use App\Http\Models\Admin\Logistic\TraxProduct;
use App\Http\Models\Admin\Logistic\TraxRiderCnDetail;
use App\Http\Models\Admin\Logistic\TraxService;
use App\Http\Models\Admin\Logistic\TraxShipperDetail;
use App\Http\Models\Admin\Logistic\TraxSpecialHandlingList;
use App\Http\Models\Admin\Logistic\TraxStation;
use App\Http\Models\Admin\Settings\GeneralSetting;
use App\Http\Models\City;
use App\Http\Models\CityDelivery;
use App\Http\Models\CorporateDefaultRateStatus;
use App\Http\Models\CorporateDefaultWeightCharge;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\CorporateWeightCharge;
use App\Http\Models\CorporateWeightChargeZoneWise;
use App\Http\Models\RateStatus;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\ShippingMode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Admin\Logistic\TraxLogisticBookingImages;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class RiderLogisticApiController extends Controller
{
    use RiderCns;
    public function logistic_data(Request $request)
    {
        $rider_id = $request->rider_id;
        $hub_id=$request->rider_hub;

        $city_ids=City::where('hub_id',$hub_id);
        if($city_ids->exists())
        {
            $hub_id=$city_ids->pluck('id');
        }


            $shipper_list=[];

            $rider_cn = $this->cn_issue_to_rider_filter($rider_id);

            $shippers = User::join('trax_shipper_details as sd','sd.user_id','=','users.id')
            ->select('users.id as shipper_id','users.name as shipper_name','users.phone as shipper_phone','users.address','users.city_id','sd.trax_parent_product_id','users.account_type_id','users.corporate_rate_type_id')
            ->where('users.status',3)->where('sd.status',1)
            ->where('sd.rider_id',$rider_id)
            ->get();

            foreach ($shippers as $shipper)
            {
                if($shipper->account_type_id==2)
                {
                    if($shipper->corporate_rate_type_id!=3)
                    {
                        $shipper_shipping_modes = CorporateRateStatus::where('user_id', $shipper->shipper_id)->where('status', 1)->select('shipping_mode_id')->get();
                    } else{
                        $shipper_shipping_modes = CorporateDefaultRateStatus::where('user_id', $shipper->shipper_id)->where('status', 1)->select('shipping_mode_id')->get();
                    }

                } else if($shipper->account_type_id==1){
                    $shipper_shipping_modes = RateStatus::where('user_id', $shipper->shipper_id)->where('status', 1)->select('shipping_mode_id')->get();
                }

                $shipper_list[] = [
                    'shipper_id' => $shipper->shipper_id,
                    'shipper_name' => $shipper->shipper_name,
                    'shipper_phone' => $shipper->shipper_phone,
                    'address' => $shipper->address,
                    'city_id' => $shipper->city_id,
                    'trax_parent_product_id' => $shipper->trax_parent_product_id,
                    'account_type_id' => $shipper->account_type_id,
                    'corporate_rate_type_id' => $shipper->corporate_rate_type_id,
                    'shipper_shipping_modes' => $shipper_shipping_modes
                ];
            }
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
                    ->whereIn('usi.city_id',$hub_id)
                    ->get();


            $products =  TraxProduct::select('id','product_code','product_name','parent_id')
                    ->where('status',1)->get();

            $services = TraxService::select('id','service_code','service_name','product_id','shipping_mode_id')
                ->where('status',1)->get();


            $shipping_modes = ShippingMode::select('id','mode as shipping_mode')->get();

            $city_deliveries =  CityDelivery::select('city_id','booking_type_id','shipping_mode_id')->where('booking_type_id',1)->get();

            $destination_list = TraxStation::select('id as destination_id','name as destination_name','station_code as destination_code')
                ->where('status',1)->get();

            $special_handling_list = TraxSpecialHandlingList::select('id as handling_id','description','rate','pay_mode')
                ->where('status',1)->get();

            $logistic_data = [
                'shipper_list'        =>   $shipper_list,
                'pickup_address_list' => $pickup_address_list,
                'products'          =>   $products,
                'services'          =>   $services,
                'rider_cn'          =>   $rider_cn,
                'destination_list'  =>   $destination_list,
                'special_handling_list' => $special_handling_list,
                'shipping_modes' => $shipping_modes,
                'city_deliveries' => $city_deliveries
            ];

             return response()->json(['status'=>0,'logistic_data'=> $logistic_data]);
    }

        public function logistic_booking_store(Request $request) {

            $bookig_data=$request->booking_data;
            $rider_id = $request->rider_id;
            $hub_id = $request->rider_hub;
            $current_date = Carbon::now()->toDateString();
            $already_exists_bookings = [];
            $already_used_cns =[];
            $un_inserted_cns=[];
            $already_used_child_cns =[];
            $book_Shipments=[];

            try {

                if (!empty($bookig_data))
                {
                        //check booking batch length for creating a batch
                        $batch_length=10;
                        $booking_batch_length =  GeneralSetting::where('type','booking_batch_length')->first();
                        if($booking_batch_length->exists())
                        {
                            $batch_length = $booking_batch_length->setting_value;
                        }

                        foreach ($bookig_data as $booking)
                        {
                            $cn=TraxCnIssueToRider::join('trax_rider_cn_details as rd','rd.cn_issue_id','trax_cn_issue_to_riders.id')
//                                ->where('trax_cn_issue_to_riders.area_code',$hub_id)
                                ->where('rd.cn_number',$booking['cn_number'])
                                ->where('rd.is_used',0)
                                ->where('rd.is_hold',0);

                            $old_booking = TraxLogisticBooking::where('cn_number',$booking['cn_number']);

                                if ($cn->exists() && !$old_booking->exists())
                                {
//                                    $old_booking = TraxLogisticBooking::where('cn_number',$booking['cn_number']);
//                                    if(!$old_booking->exists())
//                                    {
                                        $booking_weight=0;
                                        if($booking['total_volumetric_weight'] >= $booking['total_dense_weight'])
                                        {
                                            $booking_weight= $booking['total_volumetric_weight'];

                                        } else{
                                            $booking_weight= $booking['total_dense_weight'];
                                        }

                                        DB::beginTransaction();

                                        $origin_id=$hub_id;
                                        $user_ship_info=UserShippingInfo::where('id',$booking['pickup_address_id']);
                                        if($user_ship_info->exists())
                                        {
                                            $user_ship_info=$user_ship_info->first();
                                            $origin_id=$user_ship_info->city_id;
                                        }
                                        $logistic_booking = new TraxLogisticBooking();
                                        $logistic_booking->shipper_id = $booking['shipper_id'];
                                        $logistic_booking->cn_number = $booking['cn_number'];
                                        $logistic_booking->shipper_address_id = $booking['pickup_address_id'];
                                        $logistic_booking->booking_date = $booking['booking_date'];
                                        $logistic_booking->product_id = $booking['product_id'];
                                        $logistic_booking->rider_id = $rider_id;
                                        $logistic_booking->service_id = $booking['service_id'];
                                        $logistic_booking->origin_id = $origin_id;
                                        $logistic_booking->destination_id = $booking['destination_id'];
                                        $logistic_booking->shipper_reference = $booking['shipper_reference'];
                                        $logistic_booking->consignee_name = $booking['consignee_name'];
                                        $logistic_booking->consignee_address = 'Address';
                                        $logistic_booking->consignee_phone_1 ='03100112321';
                                        $logistic_booking->total_pieces = $booking['total_pieces'];
                                        $logistic_booking->total_booking_weight = $booking_weight;
                                        $logistic_booking->total_dense_weight = $booking['total_dense_weight'];
                                        $logistic_booking->total_volumetric_weight = $booking['total_volumetric_weight'];
                                        $logistic_booking->user_type = 2; // 1 - Admin, 2 - Rider, 0 -> shipper
                                        $logistic_booking->created_by = $rider_id;
                                        $logistic_booking->payment_mode_id=1;
                                        $logistic_booking->save();

                                        //update CN status and mark cn used by rider
                                        $rider_cn=TraxRiderCnDetail::where('cn_number',$booking['cn_number']);
                                        if($rider_cn->exists())
                                        {
                                            $rider_cn=$rider_cn->first();
                                            $rider_cn->is_used=1;
                                            $rider_cn->updated_by=$rider_id;
                                            $rider_cn->save();
                                        }

                                        $shipment=Shipment::where('tracking_number',$logistic_booking['cn_number']);
                                        if(!$shipment->exists())
                                        {
                                            $charges_mode_id=0;

                                            if($booking['account_type_id']==1)
                                            {
                                                $charges_mode_id=4;

                                            } else  if($booking['account_type_id']==2)
                                            {
                                                $charges_mode_id=3;
                                            }

                                            //send data to shipments table
                                            $shipment_id = LogisticToShipmentSyncController::shipments_book($booking['shipper_id'],$booking['cn_number'],$booking['pickup_address_id'],1,1,$booking['destination_id'],$booking['consignee_name'],'Consignee Address','03100112321',$booking['booking_date'],$booking_weight,$booking['shipper_reference'],0,$booking['shipping_mode_id'],0,1,1,1,$charges_mode_id, 1,1,0.0,null,2,$rider_id,$origin_id);

                                            //insert shipment item
                                            if(isset($shipment_id))
                                            {
                                                LogisticToShipmentSyncController::shipment_item($shipment_id,$booking['total_pieces']);
                                            }

                                        }

                                        //Insert logistic booking pieces
                                        if (isset($booking['booking_pieces_data']))
                                        {
                                            foreach ($booking['booking_pieces_data'] as $pieces_data) {
                                                $i=1;
                                                for ($cn_no=$pieces_data['from_pieces'];$cn_no<=$pieces_data['to_pieces'];$cn_no++)
                                                {
                                                    $child_cn=TraxCnIssueToRider::join('trax_rider_cn_details as rd','rd.cn_issue_id','trax_cn_issue_to_riders.id')
                                                        ->where('trax_cn_issue_to_riders.area_code',$hub_id)
                                                        ->where('rd.cn_number',$cn_no)
                                                        ->where('rd.is_used',0)
                                                        ->where('rd.is_hold',0);

                                                    if($child_cn->exists())
                                                    {
                                                        $booking_piece = new TraxBookingPiece();
                                                        $booking_piece->booking_id = $logistic_booking->id;
                                                        $booking_piece->piece_cn_number = $cn_no;
                                                        $booking_piece->scan_rider_id = $rider_id;
                                                        $booking_piece->user_type = 2;
                                                        $booking_piece->created_by = $rider_id;
                                                        $booking_piece->save();

                                                        //update Child CN status and mark cn used by rider
                                                        $rider_cn=TraxRiderCnDetail::where('cn_number',$cn_no);
                                                        if($rider_cn->exists())
                                                        {
                                                            $rider_cn=$rider_cn->first();
                                                            $rider_cn->is_used=1;
                                                            $rider_cn->updated_by=$rider_id;
                                                            $rider_cn->save();
                                                        }

                                                        //send data to shipment pieces atif sir said only set 1 piece of shipment
//                                                        if(isset($shipment_id))
//                                                        {
//                                                            LogisticToShipmentSyncController::shipment_pieces($shipment_id,$cn_no,$i);
//                                                        }

                                                    } else{
                                                        $already_used_child_cns [] = [
                                                            'cn_number'=>$booking['cn_number']
                                                        ];
                                                    }

                                                }
                                            }
                                        }

                                        //Insert logistic item reference data
                                        if (isset($booking['item_refernces_data']))
                                        {
                                            foreach ($booking['item_refernces_data'] as  $item_data) {

                                                foreach ($item_data['item_detail'] as $detail){

                                                    $item_reference = new TraxItemRefernce();

                                                    $item_reference->booking_id = $logistic_booking->id;
                                                    $item_reference->item_code = $item_data['item_code'];
                                                    $item_reference->width = $detail['width'];
                                                    $item_reference->height = $detail['height'];
                                                    $item_reference->length = $detail['length'];
                                                    $item_reference->weight = $detail['weight'];
                                                    $item_reference->no_piece = $detail['no_piece'];
                                                    $item_reference->user_type = 2;
                                                    $item_reference->created_by = $rider_id;
                                                    $item_reference->save();
                                                }
                                            }
                                        }

                                        //Insert logistic item insurance data
                                        if (isset($booking['item_insurance_data']))
                                        {
                                            foreach ($booking['item_insurance_data'] as $item_insure)
                                            {
                                                $item_insurance = new TraxItemInsurance();

                                                $item_insurance->booking_id = $logistic_booking->id;
                                                $item_insurance->special_handling_id = $item_insure['special_handling_id'];
                                                $item_insurance->insurance = $item_insure['insurance'];
                                                $item_insurance->item_code = $item_insure['item_code'];
                                                $item_insurance->user_type = 2;
                                                $item_insurance->created_by = $rider_id;
                                                $item_insurance->save();
                                            }
                                        }

                                        //logistic batch process
                                        $batch = TraxBookingBatch::leftJoin('trax_booking_batch_details as bbd', 'trax_booking_batches.id', '=', 'bbd.batch_id')
                                            ->where('trax_booking_batches.city_id', $hub_id)
                                            ->where('trax_booking_batches.batch_date', $current_date)
                                            ->selectRaw('COUNT(bbd.batch_id) AS batch_count, trax_booking_batches.id AS batch_id')
                                            ->groupBy('trax_booking_batches.id')
                                            ->havingRaw('batch_count < ?',[$batch_length]);

                                        //check batch exist than check batch length
                                        if($batch->exists())
                                        {
                                            $batch_id = $batch->first()->batch_id;
                                        } else {
                                            //create new batch
                                            $batch_id = AdminBatchController::booking_batch_store($hub_id,$batch_length);
                                        }
                                        if(isset($logistic_booking->id))
                                        {
                                            //create booking batch detail for add bookings in batch
                                            AdminBatchController::booking_batch_detail_store($batch_id,$logistic_booking->id);
                                        }


                                        DB::commit();

                                        // add bookings detail for send email to shipper
                                        $book_Shipments[$booking['shipper_id']][] = $logistic_booking->id;

//                                    } else{
//                                        $old_booking = $old_booking->first();
//                                        $already_exists_bookings [] = [
//                                            'booking_id'=>$old_booking->id,
//                                            'cn_number'=>$old_booking->cn_number
//                                        ];
//                                    }
                                } else {
                                    $un_inserted_cns [] = [
                                        'cn_number'=>$booking['cn_number']
                                    ];
//                                    $already_used_cns [] = [
//
//                                    ];
                                }

                        }

                        if(!empty($book_Shipments))
                        {
                            NotificationsController::send(232,$book_Shipments);
                        }

//                        if(!empty($already_exists_bookings) || !empty($un_inserted_cns) || !empty($already_used_child_cns))
//                        {
//                            return response()->json(['status'=>1,'error'=>'Something went wrong','already_exists_bookings'=>$already_exists_bookings,'already_used_cns'=>$already_used_cns,'already_used_child_cns'=>$already_used_child_cns]);
//                        }
                        if( !empty($un_inserted_cns) || !empty($already_used_child_cns))
                        {
                            return response()->json(['status'=>1,'error'=>'Something went wrong','un_inserted_cns'=>$un_inserted_cns,'already_used_child_cns'=>$already_used_child_cns]);
                        }
//                        return response()->json(['status'=>0,'success'=>'Booking Completed Successfully','already_exists_bookings'=>$already_exists_bookings,'already_used_cns'=>$already_used_cns,'already_used_child_cns'=>$already_used_child_cns]);
                          return response()->json(['status'=>0,'success'=>'Booking Completed Successfully','un_inserted_cns'=>$un_inserted_cns,'already_used_child_cns'=>$already_used_child_cns]);

                } else {
                    return response()->json(['status'=>1,'error'=>'Logistic Booking empty not add']);
                }

            } catch (\Exception $ex) {
                DB::rollback();
                return response()->json(['status'=>1,'error'=> $ex->getMessage()]);
            }
        }

        public function get_shipper_detail(Request  $request)
        {
                $hub_id = $request->rider_hub;
                $shipper_id = $request->shipper_id;
                $pickup_address_list='';
                $shipper_list=[];

                $city_ids=City::where('hub_id',$hub_id);
                if($city_ids->exists())
                {
                    $hub_id=$city_ids->pluck('id');
                }
                if(isset($shipper_id))
                {
                    $shipper = User::join('trax_parent_products as pp','pp.segment_id','=','users.segment_id')
                        ->select('users.id as shipper_id','users.name as shipper_name','users.phone as shipper_phone','users.address','users.city_id','pp.id AS trax_parent_product_id','users.account_type_id','users.corporate_rate_type_id')
                        ->where('users.status',3)->where('pp.status',1)
                        ->where('users.id', $shipper_id);
                        if($shipper->exists())
                        {

                            $shipper=$shipper->first();
                            if($shipper->account_type_id==2)
                            {
                                if($shipper->corporate_rate_type_id!=3)
                                {
                                    $shipper_shipping_modes = CorporateRateStatus::where('user_id', $shipper->shipper_id)->where('status', 1)->select('shipping_mode_id')->get();
                                } else{
                                    $shipper_shipping_modes = CorporateDefaultRateStatus::where('user_id', $shipper->shipper_id)->where('status', 1)->select('shipping_mode_id')->get();
                                }
                            } else if($shipper->account_type_id==1){
                                $shipper_shipping_modes = RateStatus::where('user_id', $shipper->shipper_id)->where('status', 1)->select('shipping_mode_id')->get();
                            }
                        
                            // $shipper = $shipper->get();
                            $pickup_address_list = UserShippingInfo::select('id as pickup_address_id', 'pickup_address', 'poc as contact_person', 'phone as contact_number', 'email as contact_email', 'user_id as shipper_id')
                                ->where('user_id',$shipper_id)->whereIn('city_id',$hub_id)->get();

                            $shipper->shipper_shipping_modes=$shipper_shipping_modes;

                            $shipper_detail = [
                                'shipper'        =>   $shipper,
                                'pickup_address_list' => $pickup_address_list
                            ];
    

                           return response()->json(['status'=>0,'shipper_detail'=> $shipper_detail]);
    
                        }
                        
                }
                return response()->json(['status'=>1,'error'=>'Shipper ID not found!']);
        }

        public function store_image(Request $request) {

           // Log::channel('code_test_log')->error('logistic CN Number: '. $request->cn_number);
            $rules = [ 
                'booking_image' => ['required', 'mimes:png,jpeg,jpg'],
                'cn_number' => ['required']
            ];

            $messages = [
                'required' => ':attribute is Required.',
            ];

            $validate = Validator::make($request->all(), $rules, $messages);

            if ($validate->fails()) {
                return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
            }else {

                try {
                    $booking_id = TraxLogisticBooking::where('cn_number', $request->cn_number);
                    if($booking_id->exists())
                    {
                        $booking_id = $booking_id->pluck('id')->first();
                        $image = new TraxLogisticBookingImages();
                        $time = Carbon::now()->timestamp;
                        $image_name =$booking_id . '_' . $time . '.png';
                        $image_path = 'logistic_bookings/' . $image_name;
                        Log::channel('code_test_log')->error('logistic image_path: '. $image_path.' booking_image - > '.$request->booking_image);
//                    Storage::disk('public')->put($image_path, file_get_contents($request->booking_image));
                        Storage::disk('s4')->put($image_path, file_get_contents($request->booking_image));
                        $image->booking_id = $booking_id;
                        $image->image_name = $image_name;
                        $image->image_path = $image_path;
                        $image->save();
                        return response()->json(['status' => 0, 'message' => 'Image has been stored!']);
                    } else {
                        return response()->json(['status' => 1, 'error' => 'Image not save','cn_number' => $request->cn_number]);
                    }
                } catch (\Exception $ex) {
                    Log::channel('code_test_log')->error('logistic-booking-image: ' . json_encode($ex->getMessage()));
                    return response()->json(['status' => 1, 'message' => 'An error occurred while storing the image:'.$ex->getMessage()]);
                }
            }

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
