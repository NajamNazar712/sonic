<?php

namespace App\Http\Controllers\Admins;
use App\Http\Controllers\CargoManifestBagJourneyController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\CargoManifest\CargoManifest;
use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;
use App\Http\Models\Admin\CargoManifest\ManifestBag;
use App\Http\Models\International\InternationalShipmentServiceProvider;
use App\Http\Models\InternationalShipment;
use App\Http\Models\InternationalShipmentsLog;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\PODImage;

class AdminInternationalShipmentsController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function tracking_upload_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),53);
        $service_providers = InternationalShipmentServiceProvider::all();
        return view('admin.international.tracking_upload')->with(['service_providers' => $service_providers]);
    }
    public function tracking_upload_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),113);
        }
        $shipments = InternationalShipment::join('shipments', 'shipments.id', '=', 'international_shipments.shipment_id')
        ->leftjoin('international_shipment_service_providers as issp', 'issp.id', '=', 'international_shipments.service_provider_id')
            ->select('shipments.id as shipment_id', 'shipments.tracking_number','international_shipments.international_tracking_number','international_shipments.postal_code','shipments.created_at as booking_date','international_shipments.actual_weight as actual_weight', 'issp.name as provider','international_shipments.seal_number');

        $datatables = Datatables::of($shipments)
            ->addColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->addColumn('pod_file', function ($shipments) {
                $pod_image = PODImage::where('shipment_id',$shipments->shipment_id);
                if($pod_image->exists()){
                    $pod_image = asset('uploads/pod_images/' . $pod_image->latest()->first()->pod_file);
                    return '<a class="btn btn-sm btn-outline-info align-middle pod_file_view" href="'.$pod_image.'" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                }
            })
            ->addColumn('action',function ($shipments) {

                $pod_file = PODImage::where('shipment_id' , $shipments->shipment_id);

                if ($pod_file->exists()) {
                    $dropdown = '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                                <button type="button" class="dropdown-item remove"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
                                <button type="button" class="dropdown-item replace_pod" data-target-id="' . $shipments->shipment_id . '" data-toggle="modal" data-target="#ReplacePOD"><i class="ft-plus-circle"></i> Replace POD</button>
                            </div>
                        </div>
                    ';
                }else{
                    $shipment_check = Shipment::find($shipments->shipment_id);
                    if($shipment_check->shipper_status_id == 14){
                        $dropdown = '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                                <button type="button" class="dropdown-item remove"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
                                <button type="button" class="dropdown-item upload_pod" data-target-id="' . $shipments->shipment_id . '" data-toggle="modal" data-target="#UploadPOD"><i class="ft-plus-circle"></i> Upload POD</button>
                            
                                </div>
                        </div>
                    ';
                    }else{
                        $dropdown = '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                                <button type="button" class="dropdown-item remove"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
                            </div>
                        </div>
                    ';
                    }
                   
                }

                return $dropdown;
            });
        return $datatables->make(true);
    }

    public function tracking_upload_store(Request $request){

        $names = [
            'tracking_number' => 'Tracking Number',
            'international_tracking_number' => 'Tracking Number',
            'actual_weight' => 'Actual Weight',
            'service_provider_id' => 'Service Provider',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
            'exists' => 'Given :attribute is Invalid / not ready for update.',
        ];
        $rules = [
            'tracking_number' => ['required', 'integer', Rule::exists('shipments', 'tracking_number')->where(function($query){
                $query->whereIn('shipper_status_id', [2,3,4,5,6,7,8,9,10,11,12,13,15,18,49,51,52,54,55,56]);
            })],
            'international_tracking_number' => ['required'],
            'actual_weight' => ['required', 'numeric', 'between:0.1,100000'],
            'service_provider_id' => ['required', 'numeric', 'between:1,7', 'exists:international_shipment_service_providers,id']
        ];


        if($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number', 'International Tracking Number', 'Actual Weight', 'Service Provider'];

            if (isset($spreadsheet)) {
                if (count($spreadsheet[0]) == 4){
                    $fields = [0 => 'tracking_number', 1 => 'international_tracking_number', 2 => 'actual_weight', 3 => 'service_provider_id'];
                }
                else{
                    return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
                }
                unset($spreadsheet[0]);
            }

            if (!empty($spreadsheet) || !isset($spreadsheet)) {
                $rows = array();
                foreach ($spreadsheet as $spreadsheet_row) {
                    $row = array();

                    foreach ($spreadsheet_row as $key => $value) {
                        $row[$fields[$key]] = $value;
                    }

                    $rows[] = $row;
                }

                unset($spreadsheet);
                $errors = array();
                $tracking_ids = array();
                $tracking_id_row = array();
                foreach ($rows as $key => $row) {
                    $row_id = $key + 2;

                    $validate = Validator::make($row, $rules, $messages);

                    $validate->setAttributeNames($names);

                    if ($validate->fails()) {
                        $errors['Row #' . $row_id] = $validate->errors()->all();
                    }
                    if (empty($errors['Row #' . $row_id])) {
                        if (!empty(trim($row['tracking_number']))) {
                            if (empty($tracking_ids)) {
                                $tracking_ids[] = $row['tracking_number'];
                                $tracking_id_row[$row['tracking_number']] = $row_id;
                            }
                            else {
                                if (in_array($row['tracking_number'], $tracking_ids)) {
                                    $errors['Row #' . $row_id][] = 'Same Tracking Number as of Row #' . $tracking_id_row[$row['tracking_number']];
                                }
                                else {
                                    $tracking_ids[] = $row['tracking_number'];
                                    $tracking_id_row[$row['tracking_number']] = $row_id;
                                }
                            }
                        }
                        if (!Shipment::where('tracking_number', $row['tracking_number'])->exists()) {
                            $errors['Row #' . $row_id][] = 'Shipment is already updated from Booked Status #' . $row['tracking_number'];
                        }
                        $shipment = Shipment::where('tracking_number', $row['tracking_number']);
                        if($shipment->exists()){
                            $shipment = $shipment->first();
                            if(InternationalShipment::where('shipment_id', $shipment->id)->whereNotNull('international_tracking_number')->exists()){
                                $errors['Row #' . $row_id][] = 'International Tracking Number is already added #' . $row['tracking_number'];
                            }
                        }
                    }
                }
                if(empty($errors)){
                    $tracking_numbers = array();
                    foreach ($rows as $key => $row) {
                        $row_id = $key + 2;
                        $tracking = trim($row['tracking_number']);
                        $international_tracking_number = trim($row['international_tracking_number']);
                        $international_shipment_weight = $row['actual_weight'];
                        $service_provider_id = $row['service_provider_id'];
                        $shipment_details = Shipment::where('tracking_number',$tracking)->first();
                        $shipment_id = $shipment_details->id;
                        $international_shipment = InternationalShipment::where('shipment_id', $shipment_id);
                        if($international_shipment->exists()){
                            $international_shipment = $international_shipment->first();
                            $international_shipment->international_tracking_number = $international_tracking_number;
                            $international_shipment->actual_weight = $international_shipment_weight;
                            $international_shipment->sync = 1;
                            $international_shipment->service_provider_id = $service_provider_id;
                            $international_shipment->save();

                            if($international_shipment_weight != NULL){
                                $shipment_details->actual_weight = $international_shipment_weight;
                                $shipment_details->save();

                                if($shipment_details->packaging_material_request == 0 && $shipment_details->shipment_type == 1){
                                    if($shipment_details->booking_type_id != 4){
                                        ShipmentChargesController::weight($shipment_id);
                                        ShipmentChargesController::international_fuel_surcharge($shipment_id);
                                    }
                                }

                            }
                            $this->shipment_update_log($shipment_id, Auth::id(), $international_shipment_weight);
                        }
                        $tracking_numbers['Row #' . $row_id] = $tracking;

                    }
                    $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                        return $row . ': ' . $tracking_number;
                    }, array_keys($tracking_numbers), $tracking_numbers));

                    return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipment(s) Updated with Tracking Number(s):' . PHP_EOL . $tracking_numbers]);
                }
                else{
                    $errors = array_map(function ($row, $errors) {
                        return $row . ':' . PHP_EOL . implode(' | ', $errors);
                    }, array_keys($errors), $errors);

                    return redirect()->back()->withErrors($errors);
                }

            }
            else {
                return redirect()->back()->with('error', 'No Shipments in File');
            }

        }
    }

    public function tracking_upload_edit_info(Request $request){
        $shipment_id = $request->shipment_id;
        if($shipment_id){
            $international_shipment = InternationalShipment::where('shipment_id', $shipment_id);
            if($international_shipment->exists()){
                $international_shipment = $international_shipment->first();
                $shipment = Shipment::find($shipment_id);
                $details = array();
                $details['id'] = $international_shipment->id;
                $details['tracking_number'] = $shipment->tracking_number;
                $details['shipment_status'] = $shipment->shipper_status_id;
                $details['actual_weight'] = $international_shipment->actual_weight;
                $details['international_tracking_number'] = $international_shipment->international_tracking_number;
                return response()->json(['status' => 0, 'details' => $details]);
            }
        }
        return response()->json(['status' => 1, 'error' => 'Please select a shipment!']);
    }

    public function tracking_upload_edit(Request $request){
        $international_shipment_id = $request->international_shipment_id;
        $actual_weight = $request->actual_weight;
        $tracking_number = $request->tracking_number;
        $international_tracking_number = $request->international_tracking_number;
        $service_provider_id = $request->service_provider;

        if($international_shipment_id){
            $international_shipment = InternationalShipment::find($international_shipment_id);
            if($international_shipment){
                if($tracking_number){
                    $shipment = Shipment::where('tracking_number', $tracking_number)->first();
                    if($shipment){
                        $shipment_id = $shipment->id;
                        $international_shipment->shipment_id = $shipment_id;
                        $international_shipment->international_tracking_number = $international_tracking_number;
                        $international_shipment->actual_weight = $actual_weight;
                        $international_shipment->sync = 1;
                        $international_shipment->service_provider_id = $service_provider_id;
                        $international_shipment->save();
                        $shipment->actual_weight = $actual_weight;
                        $shipment->save();

                        $this->shipment_update_log($shipment->id, Auth::id(), $actual_weight);
                        return redirect()->back()->with('success', 'Shipment successfully updated!');
                    }
                    return redirect()->back()->with('error', 'Shipment with this tracking number Not found!');
                }
                return redirect()->back()->with('error', 'Tracking Number missing!');
            }
            return redirect()->back()->with('error', 'Shipment Not found in International Shipments!');
        }
        return redirect()->back()->with('error', 'International Shipment not selected!');
    }

    public function shipment_status_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),441);
        $shipment_status = ShipmentStatus::where('status', 1)->whereIn('id', [4,14,17,18,21,22,23,24,25])->get();
        return view('admin.international.shipment_status')->with(['shipment_status' => $shipment_status]);
    }

    public function get_shipment_info(Request $request)
    {
        $tracking_number = $request->tracking_number;
        $default_status_id = $request->default_status_id;
        if ($tracking_number != '') {
            if($default_status_id == 0){
                $shipment = Shipment::where('tracking_number', $tracking_number)->whereNotIn('shipper_status_id', [1, 2, 17])->where('business_category_id', 2);
                if ($shipment->exists()) {
                    /*$bag = CargoManifestBag::where('seal_number',$tracking_number);
                    if($bag->exists()){
                        $bag = $bag->first();
                        if($bag->status_id == 1 && $bag->type == 1 && !ManifestBag::where('cargo_manifest_bag_id',$bag->id)->exists()){
                            return response()->json(['status' => 0, 'error' =>'Please Create Manifest For This Shipment!']);
                        }
                    }*/

                    $data = array();
                    $shipment = $shipment->first();

                    if($shipment->shipper_status_id == 14){
                        return response()->json(['status' => 0, 'error' => 'Shipment is already delivered!']);
                    }
                    $data['id'] = $shipment->id;
                    $data['tracking_number'] = $shipment->tracking_number;
                    $data['shipper_name'] = $shipment->user->name.' (' . $shipment->pickup_address->poc . ')';
                    $data['origin'] = $shipment->pickup_address->city->name;
                    $data['destination'] = $shipment->consignee_city->name;
                    $data['status'] = $shipment->status_shipper->name;
                    $data['status_id'] = $shipment->shipper_status_id;

                    ShipmentScanningJourneyController::add($shipment->id, 17, 1, Auth::id(), null,null);
                    return response()->json(['status' => 1, 'details' => $data]);

                } else {
                    return response()->json(['status' => 0, 'error' => 'Shipment is Cancelled OR not International Shipment OR with different status!']);
                }
            }
            else{
                $shipment = Shipment::where('tracking_number', $tracking_number)->whereNotIn('shipper_status_id', [1, 2, 17])->where('shipper_status_id', $default_status_id)->where('business_category_id', 2);
                if ($shipment->exists()) {
                    $data = array();
                    $shipment = $shipment->first();

                    if($shipment->shipper_status_id == 14){
                        return response()->json(['status' => 0, 'error' => 'Shipment is already delivered!']);
                    }

                    $data['id'] = $shipment->id;
                    $data['tracking_number'] = $shipment->tracking_number;
                    $data['shipper_name'] = $shipment->user->name.' (' . $shipment->pickup_address->poc . ')';
                    $data['origin'] = $shipment->consignee_city->name;
                    $data['destination'] = $shipment->pickup_address->city->name;
                    $data['status'] = $shipment->status_shipper->name;
                    $data['status_id'] = $shipment->shipper_status_id;
                    ShipmentScanningJourneyController::add($shipment->id, 17, 1, Auth::id(), null,null);
                    return response()->json(['status' => 1, 'details' => $data]);

                } else {
                    return response()->json(['status' => 0, 'error' => 'Shipment is Cancelled OR not International Shipment OR with different status!']);
                }
            }

        }
        return response()->json(['status' => 0, 'error' => 'Tracking number empty!']);

    }

    public function shipment_status_update(Request $request){
        $shipper_status_id = $request->shipment_status_id;
        $in_transit_array = array();
        $updated_array = array();
        if($shipper_status_id){
            $shipments = explode(',', $request->shipment_ids);
            if(count($shipments) > 0){
                if($shipper_status_id == 14){
                    foreach ($shipments as $shipment_id){
                        $shipment = Shipment::where('id', $shipment_id)->first();
                        if($shipment->shipper_status_id != 3) {
                            $shipment->update(['shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $shipper_status_id]);
                            ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $shipper_status_id, NULL, NULL, NULL, Auth::id());
                            AdminFinanceController::add_payment($shipment_id, 0);
                            $international_shipment = InternationalShipment::where('shipment_id', $shipment_id)->first();
                            $international_shipment->sync = 0;
                            $international_shipment->save();

                            array_push($updated_array,$shipment->tracking_number);
                        }
                        else{
                            array_push($in_transit_array,$shipment->tracking_number);
                        }
                    }
                }
                else{
                    foreach ($shipments as $shipment_id){
                        $shipment = Shipment::where('id', $shipment_id)->first();
                        $shipment->update(['shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $shipper_status_id]);
                        ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $shipper_status_id, NULL, NULL, NULL, Auth::id());

                        array_push($updated_array,$shipment->tracking_number);
                        if($shipper_status_id == 4){
                            $manifest_shipment = CargoManifestBagShipments::where('shipment_id',$shipment_id);
                            if($manifest_shipment->exists()){
                                $received_shipments = 0;
                                $manifest_shipment = $manifest_shipment->first();
                                $bag = CargoManifestBag::where('id',$manifest_shipment->cargo_manifest_bag_id)->first();
                                if($bag){
                                    $bag_shipment = CargoManifestBagShipments::where('shipment_id',$shipment_id)->where('cargo_manifest_bag_id',$bag->id)->first();
                                    if($bag_shipment){
                                        $bag_shipment->status = 1;
                                        $bag_shipment->save();
                                    }
                                    $received_shipments = $bag->shipment->where('status',1)->count();
                                }

                                if($bag->shipments == $received_shipments){
                                    $bag->received_shipments = $received_shipments;
                                    $bag->status_id = 7;
                                    $bag->completed = 1;
                                    $bag->receiver_id = 346; //global_admin
                                    $bag->save();

                                    $manifest = ManifestBag::where('cargo_manifest_bag_id',$bag->id)->latest()->first();
                                    if($manifest){
                                        $manifest->status = 1;
                                        $manifest->save();

                                        $cargo_manifest = CargoManifest::find($manifest->cargo_manifest_id);

                                        $total_manifest_bags = $cargo_manifest->bags;
                                        $total_received_manifest_bags = ManifestBag::where('cargo_manifest_id',$manifest->cargo_manifest_id)->where('status',1)->count();

                                        CargoManifestBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, 346, $manifest->id);

                                        if($total_manifest_bags == $total_received_manifest_bags){
                                            $cargo_manifest->status_id = 2;
                                            $cargo_manifest->received_by = 346;
                                            $cargo_manifest->received_bags = $total_received_manifest_bags;
                                            $cargo_manifest->save();
                                        }
                                    }
                                }
                            }
                        }

                    }
                }

                $in_transit_html = '';
                if (count($in_transit_array) > 0) {
                    $in_transit_html = "Following Shipments(s) did not arrive at destination.<br><ul>";
                    foreach ($in_transit_array as $v) {
                        $in_transit_html .= "<li>" . $v . "</li>";
                    }
                    $in_transit_html .= "</ul>";
                }

                $updated_html = '';
                if (count($updated_array) > 0) {
                    $updated_html = "Following Shipments(s) updated successfully.<br><ul>";
                    foreach ($updated_array as $v) {
                        $updated_html .= "<li>" . $v . "</li>";
                    }
                    $updated_html .= "</ul>";
                }

                return back()->with(['updated_html' => $updated_html, 'in_transit_html' => $in_transit_html]);

            }
            return redirect()->back()->with('error', 'No shipment selected!');

        }
        return redirect()->back()->with('error', 'Shipment Status not selected!');
    }
    public function shipment_status_update_modal(Request $request){

        $shipper_status_id = $request->shipment_status_id;
        $seal_number = $request->seal_number;
        if($shipper_status_id){
            $shipments = explode(',', $request->shipment_ids);
            if(count($shipments) > 0){
                if($shipper_status_id == 14){
                    foreach ($shipments as $shipment_id){
                        Shipment::where('id', $shipment_id)->update(['shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $shipper_status_id]);
                        ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $shipper_status_id, NULL, NULL, NULL, Auth::id());
                        AdminFinanceController::add_payment($shipment_id, 0);
                        $international_shipment = InternationalShipment::where('shipment_id', $shipment_id)->first();
                        $international_shipment->sync = 0;
                        $international_shipment->save();

                    }
                }
                else{
                    foreach ($shipments as $shipment_id){
                        Shipment::where('id', $shipment_id)->update(['shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $shipper_status_id]);
                        ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $shipper_status_id, NULL, NULL, NULL, Auth::id());
                        $international_shipment = InternationalShipment::where('shipment_id', $shipment_id)->first();
                        $international_shipment->seal_number = $seal_number;
                        $international_shipment->save();
                    }
                }

                return redirect()->back()->with('success', 'Shipments updated successfully!');
            }
            return redirect()->back()->with('error', 'No shipment selected!');

        }
        return redirect()->back()->with('error', 'Shipment Status not selected!');
    }

    public function shipment_update_log($shipment_id, $admin_id, $weight = NULL){
        $shipment_log = new InternationalShipmentsLog();
        $shipment_log->shipment_id = $shipment_id;
        $shipment_log->weight = $weight;
        $shipment_log->updated_by = $admin_id;
        $shipment_log->save();
    }
}
