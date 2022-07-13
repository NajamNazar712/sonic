<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteShipment;
use App\Http\Models\Admin\V2Dispute;
use App\Http\Models\Admin\V2DisputeImage;
use App\Http\Models\Admin\V2DisputeReason;
use App\Http\Models\Admin\V2DisputeStatus;
use App\Http\Models\City;
use App\Http\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\Datatables\Datatables;
use function foo\func;

class V2AdminDisputeShipmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(), 521);
        $cities = City::select('id', 'name')->where('status', 1)->get();
        $reasons = V2DisputeReason::all();
        $status = V2DisputeStatus::all();
        $admins = Admin::where('status', 1)->select('id', 'name')->get();
        return view('admin.v2_dispute.index')->with(['reasons' => $reasons, 'statuses' => $status, 'cities' => $cities, 'admins' => $admins]);
    }
    public function list(Request $request){
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 522);
        }
        $dispute = V2Dispute::leftjoin('shipments', 'shipments.id', '=', 'v2_disputes.shipment_id')
        ->leftjoin('users as u', 'shipments.user_id', '=', 'u.id')
        ->leftjoin('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
        ->leftjoin('cities AS oc', 'usi.city_id', '=', 'oc.id')
        ->leftjoin('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
        ->join('admins as ab', 'ab.id', '=', 'v2_disputes.added_by')
        ->join('v2_dispute_reasons as dr', 'dr.id', '=', 'v2_disputes.reason_id')
        ->join('v2_dispute_statuses as ds', 'ds.id', '=', 'v2_disputes.status_id')
        ->leftjoin('admins as ub', 'ub.id', '=', 'v2_disputes.updated_by')
        ->select(['v2_disputes.id as dispute_id','v2_disputes.shipment_id', 'v2_disputes.remarks', 'v2_disputes.image', 'ab.name as added_by', 'ub.name as updated_by', 'v2_disputes.created_at', 'v2_disputes.updated_at', 'shipments.tracking_number', 'shipments.actual_weight', 'shipments.amount as cod_amount', 'oc.name as origin', 'dc.name as destination', 'u.name as shipper', 'usi.poc as poc', 'dr.name as reason', 'ds.name as status', 'v2_disputes.status_id', 'v2_disputes.reason_id']);
        if (session('role_id') != 1) {
            $dispute = $dispute->where(function ($query) {
                $query->whereIn('oc.hub_id', session('hubs'))
                    ->orWhereIn('dc.hub_id', session('hubs'));
            });
        }

        $datatables = Datatables::of($dispute)
            ->addColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
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
            ->addColumn('image_view',function ($dispute){
                if($dispute->image){
                    return "<a href='#' class='btn btn-block btn-outline-info mr-1 image_popup'><i class='la la-image'></i></a>";
                }
                else{
                    return '-';
                }

            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || count(array_intersect([703], session('permissions'))) !== 0) {

                    if($result->status_id == 1){
                        $update = 'Mark as In-process';
                    }
                    else if($result->status_id == 2){
                        $update = 'Mark as Resolved';
                    }
                    else{
                        $update = '';
                    }
                    $status_update = '<a href="javascript:void(0);" class="dropdown-item update"><i class="ft-plus-circle primary"></i> '. $update .'</a>';

                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">';

                    if (session('role_id') == 1 || in_array(703, session('permissions'))){
                        if($result->status_id < 3){

                            $dropdown .= $status_update;
                        }else{
                            if($result->status_id == 3){
                                return '-';
                            }
                        }
                    }else{
                        return '-';
                    }
                    $dropdown .= '</div>
                      </div>
                    ';

                    return $dropdown;

                } else {
                    return '';
                }
            });

        if ($origin = $request->get('search_origin')) {
            $dispute->where('oc.id', '=', $origin);
        }
        if ($destination = $request->get('search_destination')) {
            $dispute->where('dc.id', '=', $destination);
        }

        if ($reason = $request->get('search_reason')) {
            $dispute->where('v2_disputes.reason_id', '=', $reason);
        }
        if ($status = $request->get('search_status')) {
            $dispute->where('v2_disputes.status_id', '=', $status);
        }
        if ($launched_by = $request->get('search_launched_by')) {
            $dispute->where('v2_disputes.added_by', '=', $launched_by);
        }


        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $dispute->whereBetween('v2_disputes.created_at', [$from,$to]);
        }

        return $datatables->make(true);
    }
    public function add_submit(Request $request)
    {
        $return_note_statuses = array(23, 24, 28, 29, 34, 35, 44, 45,46, 47, 48, 60);
        $tracking_number = $request->tracking_number;
        $dispute = new V2Dispute();
        $dispute->status_id = 1;
        $dispute->reason_id = $request->reason_id;
        $dispute->remarks = $request->remarks;
        $dispute->added_by = Auth::id();
        
        if ($tracking_number) {
            $shipment = Shipment::where('tracking_number', $tracking_number);
            if ($shipment->exists()) {
                $shipment = $shipment->first();

                if(in_array($shipment->shipper_status_id, [7, 8, 9, 10, 11, 12, 15, 18, 20, 30])) {
                    $delivery_note_shipment = DeliveryNoteShipment::where('shipment_id', $shipment->id);
                    if ($delivery_note_shipment->exists()) {
                        $delivery_note_shipment = $delivery_note_shipment->max('delivery_note_id');
                        $delivery = DeliveryNote::where('id' , $delivery_note_shipment)->where('status', 0)->exists();
                        if($delivery){
                            return redirect()->back()->with('error', 'Shipment is in an Unverified Delivery Note');
                        }
                    }
                }

                if(in_array($shipment->shipper_status_id,$return_note_statuses)){
                    $return_note_shipments_details = ReturnNoteShipment::where('shipment_id', $shipment->id);
                    if($return_note_shipments_details->exists()){
                        $return_note_id = $return_note_shipments_details->max('return_note_id');
                        $return_note = ReturnNote::where('id', $return_note_id)->where('status', 0)->exists();
                        if($return_note){
                            return redirect()->back()->with('error', 'Shipment is in an Unverified Return Note');
                        }
                    }
                }

                $dispute = new V2Dispute();
                $dispute->shipment_id = $shipment->id;
                
            }else{

                return redirect()->back()->with('error', 'Invalid Tracking Number');
            }
        }
        $dispute->save();
        $file_name = 'image_1';
        if ($request->has($file_name)) {
            $image1 = $request->file($file_name);
            $extension = 'png';
            $random = rand(1000, 100000);
            $now = Carbon::now();
            $time = $now->year . '_' . $now->month;
            $image_name = $time . $random . '_1.' . $extension;
            $directory = 'dispute_shipments';
            Storage::disk('public')->putFileAs($directory, $image1, $image_name);
            $dispute->image = 1;
            $dispute->save();
            $this->add_images($dispute->id, $image_name);
        }
        $file_name = 'image_2';
        if ($request->has($file_name)) {
            $image2 = $request->file($file_name);
            $extension = 'png';
            $random = rand(1000, 100000);
            $now = Carbon::now();
            $time = $now->year . '_' . $now->month;
            $image_name = $time . $random . '_2.' . $extension;
            $directory = 'dispute_shipments';
            Storage::disk('public')->putFileAs($directory, $image2, $image_name);
            $this->add_images($dispute->id, $image_name);
        }
        return redirect()->back()->with('success', 'Shipment Added to Dispute!');

    }

    public function add_images($dispute_id, $image_name){
        $dispute_image = new V2DisputeImage();
        $dispute_image->dispute_id = $dispute_id;
        $dispute_image->image = $image_name;
        $dispute_image->added_by = Auth::id();
        $dispute_image->save();
    }

    public function dispute_update(Request $request){
        $dispute_id = $request->dispute_id;
        if($dispute_id){
            $dispute = V2Dispute::find($dispute_id);
            if($dispute){

                if($dispute->status_id == 1){
                    $dispute->status_id = 2;
                }
                else if($dispute->status_id == 2){
                    $dispute->status_id = 3;
                }
                $dispute->updated_by = Auth::id();
                $dispute->save();
                return response()->json(['status' => 0, 'success' => 'Status updated successfully!']);
            }
            return response()->json(['status' => 1, 'error' => 'Something went wrong!']);
        }
        return response()->json(['status' => 1, 'error' => 'Something went wrong!']);
    }

    public function dispute_images(Request $request){
        $dispute_id = $request->dispute_id;

        if($dispute_id){
            $dispute_images = V2DisputeImage::where('dispute_id', $dispute_id);
            if($dispute_images->exists()){
                $dispute_images = $dispute_images->get();
                $data = array();
                foreach ($dispute_images as $index => $dispute_image) {
                    $url = Storage::url('dispute_shipments/'.$dispute_image->image);
                    $data[] = '<img src="'.asset($url) .'">';
                }
                return response()->json(['status' => 0, 'images' => $data]);
            }
        }
    }

    public function bulk_in_process(Request $request){
        
        $dispute_ids = $request->dispute_ids;
        if($dispute_ids){
            foreach($dispute_ids as $dispute_id){
                    $dispute = V2Dispute::find($dispute_id);
                    if($dispute){
                        if($dispute->status_id == 1){
                            $dispute->status_id = 2;
                        }
                        $dispute->save();
                    }
            }
            return response()->json(['status' => 0, 'success' => 'Status updated successfully!']);

        }
        return response()->json(['status' => 1, 'error' => 'Something went wrong!']);
    }

    public function bulk_resolved(Request $request){
        $dispute_ids = $request->dispute_ids;
        if($dispute_ids){

            foreach($dispute_ids as $dispute_id){
                    $dispute = V2Dispute::find($dispute_id);
                    if($dispute){
                        if($dispute->status_id == 2){
                            $dispute->status_id = 3;
                        }
                        $dispute->save();
                    }
            }
            return response()->json(['status' => 0, 'success' => 'Status updated successfully!']);

        }
        return response()->json(['status' => 1, 'error' => 'Something went wrong!']);
    }

    public function excel_upload(Request $request){
        $rules = [
            'dispute_shipments' => ['required', 'mimes:xlx,xlsx'],
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return back()->with(['error' => "Invalid File Format"]);
        } else {
            Validator::extend('check_tracking_number', function ($attribute, $value, $parameters, $validator) {
                if ($value) {
                    $result = false;
                    if (Shipment::where('tracking_number', $value)->exists()) {
                        $result = true;
                    }
                    if ($result) {
                        return true;
                    } else {
                        return false;
                    }
                }
            });
            Validator::extend('check_reason_id', function ($attribute, $value, $parameters, $validator) {
                if ($value) {
                    $result = false;
                    if (V2DisputeReason::where('id', $value)->exists()) {
                        $result = true;
                    }
                    if ($result) {
                        return true;
                    } else {
                        return false;
                    }
                }
            });
            $names = [
                'tracking_number' => 'Tracking #',
                'reason_id' => 'Reason ID',
                'remarks' => 'Remarks',
            ];

            $messages = [
                'required' => ':attribute is Required.',
                'check_tracking_number' => 'Tracking # not found!',
                'check_reason_id' => 'Reasong ID not found!',
            ];
            $rules = [
                'tracking_number' => ['between:1,100', 'check_tracking_number'],
                'reason_id' => ['required', 'between:1,20', 'check_reason_id'],
                'remarks' => ['required', 'between:1,20'],
            ];


            $fields = [0 => 'tracking_number', 1 => 'reason_id', 2 => 'remarks'];
            if ($file = $request->file('dispute_shipments')) {
                $spreadsheet = IOFactory::createReaderForFile($file);
                $spreadsheet->setReadDataOnly(true);
                $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

                $header = ['Tracking #', 'Reason ID', 'Remarks'];

                if (isset($spreadsheet)) {
                    $header_correct = true;
                    foreach ($spreadsheet[0] as $index => $header_value) {
                        if ($index == 3) {
                        } elseif (!isset($header[$index]) || $header_value != $header[$index]) {
                            $header_correct = false;
                            break;
                        }
                    }
                    if (!$header_correct) {
                        return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
                    } else {
                        unset($spreadsheet[0]);
                    }
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
                    foreach ($rows as $key => $row) {
                        $row_id = $key + 2;
                        $validate = Validator::make($row, $rules, $messages);
                        $validate->setAttributeNames($names);
                        if ($validate->fails()) {
                            $errors['Row #' . $row_id] = $validate->errors()->all();
                        }
                    }
                    if (empty($errors)) {
                        $updated = 0;
                        $not_updated = 0;

                        foreach ($rows as $data) {
                            $dispute = new V2Dispute();
                            $dispute->status_id = 1;
                            $dispute->reason_id = $data['reason_id'];
                            $dispute->remarks = $data['remarks'];
                            $dispute->added_by = Auth::id();
                            $shipment = Shipment::where('tracking_number', $data['tracking_number']);
                            if ($shipment->exists()) {
                                $shipment = $shipment->first();
                                $dispute->shipment_id = $shipment->id;
                                
                            }
                            $dispute->save();
                            $updated++;
                        }

                        $error_msg = '';
                        if ($not_updated > 1) {
                            $error_msg = 'Total ' . $not_updated . ' rows could not updated!';
                        }
                        return redirect()->back()->with(['success' => 'Total ' . $updated . ' rows updated', 'error' => $error_msg]);
                    } else {
                        $errors = array_map(function ($row, $errors) {
                            return $row . ':' . PHP_EOL . implode(' | ', $errors);
                        }, array_keys($errors), $errors);
                        return redirect()->back()->withErrors($errors);
                    }

                } else {
                    return redirect()->back()->with('error', 'No Records in File');
                }
            }
        }
    }
}
