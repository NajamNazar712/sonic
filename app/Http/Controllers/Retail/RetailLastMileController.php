<?php

namespace App\Http\Controllers\Retail;

use App\Helpers\PayfastApiCall;
use App\Http\Controllers\Admins\Handover\HandoverShipmentJourneyController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentOpenBoxJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\DeliveryRelation;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\BookingType;
use App\Http\Models\ConsigneeLocation;
use App\Http\Models\Handover\Handover;
use App\Http\Models\Handover\HandoverShipments;
use App\Http\Models\Notification;
use App\Http\Models\Rider;
use App\Http\Models\RiderDelivery;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentDetail;
use App\Http\Models\ShipmentDistributionProduct;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentOtp;
use App\Http\Models\ShipmentOtpVerification;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShippingMode;
use App\Jobs\CountFintechCharges;
use App\Jobs\LastMileAppReport;
use App\Models\PudoDeliverShipment;
use App\Models\PudoDeliverShipmentOtp;
use App\Models\TransferNote;
use App\Models\TransferNoteShipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\Self_;
use Yajra\DataTables\DataTables;

class RetailLastMileController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:retail');
    }

    public function index(Request $request){

        $shipment_status = ShipmentStatus::select('id','name')->whereIn('id',[154,156])->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        $delivery_relation = DeliveryRelation::all();
        return view('retail.pudo.pending_shipments',compact('shipment_status','service_type','shipping_mode','delivery_relation'));
    }

    public function pending_list(Request $request)
    {

        $retail_user = Auth::user();
//        $from = $request->search_date_from ? Carbon::parse($request->search_date_from)->toDateString() : null;
//        $to = $request->search_date_to ? Carbon::parse($request->search_date_to)->toDateString() : null;
        $tracking = $request->get('search_tracking');

        $from = $request->get('search_date_from');
        $to   = $request->get('search_date_to');

        if ($from || $to) {
            $from = Carbon::parse($from ?? $to)->startOfDay();
            $to   = Carbon::parse($to ?? $from)->endOfDay();
        } else {
            $from = Carbon::today()->startOfDay();
            $to   = Carbon::today()->endOfDay();
        }



        $latestJourney = DB::table('shipments_journey')
            ->select('shipment_id', DB::raw('MAX(created_at) as latest_created_at'))
            ->whereIn('shipper_status_id', [154, 156])
            ->groupBy('shipment_id');

        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('pudo_deliver_shipments as pds', 'pds.shipment_id', '=', 'shipments.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->joinSub($latestJourney, 'sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id');
            })
            ->select(
                'shipments.id as shId',
                'shipments.tracking_number',
                'shipments.tracking_number as tracking',
                'u.name as shipper',
                'u.phone as shipper_phone1',
                'u.phone2 as shipper_phone2',
                'oc.name as origin',
                'dc.name as destination',
                'shipments.order_id',
                'h.name as hub',
                'shipments.consignee_name',
                'shipments.consignee_phone_number_1',
                'shipments.consignee_phone_number_2',
                'shipments.consignee_address',
                'shipments.amount',
                'sm.mode',
                'bt.booking_type as service_type',
                'ss.name as status',
                'shipments.shipper_status_id as shipper_status_id',
//                'dc.pickup as pickup',
//                'shipments.intercepted as intercepted',
//                'shipments.nsa_osa_estimated_charges'
            )
            ->whereIn('shipments.shipper_status_id', [154, 156])
            ->where('pds.retail_store_id', $retail_user->category_id)
            ->where('pds.retail_type', $retail_user->category);
            if (!empty($tracking)) {
                $shipments->where('shipments.tracking_number',$tracking);
            } else {
                if (!empty($from) && !empty($to)) {
                    $shipments->whereBetween('sj.latest_created_at', [$from, $to]);
                }
            }
        $shipments->groupBy('shipments.id');

        return Datatables::of($shipments)
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('retail.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->editColumn('consignee_phone', function ($shipments) {
                return '<button type="button" class="btn btn-sm btn-outline-info align-middle consignee_info_label" rel="' . $shipments->consignee_phone_number_1 . '"><i class="la la-lg la-phone align-middle"></i> <span class="align-middle">' . $shipments->consignee_phone_number_1 . '|' . $shipments->consignee_phone_number_2 . '</span></button>';
            })
            ->filterColumn('consignee_phone', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('shipments.consignee_phone_number_1', 'like', '%' . $keyword . '%')
                        ->orWhere('shipments.consignee_phone_number_2', 'like', '%' . $keyword . '%');
                } else {
                    $query->whereRaw('false');
                }
            })
//            ->addColumn('shipment_remarks', function ($shipments) {
//                $remark = '<textarea style="width:200px;" placeholder="Enter Remarks" class="form-control form-control-sm" rows="4" cols="100" >' . $shipments->remarks . '</textarea>';
//                return $remark;
//            })
            ->orderColumn('consignee_phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1')
//            ->editColumn('arrival', function ($shipments) {
//                if ($shipments->arrival) {
//                    return $shipments->arrival;
//                } else {
//                    return " - ";
//                }
//            })
            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword != '') {
                    $query->where('ss.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($result) {
                $button = '<button type="button" class="dropdown-item btn-delivered" data-id='.$result->shId.' data-cod='.$result->amount .' ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Delivered</div></button>';

                $dropdown = "
                <div class='btn-group'>
                    <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                    <div class='dropdown-menu dropdown-menu-sm'>";
                $dropdown .= $button;

                $dropdown .= "
                    </div>
                </div>
            ";

                return $dropdown;
            })
            ->rawColumns(['tracking_number', 'consignee_phone', 'action'])
            ->make(true);
    }

    public function shipment_delivered(Request $request)
    {
        $validated = Validator::make($request->all(),[
           'shipment_id' => 'required|integer',
            'otp' =>  'required|integer',
            'consignee_name' => 'required',
            'cnic' => 'required',
            'relation' => 'required',
            'cod_value' => 'required|numeric',
            'remarks' => 'nullable'
        ]);

        if($validated->fails()){
            return response()->json(['status' => 0, 'message' => 'Input validation failed']);
        }

        $shipment = Shipment::find($request->shipment_id);
        if($shipment) {
            $pudo_otp = PudoDeliverShipmentOtp::where('shipment_id',$shipment->id)
                ->where('otp',$request->otp)
                ->where('is_verified',0)->first();
            if(!$pudo_otp) {
                return response()->json(['status' => 0, 'message' => 'Shipment OTP not match!']);
            }
            if(!in_array($shipment->shipper_status_id,[154,156])) {
                return response()->json(['status' => 0, 'message' => 'Shipment status already modified!']);
            }
            $rider_id = 1837;
            $rider = GlobalSettings::where('type','rider_center_collection')->first();
            if($rider) {
                $rider_id = $rider->setting_value;
            }
            $retail_user = Auth::user();
            if($shipment->shipper_status_id == 156) {
                $shipment->shipper_status_id = 13;
                $shipment->consignee_status_id = 13;
                $shipment->save();

                ShipmentsJourneyController::add(
                    $shipment->id,
                    13, 13, null, null, null, null,
                    null,
                    null, 1, null, null, null, null, null,
                    Auth::id()
                );

                $note_id = self::create_delivery_note($retail_user->hub_id,null,$rider_id,$shipment->id,null);
                $added_at = Carbon::now();
                self::delivered($added_at,$note_id,$shipment->id,$rider_id,$request->consignee_name,$request->cnic,$request->relation,null,null,null,null,null,0,0,0,0);

            }else {
                $note_id = self::create_delivery_note($retail_user->hub_id,null,$rider_id,$shipment->id,null);
                $added_at = Carbon::now();
                self::delivered($added_at,$note_id,$shipment->id,$rider_id,$request->consignee_name,$request->cnic,$request->relation,null,null,null,null,null,0,0,0,0);
            }

            $pudo_otp->is_verified = 1;
            $pudo_otp->save();
            return response()->json(['status' => 1, 'message' => 'Shipment Delivered Successfully!']);

        }
        return response()->json(['status' => 0, 'message' => 'Shipment not found!']);
    }

    public static function create_delivery_note(
        $hub_id,
        $selected_route_id,
        $selected_rider_id,
        $shipment_id,
        $open_box_id= null,
    ) {
        $shipment = Shipment::find($shipment_id);
        if($shipment) {
            $shipment->shipper_status_id = 5;
            $shipment->consignee_status_id = 5;
            $shipment->save();

            $total_cod_amount = Shipment::where('id', $shipment->id)->where(function ($query) {
                $query->where('booking_type_id', '!=', 4)
                    ->orWhere(function ($sub_query) {
                        $sub_query->where('booking_type_id', '=', 4)
                            ->where('charges_mode_id', '=', 2);
                    });
            })->sum('amount');

            $admin = 346;

            $note = DeliveryNote::create([
                'hub_id' => $hub_id,
                'rider_id' => $selected_rider_id,
                'route_id' => empty($selected_route_id) ? '1837' : $selected_route_id,  //TO-6892
                'shipments_count' => 1,
                'admin_id' => $admin,
                'total_cod_amount' => $total_cod_amount,
                'password' => null,
                'last_updated_at' => Carbon::now(),
                'ordering' => 1
            ]);

            if ($note) {
                DeliveryNoteShipment::create([
                    'delivery_note_id' => $note->id,
                    'shipment_id' => $shipment->id,
                    'notification' => 1,
                    'rider_information' => 1,
                    'ordering' => 1
                ]);
            }

            if($shipment_id == $open_box_id) {
                $shipment_detail = ShipmentDetail::where('shipment_id', $shipment->id)->where('is_open', '=', 0)->first();
                if ($shipment_detail) {
                    $shipment_detail->is_open = 1;
                    $shipment_detail->save();
                }

//                $shipment_data = Shipment::find($shipment);
                $shipment->open_box = 1;
                $shipment->save();

                ShipmentOpenBoxJourneyController::add($shipment->id, 3, Auth::id());
            }

            $old_delivery_note_id = DeliveryNoteShipment::where('shipment_id', $shipment->id)->where('status', '>', 0)->orderBy('delivery_note_id', 'desc');

            if ($old_delivery_note_id->exists()) {
                $old_delivery_note_id = $old_delivery_note_id->first();

                if (DeliveryNote::where('id', $old_delivery_note_id->delivery_note_id)->where('status', 0)->exists()) {
                    $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 0)->latest()->first();
                    if ($journey) {
                        ShipmentsJourneyController::add($journey->shipment_id, $journey->shipper_status_id, $journey->consignee_status_id, $journey->status_reason_id, $journey->remarks, $journey->user_id, $admin, $journey->reference_1_id, NULL, 1, $journey->received_or_refused_by);
                    }
                }
            }
//            $transfer_note_id =null;
//            $transfer_note = TransferNoteShipment::where('shipment_id',$shipment->id)->where('status_id',2)->first();
//            if($transfer_note) {
//                $transfer_note_id = $transfer_note->transfer_note_id;
//            }

            ShipmentsJourneyController::add(
                $shipment->id,
                5, 5, null, null, null, null,
                $note->id,
                null, 1, null, null, null, null, null,
                Auth::id()
            );
//            ShipmentsJourneyController::add($shipment, 5, 5, NULL, NULL, NULL, $admin, $note->id, $note->rider_id);

            $handover_shipments = HandoverShipments::where('shipment_id', $shipment->id)->whereIn('status', [1, 3]);
            if ($handover_shipments->exists()) {
                $handover_shipments = $handover_shipments->first();
                $handover_shipments->status = 2;
                $handover_shipments->save();
                $handover_count = HandoverShipments::where('status', 1)->where('handover_id', $handover_shipments->handover_id)->count();
                if ($handover_count == 0) {
                    $handover = Handover::find($handover_shipments->handover_id);
                    $handover->received_by = $admin;
                    $handover->received_at = Carbon::now();
                    $handover->received = $handover->received + 1;
                    $handover->status_id = 4;
                    $handover->save();
                }
                HandoverShipmentJourneyController::add($shipment->id, $handover_shipments->handover_id, 2);
            }

            if($shipment->amount > 0) {
                $environment = config('app.env');
                if ($environment == 'production' || $environment == 'staging') {
                    //When Admin Create Delivery Note
                    $payment_details = PayfastApiCall::ApiCall($note->id, $shipment_id);
                    $rand = $payment_details['unique_key'];
                    $payment_link = $payment_details['payment_link'];
                    $url = $payment_details['url'];
                    $trans_id = $payment_details['id'];
//                    Log::channel('trax_pay_test')->info('sh '. json_encode($shipment_id, true));
                    CountFintechCharges::dispatch($shipment_id, $payment_link, $rand, $url , $trans_id);
                }
            }
            return $note->id;
        } else {
            return false;
        }
    }


    public static function delivered(
        $added_at,
        $delivery_note_id,
        $shipment_id,
        $rider_id,
        $receiver_name=null,
        $cnic=null,
        $relation=null,
        $picture=null,
        $cnic_image=null,
        $house_image=null,
        $ccd_image=null,
        $replacement_image=null,
        $start_location_latitude=null,
        $start_location_longitude=null,
        $actual_location_latitude=null,
        $actual_location_longitude=null

    ){

        if (!RiderDelivery::where('delivery_note_id', $delivery_note_id)->where('shipment_id', $shipment_id)->where('delivered_status', 1)->exists()) {
            if (DeliveryNoteShipment::join('delivery_notes as dn', 'delivery_note_shipments.delivery_note_id', 'dn.id')->where('dn.id', $delivery_note_id)->where('shipment_id', $shipment_id)->where('dn.rider_id', $rider_id)->exists()) {
                $destination = $actual_location_latitude . ',' . $actual_location_longitude;

                $rider_delivery = new RiderDelivery();
                $rider_delivery->added_at = $added_at;
                $rider_delivery->delivery_note_id = $delivery_note_id;
                $rider_delivery->shipment_id = $shipment_id;
                $rider_delivery->rider_id = $rider_id;
                $rider_delivery->start_location_latitude = $start_location_latitude;
                $rider_delivery->start_location_longitude = $start_location_longitude;
                $rider_delivery->actual_location_latitude = $actual_location_latitude;
                $rider_delivery->actual_location_longitude = $actual_location_longitude;
                $rider_delivery->rider_status_id = 14;
                $rider_delivery->delivered_status = 1;
                $received_by = NULL;
                if ($receiver_name) {
                    $receiver_name = str_replace('"', '', $receiver_name);
                    $received_by = $receiver_name;
                }

                 if($cnic){
                     $cnic = str_replace('"', '', $cnic);
                     if ($cnic != "Empty") {
                         $rider_delivery->cnic = $cnic;
                     } else {
                         $cnic = NULL;
                     }
                 }

                 if($relation) {
                     $relation = str_replace('"', '', $relation);
                     if ($relation != "Empty") {
                         $rider_delivery->relation = $relation;
                     } else {
                         $relation = NULL;
                     }
                 }


                $shipment = Shipment::find($shipment_id);

                $consignee_phone_number_1 = $shipment->consignee_phone_number_1;
                $consignee_phone_number_2 = $shipment->consignee_phone_number_2;
                $consignee_address = $shipment->consignee_address;

                $coordinates = ConsigneeLocation::where(function ($sub_query) use ($consignee_phone_number_1, $consignee_phone_number_2) {
                    $sub_query->where('phone_number', $consignee_phone_number_1)
                        ->orwhere('phone_number', $consignee_phone_number_2);
                })->where('address', $consignee_address);

                if ($actual_location_latitude > 0 && $actual_location_longitude > 0) {

                    $origin = $start_location_latitude . ',' . $start_location_longitude;
                    $rider_delivery->distance_from_start_to_actual = self::distance($origin, $destination);

                    if ($coordinates->exists()) {
                        $coordinates = $coordinates->latest()->first();

                        $rider_delivery->current_location_latitude = $coordinates->lat;
                        $rider_delivery->current_location_longitude = $coordinates->long;

                        $origin = $coordinates->lat . ',' . $coordinates->long;

                        $distance = self::distance($origin, $destination);

                        $rider_delivery->distance_from_current_to_actual = $distance;
                    }

                } else {
                    $rider_delivery->distance_from_start_to_actual = 0;

                    if ($coordinates->exists()) {
                        $rider_delivery->current_location_latitude = $coordinates->lat;
                        $rider_delivery->current_location_longitude = $coordinates->long;
                        $rider_delivery->distance_from_current_to_actual = 0;
                    }
                }
                $rider_delivery->save();

                if ($picture) {
                    $time = Carbon::now()->toDateString();
                    $picture_path = 'rider_delivery/picture_' . $rider_delivery->id . '_' . $time . '.png';
                    Storage::disk('public')->put($picture_path, file_get_contents($picture));
                    $rider_delivery->picture_path = $picture_path;
                    $rider_delivery->save();
                }
                if ($cnic_image) {
                    $time = Carbon::now()->toDateString();
                    $picture_path = 'rider_delivery/cnic_image_' . $rider_delivery->id . '_' . $time . '.png';
                    Storage::disk('public')->put($picture_path, file_get_contents($cnic_image));
                    $rider_delivery->cnic_image = $picture_path;
                    $rider_delivery->save();
                }
                if ($house_image) {
                    $time = Carbon::now()->toDateString();
                    $picture_path = 'rider_delivery/house_image_' . $rider_delivery->id . '_' . $time . '.png';
                    Storage::disk('public')->put($picture_path, file_get_contents($house_image));
                    $rider_delivery->house_image = $picture_path;
                    $rider_delivery->save();
                }
                if ($replacement_image) {
                    $time = Carbon::now()->toDateString();
                    $picture_path = 'rider_delivery/replacement_image_' . $rider_delivery->id . '_' . $time . '.png';
                    Storage::disk('public')->put($picture_path, file_get_contents($replacement_image));
                    $rider_delivery->replacement_image = $picture_path;
                    $rider_delivery->save();
                }
                if ($ccd_image) {
                    $time = Carbon::now()->toDateString();
                    $picture_path = 'rider_delivery/ccd_image_' . $rider_delivery->id . '_' . $time . '.png';
                    Storage::disk('public')->put($picture_path, file_get_contents($ccd_image));
                    $rider_delivery->ccd_image = $picture_path;
                    $rider_delivery->save();
                }

                if (DeliveryNote::where('id', $delivery_note_id)->where('pending_status', 0)->exists()) {

//                    if ($distribution == 1) {
//                        if ($request->has('distribution_items_list')) {
//                            $distribution_items = json_decode($request->distribution_items_list, true);
//                            foreach ($distribution_items as $distribution_item) {
//                                $product = ShipmentDistributionProduct::find($distribution_item["pid"]);
//                                $product->total_delivered_units = $distribution_item["delivered_qty"];
//                                $product->received_amount = round($distribution_item["total_amount_in_double"]);
//                                $total_delivered_skus = $distribution_item["delivered_qty"] / $product->units_per_item;
//                                $product->total_delivered_skus = (int)$total_delivered_skus;
//                                $product->save();
//                            }
//                            $shipment->shipper_status_id = 14;
//                            $shipment->consignee_status_id = 14;
//                            $shipment->received_amount = round($request->total_cod_amount);
//                            $shipment->amount = round($request->total_cod_amount);
//                            DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 6, 'update_type' => 1]);
//                            ShipmentsJourneyController::add($shipment->id, 14, 14, NULL, NULL, NULL, NULL, $delivery_note_id, NULL, 1, $received_by, $rider_id, $cnic, $relation);
//
//                            //$this->rider_wise_delivery_note($shipment->id,$request->delivery_note_id,$rider_id,14,$added_at,$rider_delivery,2);
//                            // dispatch(new LastMileApp($shipment->id,$request->delivery_note_id,$rider_id,14,$added_at,$rider_delivery,2));
//
//                            LastMileAppReport::dispatch($shipment->id,$delivery_note_id,$rider_id,14,$added_at,$rider_delivery,2);
//
//                        }
//                    }
                    if ($shipment->booking_type_id == 2) {
                        $shipment->shipper_status_id = 30;
                        $shipment->consignee_status_id = 30;
                        $shipment->received_amount = $shipment->amount;

                        DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 2, 'update_type' => 1]);
//                        ShipmentsJourneyController::add($shipment->id, 30, 30, NULL, NULL, NULL, NULL, $delivery_note_id, NULL, 1, $received_by, $rider_id, $cnic, $relation);

                        ShipmentsJourneyController::add(
                            $shipment->id,
                            30, 30, null, null, null, null,
                            $delivery_note_id,
                            null, 1, $received_by, null, $cnic, $relation, null,
                            Auth::id()
                        );
//                        $details = ['tracking_number' => $shipment->tracking_number, 'shipper_number_1' => $shipment->user->phone, 'shipper_number_2' => $shipment->user->phone2, 'name' => $shipment->consignee_name, 'consignee_number_1' => $shipment->consignee_phone_number_1, 'consignee_number_2' => $shipment->consignee_phone_number_2, 'shipper_name' => $shipment->user->name];
//                        NotificationsController::send(183, $details);
//                        NotificationsController::send(184, $details);
                        $rider_delivery->rider_status_id = 30;
                    }
                    else if ($shipment->booking_type_id == 3) {
                        $shipment_items = ShipmentItem::where('shipment_id',$shipment->id)->get();
//                        $res = str_replace(array('[', ']', '"'), '', $request->trybuy_id_list);
//                        $item_ids = explode(',', $res);
                        $total_cod = 0;
                        $total_itmes =0;
                        foreach ($shipment_items as $shipment_item) {
//                            $shipment_item = ShipmentItem::find($item_id);
                            $total_cod += $shipment_item->price;
                            $shipment_item->bought = 1;
                            $shipment_item->save();
                            $total_itmes+=1;
                        }
                        $shipment = Shipment::find($shipment->id);
                        $total_cod += $shipment->try_and_buy_fees;
                        $total_parcels = ShipmentItem::where('shipment_id', $shipment->id)->count();
                        $delivered_parcels = $total_itmes;
                        if ($total_parcels == $delivered_parcels) {

                            Shipment::where('id', $shipment->id)->update(['amount' => $total_cod, 'received_amount' => $total_cod, 'shipper_status_id' => 36, 'consignee_status_id' => 36]);
//                            ShipmentsJourneyController::add($shipment->id, 36, 36, NULL, NULL, NULL, NULL, $delivery_note_id, NULL, 1, $received_by, $rider_id, $cnic, $relation);
                            ShipmentsJourneyController::add(
                                $shipment->id,
                                36, 36, null, null, null, null,
                                $delivery_note_id,
                                null, 1, $received_by, null, $cnic, $relation, null,
                                Auth::id()
                            );
                            $rider_delivery->rider_status_id = 36;

                        } else {

                            Shipment::where('id', $shipment->id)->update(['amount' => $total_cod, 'received_amount' => $total_cod, 'shipper_status_id' => 37, 'consignee_status_id' => 37]);
//                            ShipmentsJourneyController::add($shipment->id, 37, 37, NULL, NULL, NULL, NULL, $delivery_note_id, NULL, 1, $received_by, $rider_id, $cnic, $relation);
                            ShipmentsJourneyController::add(
                                $shipment->id,
                                37, 37, null, null, null, null,
                                $delivery_note_id,
                                null, 1, $received_by, null, $cnic, $relation, null,
                                Auth::id()
                            );
                            $rider_delivery->rider_status_id = 37;

                        }
                        DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 5, 'update_type' => 1]);
                    }
                    else if ($shipment->booking_type_id == 4) {

//                        ShipmentsJourneyController::add($shipment->id, 14, 14, NULL, NULL, NULL, NULL, $delivery_note_id, NULL, 1, $received_by, $rider_id, $cnic, $relation);

                        ShipmentsJourneyController::add(
                            $shipment->id,
                            14, 14, null, null, null, null,
                            $delivery_note_id,
                            null, 1, $received_by, null, $cnic, $relation, null,
                            Auth::id()
                        );
                        if ($shipment->charges_mode_id == 1) {
                            $shipment->shipper_status_id = 14;
                            $shipment->consignee_status_id = 14;

                            DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 7, 'update_type' => 1]);
                        } else {
                            $shipment->shipper_status_id = 14;
                            $shipment->consignee_status_id = 14;
                            $shipment->received_amount = $shipment->amount;
                            DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 6, 'update_type' => 1]);
                        }
                    }
                    else {
                        $shipment->shipper_status_id = 14;
                        $shipment->consignee_status_id = 14;
                        $shipment->received_amount = $shipment->amount;

                        DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->where('shipment_id', $shipment->id)->update(['status' => 6, 'update_type' => 1]);
//                        ShipmentsJourneyController::add($shipment->id, 14, 14, NULL, NULL, NULL, NULL, $delivery_note_id, NULL, 1, $received_by, $rider_id, $cnic, $relation);

                        ShipmentsJourneyController::add(
                            $shipment->id,
                            14, 14, null, null, null, null,
                            $delivery_note_id,
                            null, 1, $received_by, null, $cnic, $relation, null,
                            Auth::id()
                        );
                    }
                    $shipment->delivery_in_route = 0;
                    $shipment->save();
                }
                $rider_delivery->save();

                return true;
            }
            else {
                return false;
            }
        }

        return false;

    }

    private function distance($origin, $destination)
    {
        return $this->vincenty_distance($origin, $destination);
    }

    private function vincenty_distance($origin, $destination)
    {
        $earth_radius = 6371;

        list($origin_latitude, $origin_longitude) = explode(',', $origin);
        list($destination_latitude, $destination_longitude) = explode(',', $destination);

        $origin_latitude = deg2rad($origin_latitude);
        $origin_longitude = deg2rad($origin_longitude);
        $destination_latitude = deg2rad($destination_latitude);
        $destination_longitude = deg2rad($destination_longitude);

        $longitude_delta = $destination_longitude - $origin_longitude;

        $distance = round($earth_radius * (atan2(sqrt(pow(cos($destination_latitude) * sin($longitude_delta), 2) + pow(cos($origin_latitude) * sin($destination_latitude) - sin($origin_latitude) * cos($destination_latitude) * cos($longitude_delta), 2)), (sin($origin_latitude) * sin($destination_latitude) + cos($origin_latitude) * cos($destination_latitude) * cos($longitude_delta)))), 2);

        return $distance;
    }


    //Auto Delivered Note Shipment
//    public static function create_delivery_note_old(
//        $hub_id,
//        $selected_route_id,
//        $selected_rider_id,
//        $shipment_ids,
//        $open_box_ids = '',
//        $notification_ids = '',
//        $rider_info_ids = '',
//        $holdInCheck = true,
//        $special_rider_name = null,
//        $special_rider_phone = null,
//        $order_checkbox = false,
//        $operation_rider_type_for_attendance = null
//    )
//    {
////        $holdInCheck = $holdInCheck ?? true; //TO-6892
//
////        if ($hub_id == '') {
////            return redirect()->back()->with('error', 'Hub not found!');
////        }
////
////        if ($selected_route_id == '' && !$holdInCheck) { //TO-6892
////            return redirect()->back()->with('error', 'Route not selected!');
////        }
////
////        if ($selected_rider_id == '') {
////            return redirect()->back()->with('error', 'Rider not selected!');
////        }
//
//        $shipments = explode(',', $shipment_ids);
//
////        if (count($shipments) == 0) {
////            return redirect()->back()->with('error', 'Shipments not entered!');
////        }
//
//        $pending_status = array(154,156);
//
//        $valid_shipments = Shipment::whereIn('id', $shipments)->whereIn('shipper_status_id', $pending_status)->pluck('id');
//
//        $shipments_count = count($valid_shipments);
//
//        if ($shipments_count != 0) {
//            $valid_shipments = $valid_shipments->toArray();
//
//            Shipment::whereIn('id', $valid_shipments)->update(['shipper_status_id' => 5, 'consignee_status_id' => 5]);
//
//            $total_cod_amount = Shipment::whereIn('id', $valid_shipments)->where(function ($query) {
//                $query->where('booking_type_id', '!=', 4)
//                    ->orWhere(function ($sub_query) {
//                        $sub_query->where('booking_type_id', '=', 4)
//                            ->where('charges_mode_id', '=', 2);
//                    });
//            })->sum('amount');
//
//            $open_box_ids = explode(',', $open_box_ids);
//            $notifications = explode(',', $notification_ids);
//            $rider_informations = explode(',', $rider_info_ids);
//            if (Notification::where('id', 40)->where('status', 1)->exists()) {
//                $password = rand(10001, 99999);
//            } else {
//                $password = NULL;
//            }
//            $order = false;
//            if ($order_checkbox) {
//                $order = true;
//            }
//
//
//            $admin = 558; //global admin
//
//            $normal_rider = TRUE;
//
//            $rider = Rider::find($selected_rider_id);
//            if ($rider->special_rider) {
//                $note = DeliveryNote::create([
//                    'hub_id' => $hub_id,
//                    'rider_id' => $selected_rider_id,
//                    'route_id' => $selected_route_id,
//                    'shipments_count' => $shipments_count,
//                    'admin_id' => $admin,
//                    'total_cod_amount' => $total_cod_amount,
//                    'password' => $password,
//                    'last_updated_at' => Carbon::now(),
//                    'special_rider_name' => $special_rider_name,
//                    'special_rider_phone' => $special_rider_phone,
//                    'special_rider' => 1,
//                    'order' => $order
//                ]);
//                $normal_rider = FALSE;
//            } else {
//                $note = DeliveryNote::create([
//                    'hub_id' => $hub_id,
//                    'rider_id' => $selected_rider_id,
//                    'route_id' => empty($selected_route_id) ? '1837' : $selected_route_id,  //TO-6892
//                    'shipments_count' => $shipments_count,
//                    'admin_id' => $admin,
//                    'total_cod_amount' => $total_cod_amount,
//                    'password' => $password,
//                    'last_updated_at' => Carbon::now(),
//                    'ordering' => $order
//                ]);
//            }
//            if ($note) {
//                if (!$order) { //Default
//                    sort($valid_shipments); //sort_valid_shipments;
//                }
//                $serial = 1;
//                foreach ($valid_shipments as $index => $shipment) {
//                    $pos = array_keys($shipments, $shipment);
//                    DeliveryNoteShipment::create([
//                        'delivery_note_id' => $note->id,
//                        'shipment_id' => $shipment,
//                        'notification' => 1,
//                        'rider_information' => 1,
//                        'ordering' => $serial
//                    ]);
//                    $serial++;
//                }
//
//                foreach ($valid_shipments as $index => $shipment) {
//                    if (in_array($shipment, $open_box_ids)) {
//                        $shipment_detail = ShipmentDetail::where('shipment_id', $shipment)->where('is_open', '=', 0)->first();
//                        if ($shipment_detail) {
//                            $shipment_detail->is_open = 1;
//                            $shipment_detail->save();
//                        }
//
//                        $shipment_data = Shipment::find($shipment);
//                        $shipment_data->open_box = 1;
//                        $shipment_data->save();
//
//                        ShipmentOpenBoxJourneyController::add($shipment, 3, Auth::id());
//                    }
//
//                    $old_delivery_note_id = DeliveryNoteShipment::where('shipment_id', $shipment)->where('status', '>', 0)->orderBy('delivery_note_id', 'desc');
//
//                    if ($old_delivery_note_id->exists()) {
//                        $old_delivery_note_id = $old_delivery_note_id->first();
//
//                        if (DeliveryNote::where('id', $old_delivery_note_id->delivery_note_id)->where('status', 0)->exists()) {
//                            $journey = ShipmentsJourney::where('shipment_id', $shipment)->where('verification', 0)->latest()->first();
//                            if ($journey) {
//                                ShipmentsJourneyController::add($journey->shipment_id, $journey->shipper_status_id, $journey->consignee_status_id, $journey->status_reason_id, $journey->remarks, $journey->user_id, Auth::id(), $journey->reference_1_id, NULL, 1, $journey->received_or_refused_by);
//                            }
//                        }
//                    }
//                    ShipmentsJourneyController::add($shipment, 5, 5, NULL, NULL, NULL, $admin, $note->id, $note->rider_id);
//                    $handover_shipments = HandoverShipments::where('shipment_id', $shipment)->whereIn('status', [1, 3]);
//                    if ($handover_shipments->exists()) {
//                        $handover_shipments = $handover_shipments->first();
//                        $handover_shipments->status = 2;
//                        $handover_shipments->save();
//                        $handover_count = HandoverShipments::where('status', 1)->where('handover_id', $handover_shipments->handover_id)->count();
//                        if ($handover_count == 0) {
//                            $handover = Handover::find($handover_shipments->handover_id);
//                            $handover->received_by = $admin;
//                            $handover->received_at = Carbon::now();
//                            $handover->received = $handover->received + 1;
//                            $handover->status_id = 4;
//                            $handover->save();
//                        }
//                        HandoverShipmentJourneyController::add($shipment, $handover_shipments->handover_id, 2);
//                    }
//                }
//
//                foreach ($valid_shipments as $index => $shipment_id) {
//
//                    $shipment_otp = ShipmentOtp::where('shipment_id', $shipment_id);
//                    $dbf_otp = mt_rand(100000, 999999);
//                    if ($shipment_otp->exists()) {
//                        $shipment_otp = $shipment_otp->first();
//                    } else {
//                        $shipment_otp = new ShipmentOtp();
//                        $shipment_otp->shipment_id = $shipment_id;
//                    }
//                    $shipment_otp->dbf_otp = $dbf_otp;
//                    $shipment_otp->rider_id = $selected_rider_id;
//                    $shipment_otp->latitude = null;
//                    $shipment_otp->longitude = null;
//
//                    $pos = array_keys($shipments, $shipment_id);
//                    if ($notifications[$pos[0]]) {
//                        $shipment_obj = Shipment::find($shipment_id);
//                        $otp = mt_rand(100000, 999999);
//                        $shipment_otp->otp = $otp;
//                        $shipment_otp->save();
//                        if(in_array($shipment_obj->user_id, [43066, 41969])) {
//                            NotificationsController::send(244, $note->id, $shipment_id);
//                        }
//                        else if ($shipment_obj->amount == 0) {
//                            //English
////                            NotificationsController::send(132, $note->id, $shipment_id);
////                            //Urdu
////                            NotificationsController::send(135, $note->id, $shipment_id);
//                        } else {
//                            $environment = config('app.env');
//                            if ($environment == 'production' || $environment == 'staging') {
//                                //When Admin Create Delivery Note
//                                $payment_details = PayfastApiCall::ApiCall($note->id, $shipment_id);
//                                $rand = $payment_details['unique_key'];
//                                $payment_link = $payment_details['payment_link'];
//                                $url = $payment_details['url'];
//                                $trans_id = $payment_details['id'];
//                                Log::channel('trax_pay_test')->info('sh '. json_encode($shipment_id, true));
//
//                                CountFintechCharges::dispatch($shipment_id, $payment_link, $rand, $url , $trans_id);
////                                NotificationsController::send(12, $note->id, $shipment_id, $payment_link);
//                            }
//                        }
//                    } else {
//                        $shipment_otp->otp = null;
//                        $shipment_otp->save();
//                    }
//
////                    NotificationsController::send(10, $note->id, $shipment_id);
////                    NotificationsController::send(11, $note->id, $shipment_id);
//
//                }
////                $process_one_link['shipment_ids'] = $valid_shipments;
////                $process_one_link['delivery_note_id'] = $note->id;
////                dispatch(new ProcessOneLinkDeliveryNoteShipment($process_one_link));
////                NotificationsController::send(40, $note->id);
////                if ($normal_rider) {
////                    NotificationsController::app_notification(5, $selected_rider_id, 2, $note->id);
////                }
//            }
//
//            //rider attendance
////            if ($operation_rider_type_for_attendance == 1) {
////                EmployeeAttendanceController::riders_attendance_mark($rider->id);
////            }
//            //rider attendance end
//
//            //todo : update status 1 to 2 (take wo next time jbtk na aae jbtk rider cat ki request dubara na daljae)
////            $rider_bypass_type = RiderCategoryByPass::where('rider_id', $selected_rider_id)->where('status', 1)->select('rider_category_id', 'id')->latest()->first();
////            if ($rider_bypass_type) {
////                $rider_bypass_id = $rider_bypass_type->id;
////                $rider_bypass_update = RiderCategoryByPass::where('rider_id', $selected_rider_id)->where('id', $rider_bypass_id)->update(["status" => 2]);
////            }
//            //todo end
//
//            return true;
//        } else {
//            return false;
//        }
//    }

}
