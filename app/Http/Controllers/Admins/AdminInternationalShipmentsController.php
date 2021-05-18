<?php

namespace App\Http\Controllers\Admins;
use App\Http\Controllers\Controller;
use App\Http\Models\InternationalShipment;
use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Admins\ActivityTrailController;

class AdminInternationalShipmentsController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function tracking_upload_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),53);
        return view('admin.international.tracking_upload');
    }
    public function tracking_upload_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),113);
        }
        $shipments = InternationalShipment::join('shipments', 'shipments.id', '=', 'international_shipments.shipment_id')
            ->select('shipments.id as shipment_id', 'shipments.tracking_number','international_shipments.international_tracking_number','international_shipments.postal_code','shipments.created_at as booking_date','international_shipments.actual_weight as actual_weight');

        $datatables = Datatables::of($shipments)
            ->addColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->addColumn('action',function ($shipments) {

                $dropdown = '
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                            <button type="button" class="dropdown-item remove"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
                        </div>
                    </div>
                ';

                return $dropdown;
            });
        return $datatables->make(true);
    }

    public function tracking_upload_store(Request $request){

        $names = [
            'tracking_number' => 'Tracking Number',
            'international_tracking_number' => 'Tracking Number',
            'actual_weight' => 'Actual Weight',
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
            'actual_weight' => ['nullable', 'numeric', 'between:0.1,100000'],
        ];


        if($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number', 'International Tracking Number', 'Actual Weight'];

            if (isset($spreadsheet)) {
                if (count($spreadsheet[0]) == 3){
                    $fields = [0 => 'tracking_number', 1 => 'international_tracking_number', 2 => 'actual_weight'];
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
                        $shipment_details = Shipment::where('tracking_number',$tracking)->first();
                        $shipment_id = $shipment_details->id;
                        $international_shipment = InternationalShipment::where('shipment_id', $shipment_id);
                        if($international_shipment->exists()){
                            $international_shipment = $international_shipment->first();
                            $international_shipment->international_tracking_number = $international_tracking_number;
                            $international_shipment->actual_weight = $international_shipment_weight;
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
                $details['international_tracking_number'] = $international_shipment->international_tracking_number;
                return response()->json(['status' => 0, 'details' => $details]);
            }
        }
        return response()->json(['status' => 1, 'error' => 'Please select a shipment!']);
    }

    public function tracking_upload_edit(Request $request){
        $international_shipment_id = $request->international_shipment_id;
        $tracking_number = $request->tracking_number;
        $international_tracking_number = $request->international_tracking_number;
        if($international_shipment_id){
            $international_shipment = InternationalShipment::find($international_shipment_id);
            if($international_shipment){
                if($tracking_number){
                    $shipment = Shipment::where('tracking_number', $tracking_number)->first();
                    if($shipment){
                        $shipment_id = $shipment->id;
                        $international_shipment->shipment_id = $shipment_id;
                        $international_shipment->international_tracking_number = $international_tracking_number;
                        $international_shipment->save();
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
}
