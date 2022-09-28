<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\BanksList;
use App\Http\Models\City;
use App\Http\Models\International\Wholesale\WholesaleShipment;
use App\Http\Models\International\Wholesale\WholesaleUser;
use App\Http\Models\International\Wholesale\WholesaleUserDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\Datatables\Datatables;
use Auth;
use Validator;
class InternationalWholesaleController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function accounts_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),594);
        $cities = City::where('status', 1)->get();
        $banks = BanksList::where('status', 1)->select('id', 'name')->get();
        return view('admin.international.wholesale.accounts_index')->with(['cities' => $cities, 'banks' => $banks]);
    }

    public function accounts_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),595);
        }
        $shippers = WholesaleUser::join('cities as c', 'c.id', '=', 'wholesale_users.city_id')
            ->join('admins as cb', 'cb.id', '=', 'wholesale_users.created_by')
            ->leftjoin('admins as ub', 'ub.id', '=', 'wholesale_users.updated_by')
            ->join('banks_lists as bl', 'bl.id', '=', 'wholesale_users.bank_id')
            ->select('wholesale_users.id as shipper_id', 'wholesale_users.name as shipper_name', 'wholesale_users.phone', 'wholesale_users.address', 'c.name as city', 'wholesale_users.email', 'bl.name as bank_name', 'wholesale_users.bank_account', 'wholesale_users.ntn', 'cb.name as created_by', 'ub.name as updated_by', 'wholesale_users.margin', 'wholesale_users.created_at', 'wholesale_users.updated_at', 'wholesale_users.is_document', 'wholesale_users.status');


        if (session('role_id') != 1) {
            $shippers = $shippers->whereIn('c.hub_id', session('hubs'));
        }

        $datatable = Datatables::of($shippers)
            ->addColumn('shipper_id_padded', function ($shippers) {
                return str_pad($shippers->shipper_id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('margin_percentage', function ($shippers) {
                if($shippers->margin != null){
                    return $shippers->margin . '%';
                }
                else{
                    return '';
                }
            })
            ->addColumn('user_status', function ($invoice) {
                if ($invoice->status == 1) {
                    return 'Enable';
                } else {
                    return 'Disable';
                }
            })
            ->filterColumn('user_status', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('wholesale_users.status', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('document', function ($shippers) {
                if ($shippers->is_document != 0) {
                    return '<a class="btn btn-sm btn-outline-info align-middle document_view" href="javascript:void(0);"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                } else {
                    return '';
                }
            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || count(array_intersect([801, 802, 803], session('permissions'))) !== 0) {
                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    if (session('role_id') == 1 || in_array(801, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item edit" data-target-id=' . $result->shipper_id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit </div></button>';
                    }

                    if (session('role_id') == 1 || in_array(802, session('permissions'))) {
                        if($result->status == 1){
                            $dropdown .= '<button type="button" class="dropdown-item disable" data-target-id=' . $result->shipper_id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x"></i></div><div class="col-9 offset-1">Disable </div></button>';
                        }
                        else{
                            $dropdown .= '<button type="button" class="dropdown-item enable" data-target-id=' . $result->shipper_id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Enable </div></button>';
                        }
                    }

                    if (session('role_id') == 1 || in_array(803, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item add_margin" data-margin="'. $result->margin .'" data-target-id=' . $result->shipper_id . ' data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-activity"></i></div><div class="col-9 offset-1">Add Margin </div></button>';
                    }


                    $dropdown .= '
                        </div>
                      </div>
                    ';

                    return $dropdown;
                } else {
                    return '';
                }
            });
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable = $datatable->whereBetween('wholesale_users.created_at', [$from,$to]);
        }

        return $datatable->make(true);
    }

    public function accounts_store(Request $request){
        $shipper_name = $request->shipper_name;
        $phone_number = $request->phone_number;
        $address = $request->address;
        $email_address = $request->email_address;
        $city_id = $request->city_id;
        $ntn = $request->ntn;
        $bank_id = $request->bank_id;
        $bank_account = $request->bank_account;

        $user = new WholesaleUser();
        $user->name = $shipper_name;
        $user->phone = $phone_number;
        $user->address = $address;
        $user->city_id = $city_id;
        $user->email = $email_address;
        $user->bank_id = $bank_id;
        $user->bank_account = $bank_account;
        $user->ntn = $ntn;
        $user->status = 1;
        $user->created_by = Auth::id();
        $user->save();

        $user_id = $user->id;

        $selected_ids = $request->selected_ids;
        if($selected_ids){
            $selected_ids = explode(',', $selected_ids);
            if(count($selected_ids) > 0){
                foreach ($selected_ids as $id){
                    $file_name = 'document_' . $id;

                    $image = $request->file($file_name);
                    $random = rand(1000, 100000);

                    $document_name = $user_id . '_' . $file_name . '_' .$random . '.'.$image->extension();
                    Storage::disk('public')->putFileAs('international_wholesale/' . $request->user_id . '', $image, $document_name);
                    $document = new WholesaleUserDocument();
                    $document->wholesale_user_id = $user_id;
                    $document->added_by = Auth::id();
                    $document->document = $document_name;
                    $document->save();

                }
                $user->is_document = 1;
                $user->save();

            }

        }

        return redirect()->back()->with('success', 'Shipper added successfully!');

    }

    public function accounts_change_status(Request $request){
        $shipper_id = $request->shipper_id;
        $action = $request->action;
        $shipper = WholesaleUser::find($shipper_id);
        if($shipper){
            if($action === 'disable'){
                $shipper->status = 0;
                $shipper->updated_by = Auth::id();
                $shipper->save();
                return response()->json(['status' => 1, 'success' => 'Shipper disabled successfully!']);
            }
            else if($action === 'enable'){
                $shipper->status = 1;
                $shipper->updated_by = Auth::id();
                $shipper->save();
                return response()->json(['status' => 1, 'success' => 'Shipper enabled successfully!']);
            }
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Something went wrong, please try again!']);
        }

    }

    public function accounts_margin(Request $request){
        $margin = $request->margin;
        $shipper_id = $request->shipper_id;
        $shipper = WholesaleUser::find($shipper_id);
        if($shipper){
            $shipper->margin = $margin;
            $shipper->updated_by = Auth::id();
            $shipper->save();
            return response()->json(['status' => 1, 'success' => 'Margin updated successfully!']);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Something went wrong, please try again!']);
        }
    }

    public function accounts_view_document(Request $request){
        $shipper_id = $request->shipper_id;

        if($shipper_id){
            $data = array();
            $shipper_documents = WholesaleUserDocument::join('admins', 'admins.id', '=', 'wholesale_user_documents.added_by')->where('wholesale_user_documents.wholesale_user_id', $shipper_id);
            if($shipper_documents->exists()){
                $shipper_documents = $shipper_documents->select('wholesale_user_documents.id', 'wholesale_user_documents.document', 'admins.name', 'wholesale_user_documents.created_at')->get();
                foreach ($shipper_documents as $shipper_document){
                    $row = array();
                    $row['id'] = $shipper_document->id;
                    $row['added_by'] = $shipper_document->name;
                    $row['added_at'] = Carbon::parse($shipper_document->created_at)->toDateTimeString();
                    $row['document'] =  asset(Storage::url('international_wholesale/') . $shipper_document->document);
                    $data[] = $row;
                }
                if(count($data) > 0){
                    return response()->json(['status' => 1, 'details' => $data]);
                }
                else{
                    return response()->json(['status' => 0, 'error' => 'No data found!']);
                }
            }
        }
    }

    public function accounts_edit_info(Request $request){
        $shipper_id = $request->shipper_id;
        if($shipper_id){
            $wholesale_user = WholesaleUser::find($shipper_id);
            if($wholesale_user){
                $shipper = array();
                $shipper['id'] = $shipper_id;
                $shipper['name'] = $wholesale_user->name;
                $shipper['phone'] = $wholesale_user->phone;
                $shipper['address'] = $wholesale_user->address;
                $shipper['city_id'] = $wholesale_user->city_id;
                $shipper['email'] = $wholesale_user->email;
                $shipper['bank_id'] = $wholesale_user->bank_id;
                $shipper['bank_account'] = $wholesale_user->bank_account;
                $shipper['ntn'] = $wholesale_user->ntn;
                $shipper['is_document'] = FALSE;
                if($wholesale_user->is_document){
                    $wholesale_user_documents = WholesaleUserDocument::where('wholesale_user_id', $shipper_id);
                    if($wholesale_user_documents->exists()){
                        $wholesale_user_documents = $wholesale_user_documents->get();
                        $documents = array();
                        foreach ($wholesale_user_documents as $user_document){
                            $data = array();
                            $data['id'] = $user_document->id;
                            $data['document'] = asset(Storage::url('international_wholesale/') . $user_document->document);
                            $data['added_by'] = Admin::find($user_document->added_by)->name;
                            $data['added_at'] = Carbon::parse($user_document->created_at)->toDateTimeString();
                            $documents[] = $data;
                        }
                        if(count($documents) > 0){
                            $shipper['documents'] = $documents;
                            $shipper['is_document'] = TRUE;
                        }
                    }
                }
                return response()->json(['status' => 1, 'details' => $shipper]);
            }
            else{
                return response()->json(['status' => 0, 'error' => 'No data found!']);
            }
        }
    }


    public function accounts_edit(Request $request){

        $shipper_name = $request->shipper_name;
        $phone_number = $request->phone_number;
        $address = $request->address;
        $email_address = $request->email_address;
        $city_id = $request->city_id;
        $ntn = $request->ntn;
        $bank_id = $request->bank_id;
        $bank_account = $request->bank_account;
        $shipper_id = $request->shipper_id;
        $wholesale_user = WholesaleUser::find($shipper_id);
        if($wholesale_user){
            $wholesale_user->name = $shipper_name;
            $wholesale_user->phone = $phone_number;
            $wholesale_user->address = $address;
            $wholesale_user->city_id = $city_id;
            $wholesale_user->email = $email_address;
            $wholesale_user->bank_id = $bank_id;
            $wholesale_user->bank_account = $bank_account;
            $wholesale_user->ntn = $ntn;
            $wholesale_user->updated_by = Auth::id();
            $wholesale_user->save();

            $selected_ids = $request->selected_ids;
            if($selected_ids){
                $selected_ids = explode(',', $selected_ids);
                if(count($selected_ids) > 0){
                    foreach ($selected_ids as $id){
                        $file_name = 'document_' . $id;

                        $file = $request->file($file_name);
//                        $random = rand(1000, 100000);
                        $now = Carbon::now();
                        $time = $now->year . '_' . $now->month . '_' . $now->day;
                        $document_name = $shipper_id . '_' . $file_name . '_' .$time . '.'.$file->extension();
                        Storage::disk('public')->putFileAs('international_wholesale/' . $request->user_id . '', $file, $document_name);
                        $document = new WholesaleUserDocument();
                        $document->wholesale_user_id = $shipper_id;
                        $document->added_by = Auth::id();
                        $document->document = $document_name;
                        $document->save();

                    }
                    $wholesale_user->is_document = 1;
                    $wholesale_user->save();

                }

            }
            return redirect()->back()->with('success', 'Shipper added successfully!');
        }
        return redirect()->back()->with('error', 'Shipper not found!');

    }

    public function accounts_remove_image(Request $request){
        $image_id = $request->image_id;
        $shipper_id = NULL;
        if($image_id){
            $user_document = WholesaleUserDocument::find($image_id);
            if($user_document){
                $shipper_id = $user_document->wholesale_user_id;
                $exists = Storage::disk('public')->exists('international_wholesale/' . $user_document->document);
                if ($exists) {
                    Storage::disk('public')->delete('international_wholesale/' . $user_document->document);
                    $user_document->delete();

                    if(WholesaleUserDocument::where('wholesale_user_id', $shipper_id)->count() == 0){
                        $wholesale_user = WholesaleUser::find($shipper_id);
                        $wholesale_user->is_document = 0;
                        $wholesale_user->save();
                    }

                    return response()->json(['status' => 0, 'success' => 'File deleted successfully!']);
                }
                return response()->json(['status' => 1, 'success' => 'File not found!']);
            }
        }
        return response()->json(['status' => 1, 'success' => 'Something went wrong!']);

    }

    public function excel_booking_index(){

        ActivityTrailController::createActivityTrailLog(Auth::id(),596);
        $cities = City::where('business_category_id', 2)->get();
        return view('admin.international.wholesale.excel_booking')->with(['cities' => $cities]);
    }

    public function excel_booking_store(Request $request){

        $names = [
            'shipper_id' => 'Shipper Id',
            'dhl_waybill' => 'DHL Waybill No',
            'destination' => 'Destination',
            'type' => 'Type',
            'weight' => 'Weight',
            'pieces' => 'Pieces',
            'other_charges' => 'Other Charges',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
            'exists' => 'Given :attribute is Invalid / not ready for update.',
        ];
        $rules = [
            'shipper_id' => ['required', 'integer', Rule::exists('wholesale_users', 'id')->where(function($query){
                $query->where('status', 1);
            })],
            'dhl_waybill' => ['required'],
            'destination' => ['required', 'string', 'between:1,100', Rule::exists('cities', 'name')->where(function($query){
                $query->where('status', 1)->where('hub', 1)->where('business_category_id', 2);
            })],
            'type' => ['required', 'between:0,190'],
            'weight' => ['required', 'numeric', 'between:0.1,100000'],
            'pieces' => ['required', 'numeric', 'between:0.1,1000'],
            'other_charges' => ['required', 'numeric', 'between:0,1000000']
        ];


        if($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Shipper Id', 'DHL Waybill No', 'Destination', 'Type', 'Weight', 'Pieces', 'Other Charges'];

            if (isset($spreadsheet)) {

                if (count($spreadsheet[0]) == 7){
                    $fields = [0 => 'shipper_id', 1 => 'dhl_waybill', 2 => 'destination', 3 => 'type', 4 => 'weight', 5 => 'pieces', 6 => 'other_charges'];
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

                foreach ($rows as $key => $row) {
                    $row_id = $key + 2;

                    $validate = Validator::make($row, $rules, $messages);

                    $validate->setAttributeNames($names);

                    if ($validate->fails()) {
                        $errors['Row #' . $row_id] = $validate->errors()->all();
                    }
                    if (empty($errors['Row #' . $row_id])) {

                        if (WholesaleShipment::where('dhl_waybill', '=', $row['dhl_waybill'])->where('wholesale_user_id', '=', $row['shipper_id'])->exists()) {
                            $errors['Row #' . $row_id][] = 'Shipment is already Booked #' . $row['dhl_waybill'];
                        }
                    }
                }
                if(empty($errors)){
                    $tracking_numbers = array();
                    $invoice_data = array();
                    foreach ($rows as $key => $row) {
                        $row_id = $key + 2;
                        $shipper_id = trim($row['shipper_id']);
                        $tracking = trim($row['dhl_waybill']);
                        $destination_name = trim($row['destination']);
                        $type = trim($row['type']);
                        $weight = $row['weight'];
                        $pieces = $row['pieces'];
                        $other_charges = $row['other_charges'];
                        $shipper = WholesaleUser::find($shipper_id);

                        $destination = City::where('name', $destination_name)->first();

                        $wholesale_shipment = new WholesaleShipment();
                        $wholesale_shipment->dhl_waybill = $tracking;
                        $wholesale_shipment->wholesale_user_id = $shipper_id;
                        $wholesale_shipment->origin_city_id = $shipper->city_id;
                        $wholesale_shipment->destination_city_id = $destination->id;
                        $wholesale_shipment->type = $type;
                        $wholesale_shipment->weight = $weight;
                        $wholesale_shipment->pieces = $pieces;
                        $wholesale_shipment->courier_charges = 0;
                        $wholesale_shipment->other_charges = $other_charges;
                        $wholesale_shipment->bill_amount = 0;
                        $wholesale_shipment->status_id = 2;
                        $wholesale_shipment->created_by = Auth::id();
                        $wholesale_shipment->save();

                        $courier_charges = InternationalWholesaleChargesController::weight($destination->id, $weight);

                        $wholesale_shipment->courier_charges = $courier_charges;
                        $wholesale_shipment->bill_amount = $courier_charges + $other_charges;

                        $wholesale_shipment->save();
                        $shipment_id = $wholesale_shipment->id;
                        $tracking_numbers['Row #' . $row_id] = $tracking;

                        $invoice_data[$shipper_id][] = $shipment_id;
                    }
                    if(count($invoice_data) > 0){
                        foreach ($invoice_data as $shipper_id => $shipment_ids){
                            InternationalWholesaleInvoiceController::generate_invoice($shipper_id, $shipment_ids);
                        }
                    }
                    $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                        return $row . ': ' . $tracking_number;
                    }, array_keys($tracking_numbers), $tracking_numbers));

                    return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipment(s) Updated with DHL Waybill(s):' . PHP_EOL . $tracking_numbers]);
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

    public function excel_booking_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),597);
        }
        $bookings = WholesaleShipment::join('cities as o' , 'o.id', '=', 'wholesale_shipments.origin_city_id')
            ->join('wholesale_users as wu', 'wu.id','=', 'wholesale_shipments.wholesale_user_id')
            ->join('cities as d' , 'd.id', '=', 'wholesale_shipments.destination_city_id')
            ->join('wholesale_shipment_statuses as wss', 'wss.id', '=', 'wholesale_shipments.status_id')
            ->join('admins as cb', 'cb.id', '=', 'wholesale_shipments.created_by')
            ->leftjoin('admins as ub', 'ub.id', '=', 'wholesale_shipments.updated_by')
            ->select('wholesale_shipments.id as shipment_id', 'wholesale_shipments.dhl_waybill','wu.name as shipper_name', 'o.name as origin', 'd.name as destination', 'wholesale_shipments.type', 'wholesale_shipments.weight', 'wholesale_shipments.pieces', 'wholesale_shipments.courier_charges', 'wholesale_shipments.other_charges', 'wholesale_shipments.bill_amount', 'wholesale_shipments.created_at as booking_date', 'wholesale_shipments.updated_at as updated_date', 'wss.name as shipment_status', 'ub.name as updated_by', 'cb.name as booked_by', 'wholesale_shipments.status_id');


        if (session('role_id') != 1) {
            $bookings = $bookings->whereIn('o.hub_id', session('hubs'));
        }

        $datatable = Datatables::of($bookings)
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || count(array_intersect([805, 806], session('permissions'))) !== 0) {
                    if($result->status_id != 4){

                        $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                        if (session('role_id') == 1 || in_array(805, session('permissions'))) {
                            $dropdown .= '<button type="button" class="dropdown-item edit" data-target-id=' . $result->shipper_id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit </div></button>';
                        }



                        /*if (session('role_id') == 1 || in_array(806, session('permissions'))) {
                            $dropdown .= '<button type="button" class="dropdown-item cancel" data-target-id=' . $result->shipper_id . ' data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-trash"></i></div><div class="col-9 offset-1">Cancel </div></button>';
                        }*/


                        $dropdown .= '
                        </div>
                      </div>
                    ';

                        return $dropdown;
                    }
                } else {
                    return '';
                }
            });
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable = $datatable->whereBetween('wholesale_shipments.created_at', [$from,$to]);
        }

        return $datatable->make(true);
    }

    public function excel_booking_edit_info (Request $request){
        $shipment_id = $request->shipment_id;
        if($shipment_id){
            $shipment = WholesaleShipment::find($shipment_id);

            if($shipment){
                $data = array();
                $data['id'] = $shipment->id;
                $data['dhl_waybill'] = $shipment->dhl_waybill;
                $data['destination_city_id'] = $shipment->destination_city_id;
                $data['type'] = $shipment->type;
                $data['weight'] = $shipment->weight;
                $data['pieces'] = $shipment->pieces;
                $data['other_charges'] = $shipment->other_charges;

                return response()->json(['status' => 0, 'details' => $data]);

            }
            return response()->json(['status' => 1, 'error' => 'No shipment found!']);

        }
    }

    public function excel_booking_edit(Request $request){

        $shipment_id = $request->shipment_id;
        if($shipment_id){
            $shipment = WholesaleShipment::find($shipment_id);

            if($shipment){

                $old_destination = $shipment->destination_city_id;
                $old_weight = $shipment->weight;
                $old_other_charges = $shipment->other_charges;
                $courier_charges = $shipment->courier_charges;
//                $dhl_waybill = $request->dhl_waybill;
                $destination_city_id = $request->destination_city_id;
                $type = $request->type;
                $weight = $request->weight;
                $pieces = $request->pieces;
                $other_charges = $request->other_charges;

//                $shipment->dhl_waybill = $dhl_waybill;
                $shipment->destination_city_id = $destination_city_id;
                $shipment->type = $type;
                $shipment->weight = $weight;
                $shipment->pieces = $pieces;
                $shipment->other_charges = $other_charges;
                $shipment->updated_by = Auth::id();
                $shipment->save();

                $invoice_recalculation = false;


                if(($old_destination != $destination_city_id) || ($old_weight != $weight)){
                    $courier_charges = InternationalWholesaleChargesController::weight($destination_city_id, $weight);

                    $invoice_recalculation = true;
                }
                if($old_other_charges != $other_charges){
                    $invoice_recalculation  = true;
                }

                if($invoice_recalculation){

                    $shipment->courier_charges = $courier_charges;

                    $shipment->bill_amount = $courier_charges + $other_charges;
                    $shipment->save();
                    InternationalWholesaleInvoiceController::recalculate_invoice($shipment->id);
                }

                return redirect()->back()->with('success', 'Shipment updated successfully!');

            }

            return redirect()->back()->with('error', 'Shipment not found!');
        }

    }
    public function excel_booking_cancel(Request $request){
        $shipment_id = $request->shipment_id;

        if($shipment_id){
            $shipment = WholesaleShipment::find($shipment_id);
            if($shipment){
                $shipment->status_id = 3;
                $shipment->updated_by = Auth::id();
                $shipment->save();

                return response()->json(['status' => 1, 'success' => 'Shipment canceled successfully!']);
            }
            return response()->json(['status' => 0, 'error' => 'Shipment not found!']);

        }
    }
}
