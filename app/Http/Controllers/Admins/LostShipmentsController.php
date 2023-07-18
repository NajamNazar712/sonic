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
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;



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
                ->leftJoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
                ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
                ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
                ->leftJoin('shipments_journey', function ($join) {
                    $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                        ->where('shipments_journey.id', '=',
                            DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
                })
                ->leftJoin('admins as ad', 'ad.id', '=', 'shipments_journey.admin_id')
                ->leftJoin('shipments_journey as sj', function ($join) {
                    $join->on('sj.shipment_id', '=', 'shipments.id')
                        ->where('sj.id', '=',
                            DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
                })
                ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
//                ->leftJoin('shipment_payment_status as sps', 'sps.id', '=', 'shipments.payment_status_id')
                ->select('shipments.id as shId', 'shipments.tracking_number as tracking_number_link', 'shipments.tracking_number','shipments.user_id as shipper_id', 'u.name as shipper', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'shipments.consignee_name', 'shipments.consignee_phone_number_1 as phone', 'shipments.consignee_address', 'shipments.amount', 'sm.mode as shipping_mode', 'bt.booking_type as service_type', 'ss.name as status', 'ssr.name as reason', 'shipments_journey.remarks as remarks', 'shipments_journey.created_at as status_date', 'shipments_journey.created_at as current_status_date', 'sj.created_at as arrival','shipments.payment_status_id', 'shipments.booking_type_id', 'usi.poc', 'shipments_journey.reference_1_id as reference','ad.name as marked_by')
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

            if(session('role_id') != 1){
                $check_lost_shipments_admins = LostShipmentAdmin::where('admin_id',Auth::id());
                $lost_shipments_shippers_id = LostShipmentShipper::pluck('user_id')->toArray();
                if(!empty($lost_shipments_shippers_id)){
                    if($check_lost_shipments_admins->exists()){
                            $shipments = $shipments->whereIn('shipments.user_id', $lost_shipments_shippers_id);
                    }
                    else{
                            $shipments = $shipments->whereNotIn('shipments.user_id', $lost_shipments_shippers_id);
                    }
                }
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
                    return ['status' => 0, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
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
                    return ['status' => 0, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
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
            $shipment_status_for_bags = array(3,21,26,32,49);
            $status_array = array(1, 5, 11, 14, 17, 21, 23, 25, 26, 28, 30, 31, 32, 34, 36, 37, 38, 49, 50, 51, 56, 60, 61);
            $tracking_number = $request->tracking_number;
            if ($tracking_number != '') {
                $shipment = Shipment::where('tracking_number', $tracking_number)->whereNotIn('shipper_status_id', $status_array);
                if ($shipment->exists()) {
                    $data = array();
                    $shipment = $shipment->first();
                    $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
                    if(!$dispute_check){
                        return ['status' => 0, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
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

                        $cargo_manifest_bag_shipments = CargoManifestBagShipments::where('shipment_id', $shipment->id);
                        if($cargo_manifest_bag_shipments->exists()){
                            $cargo_manifest_bag_shipments = $cargo_manifest_bag_shipments->latest()->first();
                            $bag = CargoManifestBag::find($cargo_manifest_bag_shipments->cargo_manifest_bag_id);
                            if($bag){
                                if(ManifestBagLostShipment::where('bag_id',$bag->id)->where('shipment_id',$shipment->id)->exists()){
                                    return response()->json(['status' => 0, 'error' => 'Shipment already marked lost for the current bag']);
                                }
                            }
                        }

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
        //dd($request);
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
                        //shipment receive huyi phr lost laga (only for manifest)
                        $cargo_manifest_bag_shipments = CargoManifestBagShipments::where('shipment_id', $shipment_details->id);
                        if($cargo_manifest_bag_shipments->exists()){
                            $cargo_manifest_bag_shipments = $cargo_manifest_bag_shipments->latest()->first();
                            $bag = CargoManifestBag::find($cargo_manifest_bag_shipments->cargo_manifest_bag_id);
                            if($bag->status_id != 7){
                                ManifestBagLostShipment::create(['bag_id' => $bag->id,'shipment_id' => $shipment_details->id]);
                                $bag->lost_shipments++;
                                $bag->save();

                                $bag_total_shipments = $bag->shipments;
                                $total_shipments = $bag->lost_shipments +  $bag->received_shipments;

                                if($bag_total_shipments == $total_shipments){
                                    foreach($bag->shipment as $shipment){
                                        $bag_shipment = CargoManifestBagShipments::where('shipment_id',$shipment->shipment_id)->where('status',0)->first();
                                        if($bag_shipment){
                                            $bag_shipment->status = 1;
                                            $bag_shipment->save();
                                        }
                                    }
                                    $bag->status_id = 7;
                                    $bag->completed = 1;
                                    $bag->short_received_shipments = 0;
                                    $bag->received_shipments = $total_shipments;
                                    $bag->receiver_id = 346; //global_admin
                                    $bag->save();

                                    $manifest = ManifestBag::where('cargo_manifest_bag_id',$bag->id)->latest()->first();
                                    if($manifest){
                                        $manifest->status = 1;
                                        $manifest->save();

                                        $cargo_manifest = CargoManifest::find($manifest->cargo_manifest_id);

                                        $total_manifest_bags = $cargo_manifest->bags;
                                        $total_received_manifest_bags = ManifestBag::where('cargo_manifest_id',$manifest->cargo_manifest_id)->where('status',1)->count();

                                        CargoManifestBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, 346, $manifest->cargo_manifest_id);

                                        if($total_manifest_bags == $total_received_manifest_bags){
                                            $cargo_manifest->status_id = 2;
                                            $cargo_manifest->received_by = 346;
                                            $cargo_manifest->received_bags = $total_received_manifest_bags;
                                            $cargo_manifest->save();
                                        }

                                    }
                                }
                                else{
                                    //pehle lost laga phr baaqi ki shipment receive huyi (only for manifest)
                                    $bag_shipment = CargoManifestBagShipments::where('shipment_id',$shipment_details->id)->where('status',0)->first();
                                    if($bag_shipment){
                                        $bag_shipment->status = 1;
                                        $bag_shipment->save();
                                    }
                                }
                            }
                        }
                    }

                    $shipment_details->shipper_status_id = 18;
                    $shipment_details->save();
                    ShipmentsJourneyController::add($shipment_details->id,18,NULL,NULL, $remarks[$shipment_details->id],NULL,Auth::id());
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
    
    // Heading: N/A
    // Siderbar:  N/A
    // URL: admin/delivery/lost/add/bulk/lost/shipments
    // Description: This function is used to upload excel file for bulk lost shipments.
    public function bulk_lost_shipments(Request $request)
    {
        $status_array = array(1, 5, 11, 14, 17, 21, 23, 25, 26, 28, 30, 31, 32, 34, 36, 37, 38, 49, 50, 51, 56, 60, 61);
        $names = [
            'tracking_number' => 'Tracking Number',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
        ];
        $rules = [
            'tracking_number' => ['required', 'max:300',
            'integer', Rule::exists('shipments', 'tracking_number')->where(function ($query)use($status_array) {
                $query->whereNotIn('shipper_status_id', $status_array);
            })],
        ];
        $fields = [0 => 'tracking_number'];
        if ($file = $request->file('excel')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();
            // dd($spreadsheet);
        

            $header = ['Tracking Number'];

            if (isset($spreadsheet)) {
                $header_correct = TRUE;
                foreach ($spreadsheet[0] as $index => $header_value) {

                    if ($index == 1) {
                    } elseif (!isset($header[$index]) || $header_value != $header[$index]) {
                        $header_correct = FALSE;

                        break;
                    }
                }


                if (!$header_correct) {
                    
                    return response()->json(['status' => 2, 'error' => 'Invalid Columns, Kindly follow the Template provided']);
                    // return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
                } else {
                    unset($spreadsheet[0]);
                }
            }

            if (!empty($spreadsheet) || !isset($spreadsheet)) {
                $rows = array();
                
                foreach ($spreadsheet as $spreadsheet_row) {
                    $row = array();
                    
                    foreach ($spreadsheet_row as $key => $value) {
                        if($value != null)
                        {
                            $row[$fields[$key]] = $value;
                        }
                       
                    }

                    $rows[] = $row;
                }
                $rows = array_filter($rows);
                
                unset($spreadsheet);
                $errors = array();
                $error_count = 0;
                $tracking_ids = array();
                $tracking_id_row = array();
                    
                foreach ($rows as $key => $row) {
                    $row_id = $key + 2;
                    // dd($row['tracking_number']);
                    $validate = Validator::make($row, $rules, $messages);
                    
                    $validate->setAttributeNames($names);

                    if ($validate->fails()) {
                        
                        $errors['Row #' . $row_id] = $validate->errors()->all();
                    }
                }
                //if (empty($errors)) {
                    $tracking_numbers = array();
                    $shipment_ids = array();
                    $check = array();
                    $data = array(); 
                    $error = array();
                    foreach ($rows as $key => $row) {
                        $row_id = $key + 2;
                        $tracking = trim($row['tracking_number']);
                        $shipment = Shipment::where('tracking_number', $tracking)
                        ->whereNotIn('shipper_status_id', $status_array)->first();
                        // dd($shipment);
                        if ($shipment) {
                           
                            $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
                            if(!$dispute_check){
                                return ['status' => 0, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
                            }
                            $journey=  ShipmentsJourney::where('shipment_id',$shipment->id)->latest('id')->first();
                            if($journey){
                                $verification = $journey->verification;
                                if($verification == 0){
                                return response()->json(['status' => 0, 'error' => 'Shipment is unverified!']);
                                }
                            }
                            if($shipment->shipper_status_id == 5){
                                return response()->json(['status' => 0, 'error' => 'Shipment is Out for Delivery !']);
                            } 
                            if($shipment->shipper_status_id != 18) {
                                $cargo_manifest_bag_shipments = CargoManifestBagShipments::where('shipment_id', $shipment->id);
                                if($cargo_manifest_bag_shipments->exists()){
                                    $cargo_manifest_bag_shipments = $cargo_manifest_bag_shipments->latest()->first();
                                    $bag = CargoManifestBag::find($cargo_manifest_bag_shipments->cargo_manifest_bag_id);
                                    if($bag){
                                        if(ManifestBagLostShipment::where('bag_id',$bag->id)->where('shipment_id',$shipment->id)->exists()){
                                            return response()->json(['status' => 0, 'error' => 'Shipment already marked lost for the current bag']);
                                        }
                                    }
                                }
                            }
                            $data[$shipment->id]['id'] = $shipment->id;
                            $data[$shipment->id]['tracking_number'] = $shipment->tracking_number;
                            $data[$shipment->id]['shipper_name'] = $shipment->user->name.' (' . $shipment->pickup_address->poc . ')';
                            $data[$shipment->id]['origin'] = $shipment->consignee_city->name;
                            $data[$shipment->id]['destination'] = $shipment->pickup_address->city->name;
                            $data[$shipment->id]['hub'] = $shipment->pickup_address->city->hub_city->name;
                            $data[$shipment->id]['amount'] = number_format($shipment->amount);
                            $data[$shipment->id]['mode'] = $shipment->shipping_mode->mode;
                            $data[$shipment->id]['service_type'] = $shipment->booking_type->booking_type;
                            $data[$shipment->id]['remarks'] = '<input class="form-control form-control-sm" name="remarks[' . $shipment->id. ']" placeholder="Enter Remarks">';

                            $tracking_numbers['Row #' . $row_id] = $tracking;
                            ShipmentScanningJourneyController::add($shipment->id, 11, 1, Auth::id(), null,null);
                           
                        }
                        else
                        {   
                            $error[$row_id]['tracking_number'] = $tracking;
                            $error[$row_id]['error_msg'] = "These Tracking Numbers are Invalid";
                            
                        }
                        // dd($data);
                       
                        
                    }  
                    $error_count =  count($error);
                    
                    return response()->json(['status' => 1, 'details' => $data ,'error_count' => $error_count , 'error' => $error]);
                    // dd($data);
                    // return redirect()->back()->with(['success' => 'Bulk Lost Update']);

                // } 
                // else {
                //     dd($rows);
                //     $error['tracking_number'] = $rows['tracking_number'];
                //     $error['error_msg'] = "These Tracking Numbers are Invalid";
                //     dd($rows[$key]['tracking_number']);
                //     // $errors = array_map(function ($row, $errors) {
                //     //     return $row['tracking_number'] . ':' . PHP_EOL . implode(' | ', $errors);
                //     // }, array_keys($errors), $errors);
                    
                //     $errors = array_map(function ($key, $errors) use ($rows) {
                    

                //         if (isset($rows[$key]['tracking_number'])) {
                //             return $rows[$key]['tracking_number'] . ':' . PHP_EOL . implode(' | ', $errors);
                //         } else {
                //             return 'Undefined tracking number' . ':' . PHP_EOL . implode(' | ', $errors);
                //         }
                //     }, array_keys($errors), $errors);
                //     dd($errors);

                //     return redirect()->back()->withErrors($errors); 
                // }
            } else {
              
                // dd("1");
                return response()->json(['status' => 3, 'error' => 'No Shipments in File']);
                // return redirect()->back()->with('error', 'No Shipments in File');
            }
        }
      
    }
}
