<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\CargoManifestBagJourneyController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\CargoManifest\CargoManifest;
use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagJourney;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;
use App\Http\Models\Admin\CargoManifest\ManifestBag;
use App\Http\Models\BookingType;
use App\Http\Models\CargoConsignmentShipment;
use App\Http\Models\CargoConsignment;
use App\Http\Models\ManifestBagLostShipment;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\PackagingMaterialRequestHistory;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShipmentStatusReason;
use App\Http\Models\ShippingMode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\LostShipmentAdmin;
use App\Http\Models\Admin\LostShipmentShipper;

class LostShipmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function lost_shipments_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),48);
        $shipment_status = ShipmentStatus::select('id', 'name')->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        $return_confirm_reasons = ShipmentStatusReason::whereIn('id', [2, 5, 8, 9, 10, 12, 19, 20, 34, 38, 39, 40, 41, 42])->select('id', 'name')->get();
        return view('admin.lost.index')->with(['shipment_status' => $shipment_status, 'shipping_mode' => $shipping_mode, 'service_type' => $service_type, 'return_confirm_reasons' => $return_confirm_reasons]);
    }
    public function lost_shipments_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),108);
        }
            $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
                ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
                ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
                ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
                ->join('cities as h', 'dc.hub_id', '=', 'h.id')
                ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
                ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
                ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
                ->leftJoin('shipments_journey', function ($join) {
                    $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                        ->where('shipments_journey.id', '=',
                            DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
                })
                ->leftJoin('shipments_journey as sj', function ($join) {
                    $join->on('sj.shipment_id', '=', 'shipments.id')
                        ->where('sj.id', '=',
                            DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
                })
                ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
//                ->leftJoin('shipment_payment_status as sps', 'sps.id', '=', 'shipments.payment_status_id')
                ->select('shipments.id as shId', 'shipments.tracking_number as tracking_number_link', 'shipments.tracking_number','shipments.user_id as shipper_id', 'u.name as shipper', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'shipments.consignee_name', 'shipments.consignee_phone_number_1 as phone', 'shipments.consignee_address', 'shipments.amount', 'sm.mode as shipping_mode', 'bt.booking_type as service_type', 'ss.name as status', 'ssr.name as reason', 'shipments_journey.remarks as remarks', 'shipments_journey.created_at as status_date', 'shipments_journey.created_at as current_status_date', 'sj.created_at as arrival','shipments.payment_status_id', 'shipments.booking_type_id', 'usi.poc', 'shipments_journey.reference_1_id as reference')
//                ->whereRaw('IF (shipments.payment_status_id != NULL, (shipments.payment_status_id > 1), TRUE)')
                ->where('shipments.shipper_status_id', 18);
                // ->where(function ($sub_query) {
                //     $sub_query->where('shipments.payment_status_id', '=', null)
                //         ->orWhere('shipments.payment_status_id', '>', 1);
                // });

//                ->where(function ($sub_query) {
//                    $sub_query->where('shipments.payment_status_id', '=', null);
//                })
//                ->orWhere(function ($sub_query) {
//                    $sub_query->where('shipments.payment_status_id', '>', 1);
//                });

            if (session('role_id') != 1) {
                $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
            }

            $check_lost_shipments_admins = LostShipmentAdmin::where('admin_id',Auth::id());
            if($check_lost_shipments_admins->exists() || session('role_id') == 1){
                $lost_shipments_shippers_id = LostShipmentShipper::pluck('user_id')->toArray();
                $shipments = $shipments->whereIn('shipments.user_id', $lost_shipments_shippers_id);

            }else{
                $lost_shipments_shippers_id = LostShipmentShipper::pluck('user_id')->toArray();
                $shipments = $shipments->whereNotIn('shipments.user_id', $lost_shipments_shippers_id);
            }

            return Datatables::of($shipments)
                ->editColumn('tracking_number_link', function ($shipments) {
                    $route = route('admin.tracking.index');
                    return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
                })
                
                ->editColumn('arrival', function ($shipments) {
                    if ($shipments->arrival) {
                        return $shipments->arrival;
                    } else {
                        return " - ";
                    }
                })
                ->editColumn('reference', function ($shipments) {
                    if ($shipments->reference) {
                        return str_pad($shipments->reference, 6, '0', STR_PAD_LEFT);
                    } else {
                        return " - ";
                    }
                })
                ->editColumn('remarks', function ($shipments) {
                    if ($shipments->remarks) {
                        return $shipments->remarks;
                    } else {
                        return " - ";
                    }
                })
                ->editColumn('amount', function($shipment){
                    return number_format($shipment->amount);
                })
                ->editColumn('shipper', function ($shipment) {
                    if ($shipment->booking_type_id == 4) {
                        return $shipment->shipper .' (' . $shipment->poc . ')';
                    }
                    else {
                        return $shipment->shipper;
                    }
                })
                ->addColumn('aging',function ($shipment){
                    if(session('role_id') == 1) {
                        return 0;
                    }
                    else {
                        if (LostShipmentShipper::where('user_id', $shipment->shipper_id)->exists()) {
                            return 0;
                        }
                        else {
                            $today = Carbon::now();
                            return $today->diffInDays($shipment->status_date);
                       }
                     }
                })
                ->filterColumn('u.name', function ($query, $keyword) {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.booking_type_id', '!=', 4)
                            ->where('u.name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('shipments.booking_type_id', '=', 4)
                                ->where('usi.poc', 'like', '%' . $keyword . '%');
                        });
                })
                ->orderColumn('u.name', 'u.name $1, usi.poc $1')
                ->filterColumn('status', function ($query, $keyword) {

                    if ($keyword != '') {
                        $query->where('ss.id', $keyword);
                    } else {
                        $query->whereRaw('false');
                    }
                })
                ->filterColumn('shipping_mode', function ($query, $keyword) {

                    if ($keyword != '') {
                        $query->where('sm.id', $keyword);
                    } else {
                        $query->whereRaw('false');
                    }
                })
                ->filterColumn('service_type', function ($query, $keyword) {

                    if ($keyword != '') {
                        $query->where('bt.id', $keyword);
                    } else {
                        $query->whereRaw('false');
                    }
                })
                ->make(true);
    }
    public function shipment_confirm_status(Request $request){ //update to status 20 for confirm and 13 for re-attempt

        $shipment_ids = $request->shipment_ids;
            foreach ($shipment_ids as $shipment){
                
                $parcel = Shipment::find($shipment);
                $dispute_check = CheckDisputeShipmentsController::check($parcel->id);
                if(!$dispute_check){
                    return ['status' => 0, 'error' => 'Shipment is in Dispute, please resolve dispute first!'];
                }
                if($parcel->shipper_status_id == 18) {
                    
                    $lost_shipments_shippers = LostShipmentShipper::where('user_id',$parcel->user_id);
                    if($lost_shipments_shippers->exists()){

                        $lost_shipments_admins = LostShipmentAdmin::where('admin_id',Auth::id());
                        if($lost_shipments_admins->exists()){
//                    if (!$parcel->packaging_material_request) {
                        Shipment::where('id', $shipment)->update(['shipper_status_id' => 20, 'consignee_status_id' => 20]);
                        ShipmentChargesController::return ($shipment);
                        ShipmentsJourneyController::add($shipment, 20, 20, $request->reason, NULL, NULL, Auth::id());

                        AdminFinanceController::add_payment($shipment, 1);
//                    } else {
//
//                        Shipment::where('id', $shipment)->update(['shipper_status_id' => 17, 'consignee_status_id' => 17]);
//                        ShipmentsJourneyController::add($shipment, 17, 17, NULL, NULL, NULL, Auth::id());
//                    }
                        }

                    }else{
//                    if (!$parcel->packaging_material_request) {
                        Shipment::where('id', $shipment)->update(['shipper_status_id' => 20, 'consignee_status_id' => 20]);
                        ShipmentChargesController::return ($shipment);
                        ShipmentsJourneyController::add($shipment, 20, 20, $request->reason, NULL, NULL, Auth::id());

                        AdminFinanceController::add_payment($shipment, 1);
//                    } else {
//
//                        Shipment::where('id', $shipment)->update(['shipper_status_id' => 17, 'consignee_status_id' => 17]);
//                        ShipmentsJourneyController::add($shipment, 17, 17, NULL, NULL, NULL, Auth::id());
//                    }
                    }


                }
            }
            return ['status'=>1,'success'=>"Shipment successfully updated as ( Return Confirm )"];
    }
    public function shipment_reattempt_status(Request $request){ //update to status 20 for confirm and 13 for re-attempt

        $shipment_ids = $request->shipment_ids;

        foreach ($shipment_ids as $shipment){
                $parcel = Shipment::find($shipment);

                $dispute_check = CheckDisputeShipmentsController::check($parcel->id);
                if(!$dispute_check){
                    return ['status' => 0, 'error' => 'Shipment is in Dispute, please resolve dispute first!'];
                }
                if($parcel->shipper_status_id == 18) {

                    $lost_shipments_shippers = LostShipmentShipper::where('user_id',$parcel->user_id);

                    if($lost_shipments_shippers->exists()){

                        $lost_shipments_admins = LostShipmentAdmin::where('admin_id',Auth::id());
                        if($lost_shipments_admins->exists()){
                            Shipment::where('id', $shipment)->update(['shipper_status_id' => 13, 'consignee_status_id' => 13]);
                            ShipmentsJourneyController::add($shipment, 13, 13, NULL, $request->remarks, NULL, Auth::id());
                            if($parcel->packaging_material_request == 1) {
                                $packaging_material_shipment = PackagingMaterialRequest::where('tracking_number', $parcel->tracking_number)->first();
                                if ($packaging_material_shipment != null) {
                                    $packaging_material_shipment->status_id = 3;
                                    $packaging_material_shipment->save();
        
                                    $packaging_request_history = new PackagingMaterialRequestHistory();
                                    $packaging_request_history->packaging_material_request_id = $packaging_material_shipment->id;
                                    $packaging_request_history->status = 3;
                                    $packaging_request_history->updated_by = \Illuminate\Support\Facades\Auth::id();
                                    $packaging_request_history->save();
                                }
                            }
                        }
                    }else{
                        Shipment::where('id', $shipment)->update(['shipper_status_id' => 13, 'consignee_status_id' => 13]);
                        ShipmentsJourneyController::add($shipment, 13, 13, NULL, $request->remarks, NULL, Auth::id());
                        if($parcel->packaging_material_request == 1) {
                            $packaging_material_shipment = PackagingMaterialRequest::where('tracking_number', $parcel->tracking_number)->first();
                            if ($packaging_material_shipment != null) {
                                $packaging_material_shipment->status_id = 3;
                                $packaging_material_shipment->save();
    
                                $packaging_request_history = new PackagingMaterialRequestHistory();
                                $packaging_request_history->packaging_material_request_id = $packaging_material_shipment->id;
                                $packaging_request_history->status = 3;
                                $packaging_request_history->updated_by = \Illuminate\Support\Facades\Auth::id();
                                $packaging_request_history->save();
                            }
                        }
                    }

                    
                }
            }
            return ['status'=>1,'success'=>"Shipment successfully updated as ( Re-Attempt )"];


    }

    public function lost_add_index(){
        return view('admin.lost.add_shipments');
    }
    public function get_shipment_info(Request $request)
    {
            $passing_status_array = array(1, 5, 11 , 14, 17, 25, 30, 31, 60);
            $tracking_number = $request->tracking_number;
            if ($tracking_number != '') {
                $shipment = Shipment::where('tracking_number', $tracking_number)->whereNotIn('shipper_status_id', $passing_status_array);
                if ($shipment->exists()) {
                    $data = array();
                    $shipment = $shipment->first();
                    $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
                    if(!$dispute_check){
                        return ['status' => 0, 'error' => 'Shipment is in Dispute, please resolve dispute first!'];
                    }
                    $journey=  ShipmentsJourney::where('shipment_id',$shipment->id)->latest('id')->first();
                    if($journey)
                    {
                        $verification = $journey->verification;
                        if($verification == 0)
                        {
                            return response()->json(['status' => 0, 'error' => 'Shipment is unverified!']);
                        }

                    }
                    if($shipment->shipper_status_id == 5)
                    {
                        return response()->json(['status' => 0, 'error' => 'Shipment is Out for Delivery !']);
                    }

                    if($shipment->shipper_status_id != 18) {

                        $data['id'] = $shipment->id;
                        $data['tracking_number'] = $shipment->tracking_number;
                        $data['shipper_name'] = $shipment->user->name.' (' . $shipment->pickup_address->poc . ')';
                        $data['origin'] = $shipment->consignee_city->name;
                        $data['destination'] = $shipment->pickup_address->city->name;
                        $data['hub'] = $shipment->pickup_address->city->hub_city->name;
                        $data['amount'] = number_format($shipment->amount);
                        $data['mode'] = $shipment->shipping_mode->mode;
                        $data['service_type'] = $shipment->booking_type->booking_type;
                        $data['remarks'] = '<input class="form-control form-control-sm" name="remarks[' . $shipment->id. ']" placeholder="Enter Remarks">';

                        ShipmentScanningJourneyController::add($shipment->id, 11, 1, Auth::id(), null,null);
                        return response()->json(['status' => 1, 'details' => $data]);
                    }
                    else
                        {
                            return response()->json(['status' => 0, 'error' => 'Shipment already added to lost shipments!']);

                        }
                } else {
                    return response()->json(['status' => 0, 'error' => 'Shipment can not be added to lost!']);
                }

            }
    }
    public function add_lost_shipments(Request $request){

        $passing_status_array = array(1,14,17,18,25,31,38);
        $shipment_status_for_bags = array(3,21,26,32,49);
        $shipments = explode(',', $request->shipment_ids);
        $remarks = $request->remarks;
        $lost_shipments_array = array();
        if(!empty($shipments)){
            foreach ($shipments as $shipment) {
                $shipment_details = Shipment::where('id', $shipment)->whereNotIn('shipper_status_id', $passing_status_array);
                if ($shipment_details->exists()) {
                    $shipment_details = $shipment_details->first();

                    $journey=  ShipmentsJourney::where('shipment_id',$shipment_details->id)->latest('id')->first();
                    if($journey)
                    {
                        $verification = $journey->verification;
                        if($verification == 0)
                        {
                            return response()->json(['status' => 0, 'error' => 'Shipment is unverified!']);
                        }
                    }

                    if(in_array($shipment_details->shipper_status_id,$shipment_status_for_bags)){
                        $cargo_manifest_bag_shipments = CargoManifestBagShipments::where('shipment_id', $shipment_details->id);
                        if($cargo_manifest_bag_shipments->exists()){
                            $cargo_manifest_bag_shipments = $cargo_manifest_bag_shipments->latest()->first();
                            $bag = CargoManifestBag::find($cargo_manifest_bag_shipments->cargo_manifest_bag_id);
                            if($bag){
                                ManifestBagLostShipment::create(['bag_id' => $bag->id,'shipment_id' => $shipment_details->id]);
                                $bag->lost_shipments++;
                                $bag->save();
                            }
                        }
                    }

                    $shipment_details->shipper_status_id = 18;
                    $shipment_details->save();
                    ShipmentsJourneyController::add($shipment_details->id,18,NULL,NULL, $remarks[$shipment],NULL,Auth::id());
                    $lost_shipments_array[] = $shipment;
                }
            }
            if(count($lost_shipments_array) > 0){
                NotificationsController::send(150, $lost_shipments_array);
            }
            return redirect()->back()->with(['success' => 'Shipment(s) has been added to Lost!']);

        }
        else{
            return redirect()->back()->with(['error' => 'Shipment not selected!']);
        }
    }
}
