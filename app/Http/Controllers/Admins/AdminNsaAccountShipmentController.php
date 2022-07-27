<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\Admins\V2Pickup\V2AdminPickupsController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\ShipmentsPaymentJourneyController;
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Admin\Attendance\EmployeeAttendanceActionLog;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\NsaAccountShipment;
use App\Http\Models\Admin\OperationRidersCategory;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteShipment;
use App\Http\Models\City;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\InvoiceShipment;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\PackagingMaterialRequestHistory;
use App\Http\Models\PendingInvoiceShipment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\Rider;
use App\Http\Models\Route;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentsJourney;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Auth;

class AdminNsaAccountShipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except('cancel');

        $this->middleware('Permission');
    }

    static public function nsa_account_shipment($shipment_id)
    {
        $shipment = Shipment::find($shipment_id);

        $nsa_shipment = new NsaAccountShipment();
        $nsa_shipment->shipment_id = $shipment->id;
        $nsa_shipment->hub_id = $shipment->pickup_address->city->id;
        $nsa_shipment->save();
    }

    static public function nsa_account_shipment_process()
    {
        $nsa_shipments = NsaAccountShipment::where('status', 0);
        if ($nsa_shipments->exists()) {
            $nsa_shipments = $nsa_shipments->get();
            $settings = GlobalSettings::where('type', 'nsa_accounts')->first();
            $rider_id = $settings->setting_value;
            $valid_shipments = array();
            $shipments_count = 0;
            $total_cod_amount = 0;
            foreach ($nsa_shipments as $nsa_shipment) {
                if (!in_array($nsa_shipment->shipment_id, $valid_shipments)) {
                    $shipment_details = Shipment::find($nsa_shipment->shipment_id);
                    if ($shipment_details) {
                        $valid_shipments[] = $nsa_shipment->shipment_id;
                        $shipments_count++;

                        if ($shipment_details->booking_type_id != 4 || ($shipment_details->booking_type_id == 4 && $shipment_details->charges_mode_id == 2)) {
                            $total_cod_amount += $shipment_details->amount;
                        }
                    }
                }
                $nsa_shipment->status = 1;
                $nsa_shipment->save();
            }

            $order = false;
            if ($shipments_count != 0) {
                $note = DeliveryNote::create([
                    'hub_id' => 202,
                    'rider_id' => $rider_id,
                    'route_id' => 2,
                    'shipments_count' => $shipments_count,
                    'admin_id' => 50,
                    'total_cod_amount' => $total_cod_amount,
                    'password' => NULL,
                    'last_updated_at' => Carbon::now(),
                    'special_rider' => 0,
                    'order' => $order
                ]);

                if ($note) {
                    if (!$order) {  //Default
                        sort($valid_shipments); //sort_valid_shipments;
                    }
                    $serial = 1;
                    foreach ($valid_shipments as $index => $shipment) {
                        DeliveryNoteShipment::create([
                            'delivery_note_id' => $note->id,
                            'shipment_id' => $shipment,
                            'notification' => 0,
                            'rider_information' => 0,
                            'ordering' => $serial
                        ]);
                        $serial++;
                    }

                    foreach ($valid_shipments as $index => $shipment) {
                        $shipment_data = Shipment::find($shipment);
                        $shipment_data->shipper_status_id = 20;
                        $shipment_data->consignee_status_id = 20;
                        $shipment_data->save();

                        ShipmentsJourneyController::add($shipment, 5, 5, NULL, NULL, NULL, 50, $note->id, $rider_id);
                        ShipmentsJourneyController::add($shipment, 12, 12, 34, NULL, NULL, 50, $note->id, NULL, 0);
                        ShipmentsJourneyController::add($shipment, 20, 20, 34, NULL, NULL, 50, $note->id, NULL, 1);

                        DeliveryNoteShipment::where(['delivery_note_id' => $note->id, 'shipment_id' => $shipment_data->id])->update(['status' => 1]);
                    }

                    DeliveryNote::where('id', $note->id)->update(['delivered_shipments' => 0, 'verified_by' => 50, 'received_cod_amount' => 0, 'status' => 1, 'last_updated_at' => Carbon::now(), 'status_verified_at' => Carbon::now()]);
                }

                $note = ReturnNote::create(['hub_id' => 202, 'rider_id' => 274, 'route_id' => 2, 'shipments_count' => $shipments_count, 'admin_id' => 50]);
                if ($note) {
                    foreach ($valid_shipments as $shipment_id) {
                        $shipment = Shipment::where('id', $shipment_id);

                        $shipment = $shipment->first();
                        ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $shipment_id]);
                        $shipment->shipper_status_id = 23;
                        $shipment->consignee_status_id = 23;
                        $shipment->save();
                        ShipmentsJourneyController::add($shipment->id, 23, 23, NULL, NULL, NULL, 50, $note->id, $rider_id);
                    }
                }
            }
        }
    }

    public function arrival_index()
    {
        return view('admin.telenor.arrival');
    }

    public function arrival_submit(Request $request)
    {
        $names = [
            'tracking_number' => 'Tracking Number',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
        ];
        $rules = [
            'tracking_number' => ['required', 'integer', Rule::exists('shipments', 'tracking_number')],
        ];
        $fields = [0 => 'tracking_number'];

        if ($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number'];

            if (isset($spreadsheet)) {
                $header_correct = TRUE;

                foreach ($spreadsheet[0] as $index => $header_value) {
                    if (!isset($header[$index]) || $header_value != $header[$index]) {
                        $header_correct = FALSE;
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
                            } else {
                                if (in_array($row['tracking_number'], $tracking_ids)) {
                                    $errors['Row #' . $row_id][] = 'Same Tracking Number as of Row #' . $tracking_id_row[$row['tracking_number']];
                                } else {
                                    $tracking_ids[] = $row['tracking_number'];
                                    $tracking_id_row[$row['tracking_number']] = $row_id;
                                }
                            }
                        }

                        $settings = GlobalSettings::where('type', 'nsa_accounts');
                        $nsa_accounts = array();
                        if ($settings->exists()) {
                            $settings = $settings->first();
                            $nsa_accounts = array_map('intval', explode(',', $settings->text));
                        }
                        if (count($nsa_accounts) > 0) {
                            if (!Shipment::where('tracking_number', $row['tracking_number'])->whereIn('user_id', $nsa_accounts)->where('shipper_status_id', 1)->exists()) {
                                $errors['Row #' . $row_id][] = 'Shipment not found with Tracking Number #' . $row['tracking_number'];
                            }
                        } else {
                            $errors['Row #' . $row_id][] = 'Nsa Account Not Found' . $row['tracking_number'];
                        }
                    }
                }
                if (empty($errors)) {
                    $tracking_numbers = array();
                    $shipment_ids = array();
                    foreach ($rows as $key => $row) {
                        $row_id = $key + 2;
                        $tracking = trim($row['tracking_number']);

                        $shipment_details = Shipment::where('tracking_number', $tracking)->first();
                        $shipment_id = $shipment_details->id;
                        $shipment_ids[] = $shipment_id;


                        $tracking_numbers['Row #' . $row_id] = $tracking;
                    }

                    $nsa_shipments = Shipment::whereIn('id', $shipment_ids);

                    if ($nsa_shipments->exists()) {
                        $nsa_shipments = $nsa_shipments->get();
                        $valid_shipments = array();

                        foreach ($nsa_shipments as $nsa_shipment) {
                            if (!in_array($nsa_shipment->id, $valid_shipments)) {
                                if ($nsa_shipment) {
                                    V2AdminPickupsController::cancel($nsa_shipment->id);

                                    $status_id = 2;

                                    if (in_array($nsa_shipment->user_id, [7762, 10354])) {
                                        $admin_id = Auth::id();
                                    }
                                    else {
                                        $admin_id = 50;
                                    }

                                    ShipmentsJourneyController::add($nsa_shipment->id, $status_id, $status_id, NULL, NULL, NULL, $admin_id);

                                    if (in_array($nsa_shipment->user_id, [10354])) {
                                        if ($nsa_shipment->pickup_address->city_id != $nsa_shipment->consignee_city_id) {
                                            $status_id = 4;

                                            ShipmentsJourneyController::add($nsa_shipment->id, $status_id, $status_id, NULL, NULL, NULL, $admin_id);
                                        }
                                    }

                                    $nsa_shipment->shipper_status_id = $status_id;
                                    $nsa_shipment->consignee_status_id = $status_id;

                                    if (in_array($nsa_shipment->user_id, [7762, 10354])) {
                                        $nsa_shipment->actual_weight = $nsa_shipment->estimated_weight;
                                    }
                                    else {
                                        $nsa_shipment->actual_weight = 0.10;
                                    }

                                    $nsa_shipment->save();

                                    ShipmentChargesController::weight($nsa_shipment->id);
                                    ShipmentChargesController::cash_handling($nsa_shipment->id);
                                    ShipmentChargesController::insurance($nsa_shipment->id);
                                    ShipmentChargesController::fuel_surcharge($nsa_shipment->id);
                                    $valid_shipments[] = $nsa_shipment->id;
                                }
                            }
                        }
                    }
                    $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                        return $row . ': ' . $tracking_number;
                    }, array_keys($tracking_numbers), $tracking_numbers));

                    return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipment(s) marked as Arrived with Tracking Number(s):' . PHP_EOL . $tracking_numbers]);
                } else {
                    $errors = array_map(function ($row, $errors) {
                        return $row . ':' . PHP_EOL . implode(' | ', $errors);
                    }, array_keys($errors), $errors);

                    return redirect()->back()->withErrors($errors);
                }
            } else {
                return redirect()->back()->with('error', 'No Shipments in File');
            }
        }
    }

    public function delivery_index()
    {
        return view('admin.telenor.delivery');
    }

    public function delivery_submit(Request $request)
    {

        $names = [
            'tracking_number' => 'Tracking Number',
            'received_refused_by' => 'Received/Refused By',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
        ];
        $rules = [
            'tracking_number' => ['required', 'integer', Rule::exists('shipments', 'tracking_number')],
            'received_refused_by' => [],
        ];
        $fields = [0 => 'tracking_number', 1 => 'received_refused_by'];

        if ($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number', 'Received/Refused By'];

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
                        if (!empty(trim($row['tracking_number'])) || !empty(trim($row['received_refused_by']))) {
                            if (empty($tracking_ids)) {

                                $tracking_ids[] = $row['tracking_number'];
                                $tracking_id_row[$row['tracking_number']] = $row_id;
                            } else {
                                if (in_array($row['tracking_number'], $tracking_ids)) {
                                    $errors['Row #' . $row_id][] = 'Same Tracking Number as of Row #' . $tracking_id_row[$row['tracking_number']];
                                } else {
                                    $tracking_ids[] = $row['tracking_number'];
                                    $tracking_id_row[$row['tracking_number']] = $row_id;
                                }
                            }
                        }

                        $settings = GlobalSettings::where('type', 'nsa_accounts');
                        $nsa_accounts = array();
                        if ($settings->exists()) {
                            $settings = $settings->first();
                            $nsa_accounts = array_map('intval', explode(',', $settings->text));
                        }
                        if (count($nsa_accounts) > 0) {
                            if (!Shipment::where('tracking_number', $row['tracking_number'])->whereIn('user_id', $nsa_accounts)->whereIn('shipper_status_id', [2, 4])->exists()) {
                                $errors['Row #' . $row_id][] = 'Shipment can\'t be updated with Tracking Number #' . $row['tracking_number'];
                            }
                        } else {
                            $errors['Row #' . $row_id][] = 'Nsa Account Not Found' . $row['tracking_number'];
                        }
                    }
                }
                if (empty($errors)) {
                    $tracking_numbers = array();
                    $shipment_ids = array();

                    foreach ($rows as $key => $row) {
                        $row_id = $key + 2;
                        $tracking = trim($row['tracking_number']);
                        $received_refused_by = trim($row['received_refused_by']);
                        $shipment_details = Shipment::where('tracking_number', $tracking)->first();
                        $shipment_id = $shipment_details->id;
                        $shipment_ids[] = $shipment_id;


                        $tracking_numbers['Row #' . $row_id] = $tracking;
                    }

                    $nsa_shipments = Shipment::whereIn('id', $shipment_ids);

                    if ($nsa_shipments->exists()) {
                        $nsa_shipments = $nsa_shipments->get();
                        $settings = GlobalSettings::where('type', 'nsa_accounts')->first();

                        $valid_shipments = array();
                        $shipments_count = 0;
                        $total_cod_amount = 0;
                        foreach ($nsa_shipments as $nsa_shipment) {
                            if (in_array($nsa_shipment->user_id, [7762, 10354])) {
                                $rider_id = 1837;
                                $admin_id = Auth::id();
                            }
                            else {
                                $rider_id = $settings->setting_value;
                                $admin_id = 50;
                            }

                            if (!in_array($nsa_shipment->id, $valid_shipments)) {
                                if ($nsa_shipment) {
                                    $valid_shipments[] = $nsa_shipment->id;
                                    $shipments_count++;

                                    if ($nsa_shipment->booking_type_id != 4 || ($nsa_shipment->booking_type_id == 4 && $nsa_shipment->charges_mode_id == 2)) {
                                        $total_cod_amount += $nsa_shipment->amount;
                                    }
                                }
                            }
                        }

                        $order = false;
                        if ($shipments_count != 0) {
                            $note = DeliveryNote::create([
                                'hub_id' => 202,
                                'rider_id' => $rider_id,
                                'route_id' => 2,
                                'shipments_count' => $shipments_count,
                                'admin_id' => $admin_id,
                                'total_cod_amount' => $total_cod_amount,
                                'password' => NULL,
                                'last_updated_at' => Carbon::now(),
                                'special_rider' => 0,
                                'order' => $order
                            ]);

                            if ($note) {
                                if (!$order) {  //Default
                                    sort($valid_shipments); //sort_valid_shipments;
                                }
                                $serial = 1;
                                foreach ($valid_shipments as $index => $shipment) {
                                    DeliveryNoteShipment::create([
                                        'delivery_note_id' => $note->id,
                                        'shipment_id' => $shipment,
                                        'notification' => 0,
                                        'rider_information' => 0,
                                        'ordering' => $serial
                                    ]);
                                    $serial++;
                                }
                        foreach ($valid_shipments as $index => $shipment) {
                            $received_refused_by = '';

                            $shipment_data = Shipment::find($shipment);

                            if (in_array($shipment_data->user_id, [7762, 10354])) {
                                $rider_id = 1837;
                                $admin_id = Auth::id();
                            }
                            else {
                                $rider_id = $settings->setting_value;
                                $admin_id = 50;
                            }

                            $shipment_data->shipper_status_id = 14;
                            $shipment_data->consignee_status_id = 14;
                            $shipment_data->save();
                            foreach ($rows as $key => $row) {
                                if ($row['tracking_number'] == $shipment_data->tracking_number) {
                                    $received_refused_by = $row['received_refused_by'];
                                }
                            }

                            ShipmentsJourneyController::add($shipment, 5, 5, NULL, NULL, NULL, $admin_id, $note->id, $rider_id);
                            ShipmentsJourneyController::add($shipment, 14, 14, NULL, NULL, NULL, $admin_id, $note->id, NULL, 0,$received_refused_by);
                            ShipmentsJourneyController::add($shipment, 14, 14, NULL, NULL, NULL, $admin_id, $note->id, NULL, 1,$received_refused_by);
                            DeliveryNoteShipment::where(['delivery_note_id' => $note->id, 'shipment_id' => $shipment_data->id])->update(['status' => 1]);
                        }
                        // foreach ($valid_shipments as $index => $shipment) {
                        //     $shipment_data = Shipment::find($shipment);
                        //     $shipment_data->shipper_status_id = 14;
                        //     $shipment_data->consignee_status_id = 14;
                        //     $shipment_data->save();

                        //     // dd($rows[0]); it has both tracking and received_refused
                        //     ShipmentsJourneyController::add($shipment, 5, 5, NULL, NULL, NULL, 50, $note->id, $rider_id);
                        //     ShipmentsJourneyController::add($shipment, 14, 14, NULL, NULL, NULL, 50, $note->id, NULL, 0);
                        //     ShipmentsJourneyController::add($shipment, 14, 14, NULL, NULL, NULL, 50, $note->id, NULL, 1);

                        //     DeliveryNoteShipment::where(['delivery_note_id' => $note->id, 'shipment_id' => $shipment_data->id])->update(['status' => 1]);
                        // }   

                                DeliveryNote::where('id', $note->id)->update(['delivered_shipments' => 0, 'verified_by' => $admin_id, 'received_cod_amount' => 0, 'status' => 1, 'last_updated_at' => Carbon::now(), 'status_verified_at' => Carbon::now()]);
                            }
                        }
                    }
                    $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                        return $row . ': ' . $tracking_number;
                    }, array_keys($tracking_numbers), $tracking_numbers));

                    return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipment(s) marked as delivered with Tracking Number(s):' . PHP_EOL . $tracking_numbers]);
                } else {
                    $errors = array_map(function ($row, $errors) {
                        return $row . ':' . PHP_EOL . implode(' | ', $errors);
                    }, array_keys($errors), $errors);

                    return redirect()->back()->withErrors($errors);
                }
            } else {
                return redirect()->back()->with('error', 'No Shipments in File');
            }
        }
    }

    public function return_index()
    {
        return view('admin.telenor.return');
    }

    public function return_shipment_info(Request $request)
    {
        $tracking_number = $request->tracking_number;
        $settings = GlobalSettings::where('type', 'nsa_accounts');
        $nsa_accounts = array();
        if ($settings->exists()) {
            $settings = $settings->first();
            $nsa_accounts = array_map('intval', explode(',', $settings->text));
        }
        $shipment = Shipment::where('tracking_number', $tracking_number)->whereIn('shipper_status_id', [2, 4])->whereIn('user_id', $nsa_accounts);
        if ($shipment->exists()) {
            $shipment = $shipment->select('shipments.id as id', 'shipments.tracking_number as tracking_number')->first();
            $data['id'] = $shipment->id;
            $data['tracking_number'] = $shipment->tracking_number;
            ShipmentScanningJourneyController::add($shipment->id,24,1,Auth::id(),null,null);
            return response()->json(['status' => 1, 'success' => 'Shipment Added Successfully', 'details' => $data]);
        } else {
            return response()->json(['status' => 0, 'error' => 'Shipment can\'t be updated']);
        }
    }

    public function return_submit(Request $request)
    {
        $shipment_ids = explode(',', $request->shipment_ids);
        $nsa_shipments = Shipment::whereIn('id', $shipment_ids)->whereIn('shipper_status_id', [2, 4]);
        if ($nsa_shipments->exists()) {
            $nsa_shipments = $nsa_shipments->get();
            $settings = GlobalSettings::where('type', 'nsa_accounts')->first();
            $rider_id = $settings->setting_value;
            $valid_shipments = array();
            $shipments_count = 0;
            $total_cod_amount = 0;
            foreach ($nsa_shipments as $nsa_shipment) {
                if (!in_array($nsa_shipment->id, $valid_shipments)) {
                    if ($nsa_shipment) {
                        $valid_shipments[] = $nsa_shipment->id;
                        $shipments_count++;

                        if ($nsa_shipment->booking_type_id != 4 || ($nsa_shipment->booking_type_id == 4 && $nsa_shipment->charges_mode_id == 2)) {
                            $total_cod_amount += $nsa_shipment->amount;
                        }
                    }
                }
            }

            $order = false;
            if ($shipments_count != 0) {
                $note = DeliveryNote::create([
                    'hub_id' => 202,
                    'rider_id' => $rider_id,
                    'route_id' => 2,
                    'shipments_count' => $shipments_count,
                    'admin_id' => 50,
                    'total_cod_amount' => $total_cod_amount,
                    'password' => NULL,
                    'last_updated_at' => Carbon::now(),
                    'special_rider' => 0,
                    'order' => $order
                ]);

                if ($note) {
                    if (!$order) {  //Default
                        sort($valid_shipments); //sort_valid_shipments;
                    }
                    $serial = 1;
                    foreach ($valid_shipments as $index => $shipment) {
                        DeliveryNoteShipment::create([
                            'delivery_note_id' => $note->id,
                            'shipment_id' => $shipment,
                            'notification' => 0,
                            'rider_information' => 0,
                            'ordering' => $serial
                        ]);
                        $serial++;
                    }

                    foreach ($valid_shipments as $index => $shipment) {
                        $shipment_data = Shipment::find($shipment);
                        $shipment_data->shipper_status_id = 20;
                        $shipment_data->consignee_status_id = 20;
                        $shipment_data->save();

                        ShipmentsJourneyController::add($shipment, 5, 5, NULL, NULL, NULL, 50, $note->id, $rider_id);
                        ShipmentsJourneyController::add($shipment, 12, 12, 34, NULL, NULL, 50, $note->id, NULL, 0);
                        ShipmentsJourneyController::add($shipment, 20, 20, 34, NULL, NULL, 50, $note->id, NULL, 1);

                        DeliveryNoteShipment::where(['delivery_note_id' => $note->id, 'shipment_id' => $shipment_data->id])->update(['status' => 1]);
                    }

                    DeliveryNote::where('id', $note->id)->update(['delivered_shipments' => 0, 'verified_by' => 50, 'received_cod_amount' => 0, 'status' => 1, 'last_updated_at' => Carbon::now(), 'status_verified_at' => Carbon::now()]);
                }

                $note = ReturnNote::create(['hub_id' => 202, 'rider_id' => 274, 'route_id' => 2, 'shipments_count' => $shipments_count, 'admin_id' => 50]);
                if ($note) {
                    foreach ($valid_shipments as $shipment_id) {
                        $shipment = Shipment::where('id', $shipment_id);

                        $shipment = $shipment->first();
                        ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $shipment_id]);
                        $shipment->shipper_status_id = 23;
                        $shipment->consignee_status_id = 23;
                        $shipment->save();
                        ShipmentsJourneyController::add($shipment->id, 23, 23, NULL, NULL, NULL, 50, $note->id, $rider_id);
                    }
                }
            }
        }
        return redirect()->back()->with('success', 'Shipment(s) Marked as Return Successfully');
    }

    public function order_id_index()
    {
        return view('admin.telenor.order_ids');
    }

    public function order_id_submit(Request $request)
    {
        $names = [
            'tracking_number' => 'Tracking Number',
            'order_id' => 'Order ID',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
        ];
        $rules = [
            'tracking_number' => ['required', 'integer', Rule::exists('shipments', 'tracking_number')],
            'order_id' => ['nullable', 'filled', 'between:0,100']
        ];
        $fields = [0 => 'tracking_number', 1 => 'order_id'];

        if ($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number', 'Order ID'];

            if (isset($spreadsheet)) {
                $header_correct = TRUE;

                foreach ($spreadsheet[0] as $index => $header_value) {
                    if (!isset($header[$index]) || $header_value != $header[$index]) {
                        $header_correct = FALSE;
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
                            } else {
                                if (in_array($row['tracking_number'], $tracking_ids)) {
                                    $errors['Row #' . $row_id][] = 'Same Tracking Number as of Row #' . $tracking_id_row[$row['tracking_number']];
                                } else {
                                    $tracking_ids[] = $row['tracking_number'];
                                    $tracking_id_row[$row['tracking_number']] = $row_id;
                                }
                            }
                        }

                        $settings = GlobalSettings::where('type', 'nsa_accounts');
                        $nsa_accounts = array();
                        if ($settings->exists()) {
                            $settings = $settings->first();
                            $nsa_accounts = array_map('intval', explode(',', $settings->text));
                        }
                        if (count($nsa_accounts) > 0) {
                            if (!Shipment::where('tracking_number', $row['tracking_number'])->whereIn('user_id', $nsa_accounts)->exists()) {
                                $errors['Row #' . $row_id][] = 'Shipment not found with Tracking Number #' . $row['tracking_number'];
                            }
                        } else {
                            $errors['Row #' . $row_id][] = 'Nsa Account Not Found' . $row['tracking_number'];
                        }
                    }
                }
                if (empty($errors)) {
                    $tracking_numbers = array();
                    foreach ($rows as $key => $row) {
                        $row_id = $key + 2;
                        $tracking = trim($row['tracking_number']);
                        $order_id = trim($row['order_id']);


                        $shipment_details = Shipment::where('tracking_number', $tracking)->first();
                        if ($shipment_details) {
                            $shipment_details->order_id = $order_id;
                            $shipment_details->save();
                        }

                        $tracking_numbers['Row #' . $row_id] = $tracking;
                    }
                    $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                        return $row . ': ' . $tracking_number;
                    }, array_keys($tracking_numbers), $tracking_numbers));

                    return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipment(s) Updated with Tracking Number(s):' . PHP_EOL . $tracking_numbers]);
                } else {
                    $errors = array_map(function ($row, $errors) {
                        return $row . ':' . PHP_EOL . implode(' | ', $errors);
                    }, array_keys($errors), $errors);

                    return redirect()->back()->withErrors($errors);
                }
            } else {
                return redirect()->back()->with('error', 'No Shipments in File');
            }
        }
    }

    public function bulk_return_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),266);
        return view('admin.telenor.bulk_return');
    }
    public function bulk_return_submit(Request $request){
        $names = [
            'tracking_number' => 'Tracking Number',
            'received_by' => 'Received/Refused By',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
        ];
        $rules = [
            'tracking_number' => ['required', 'integer', Rule::exists('shipments', 'tracking_number')],
            'received_by' => [],
        ];
        $fields = [0 => 'tracking_number', 1 => 'received_by'];

        if ($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number', 'Returned By'];

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
                        if (!empty(trim($row['tracking_number'])) || !empty(trim($row['received_by']))) {
                            if (empty($tracking_ids)) {

                                $tracking_ids[] = $row['tracking_number'];
                                $tracking_id_row[$row['tracking_number']] = $row_id;
                            } else {
                                if (in_array($row['tracking_number'], $tracking_ids)) {
                                    $errors['Row #' . $row_id][] = 'Same Tracking Number as of Row #' . $tracking_id_row[$row['tracking_number']];
                                } else {
                                    $tracking_ids[] = $row['tracking_number'];
                                    $tracking_id_row[$row['tracking_number']] = $row_id;
                                }
                            }
                        }

                        $settings = GlobalSettings::where('type', 'nsa_accounts');
                        $nsa_accounts = array();
                        if ($settings->exists()) {
                            $settings = $settings->first();
                            $nsa_accounts = array_map('intval', explode(',', $settings->text));
                        }
                        if (count($nsa_accounts) > 0) {
                            if (!Shipment::where('tracking_number', $row['tracking_number'])->whereIn('user_id', $nsa_accounts)->whereIn('shipper_status_id', [2, 4, 13])->exists()) {
                                $errors['Row #' . $row_id][] = 'Shipment can\'t be updated with Tracking Number #' . $row['tracking_number'];
                            }
                        } else {
                            $errors['Row #' . $row_id][] = 'Nsa Account Not Found' . $row['tracking_number'];
                        }
                    }
                }
                if (empty($errors)) {
                    $tracking_numbers = array();
                    $shipment_ids = array();

                    foreach ($rows as $key => $row) {
                        $row_id = $key + 2;
                        $tracking = trim($row['tracking_number']);
                        $received_by = trim($row['received_by']);
                        $shipment_details = Shipment::where('tracking_number', $tracking)->first();
                        $shipment_id = $shipment_details->id;
                        $shipment_ids[] = $shipment_id;


                        $tracking_numbers['Row #' . $row_id] = $tracking;
                    }

                    $nsa_shipments = Shipment::whereIn('id', $shipment_ids);

                    if ($nsa_shipments->exists()) {
                        $nsa_shipments = $nsa_shipments->get();
                        $settings = GlobalSettings::where('type', 'nsa_accounts')->first();
                        $rider_id = $settings->setting_value;
                        $valid_shipments = array();
                        $shipments_count = 0;
                        $total_cod_amount = 0;
                        foreach ($nsa_shipments as $nsa_shipment) {
                            if (!in_array($nsa_shipment->id, $valid_shipments)) {
                                if ($nsa_shipment) {
                                    $valid_shipments[] = $nsa_shipment->id;
                                    $shipments_count++;

                                    if ($nsa_shipment->booking_type_id != 4 || ($nsa_shipment->booking_type_id == 4 && $nsa_shipment->charges_mode_id == 2)) {
                                        $total_cod_amount += $nsa_shipment->amount;
                                    }
                                }
                            }
                        }

                        $order = false;
                        if ($shipments_count != 0) {
                            $note = DeliveryNote::create([
                                'hub_id' => 202,
                                'rider_id' => $rider_id,
                                'route_id' => 2,
                                'shipments_count' => $shipments_count,
                                'admin_id' => 50,
                                'total_cod_amount' => $total_cod_amount,
                                'password' => NULL,
                                'last_updated_at' => Carbon::now(),
                                'special_rider' => 0,
                                'order' => $order
                            ]);
                            

                            if ($note) {
                                if (!$order) {  //Default
                                    sort($valid_shipments); //sort_valid_shipments;
                                }
                                $serial = 1;
                                foreach ($valid_shipments as $index => $shipment) {
                                    DeliveryNoteShipment::create([
                                        'delivery_note_id' => $note->id,
                                        'shipment_id' => $shipment,
                                        'notification' => 0,
                                        'rider_information' => 0,
                                        'ordering' => $serial
                                    ]);
                                    $serial++;
                                }
                                foreach ($valid_shipments as $index => $shipment) {
                                    $received_by = '';

                                    $shipment_data = Shipment::find($shipment);
                                    $shipment_data->shipper_status_id = 20;
                                    $shipment_data->consignee_status_id = 20;
                                    $shipment_data->save();

                                    foreach ($rows as $key => $row) {
                                        if ($row['tracking_number'] == $shipment_data->tracking_number) {
                                            $received_by = $row['received_by'];
                                        }
                                    }

                                    ShipmentsJourneyController::add($shipment, 5, 5, NULL, NULL, NULL, 50, $note->id, $rider_id);
                                    ShipmentsJourneyController::add($shipment, 12, 12, 34, NULL, NULL, 50, $note->id, NULL, 0,$received_by);
                                    ShipmentsJourneyController::add($shipment, 20, 20, 34, NULL, NULL, 50, $note->id, NULL, 1,$received_by);
                                    DeliveryNoteShipment::where(['delivery_note_id' => $note->id, 'shipment_id' => $shipment_data->id])->update(['status' => 1]);
                                }

                                DeliveryNote::where('id', $note->id)->update(['delivered_shipments' => 0, 'verified_by' => 50, 'received_cod_amount' => 0, 'status' => 1, 'last_updated_at' => Carbon::now(), 'status_verified_at' => Carbon::now()]);
                            }

                            $note = ReturnNote::create(['hub_id' => 202, 'rider_id' => 274, 'route_id' => 2, 'shipments_count' => $shipments_count, 'admin_id' => 50, 'status' => 3]);
                            if ($note) {
                                foreach ($valid_shipments as $shipment_id) {
                                    $shipment = Shipment::where('id', $shipment_id);

                                    $shipment = $shipment->first();
                                    ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $shipment_id, 'status' => 1]);
                                    $shipment->shipper_status_id = 25;
                                    $shipment->consignee_status_id = 25;
                                    $shipment->save();
                                    ShipmentsJourneyController::add($shipment->id, 23, 23, NULL, NULL, NULL, 50, $note->id, $rider_id);
                                    ShipmentsJourneyController::add($shipment->id, 25, 25, NULL, NULL, NULL, 50, $note->id, $rider_id);
                                }
                            }
                        }
                    }
                    $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                        return $row . ': ' . $tracking_number;
                    }, array_keys($tracking_numbers), $tracking_numbers));

                    return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipment(s) marked as returned with Tracking Number(s):' . PHP_EOL . $tracking_numbers]);
                } else {
                    $errors = array_map(function ($row, $errors) {
                        return $row . ':' . PHP_EOL . implode(' | ', $errors);
                    }, array_keys($errors), $errors);

                    return redirect()->back()->withErrors($errors);
                }
            } else {
                return redirect()->back()->with('error', 'No Shipments in File');
            }
        }
    }

    public function carrefour_arrival_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),439);
        return view('admin.carrefour.arrival');
    }

    public function carrefour_arrival_submit(Request $request)
    {
        $names = [
            'tracking_number' => 'Tracking Number',
            'weight' => 'Weight',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
            'numeric' => ':attribute must be a Number.',
        ];
        $rules = [
            'tracking_number' => ['required', 'integer', Rule::exists('shipments', 'tracking_number')],
            'weight' => ['required', 'numeric', 'between:0.1,100000'],
        ];
        $fields = [0 => 'tracking_number', 1 => 'weight'];

        if ($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number', 'Weight'];

            if (isset($spreadsheet)) {
                $header_correct = TRUE;

                foreach ($spreadsheet[0] as $index => $header_value) {
                    if (!isset($header[$index]) || $header_value != $header[$index]) {
                        $header_correct = FALSE;
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
                $tracking_ids = array();
                $weights = array();
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
                            } else {
                                if (in_array($row['tracking_number'], $tracking_ids)) {
                                    $errors['Row #' . $row_id][] = 'Same Tracking Number as of Row #' . $tracking_id_row[$row['tracking_number']];
                                } else {
                                    $tracking_ids[] = $row['tracking_number'];
                                    $tracking_id_row[$row['tracking_number']] = $row_id;
                                }
                            }
                        }

                        $settings = GlobalSettings::where('type', 'carrefour_accounts');
                        $carrefour_accounts = array();
                        if ($settings->exists()) {
                            $settings = $settings->first();
                            $carrefour_accounts = array_map('intval', explode(',', $settings->text));
                        }
                        if (count($carrefour_accounts) > 0) {
                            if (!Shipment::where('tracking_number', $row['tracking_number'])->whereIn('user_id', $carrefour_accounts)->where('shipper_status_id', 1)->exists()) {
                                $errors['Row #' . $row_id][] = 'Shipment not found with Tracking Number #' . $row['tracking_number'];
                            }
                        } else {
                            $errors['Row #' . $row_id][] = 'Carrefour Account Not Found' . $row['tracking_number'];
                        }
                    }
                }
                if (empty($errors)) {
                    $tracking_numbers = array();
                    $shipment_ids = array();
                    foreach ($rows as $key => $row) {
                        $row_id = $key + 2;
                        $tracking = trim($row['tracking_number']);

                        $shipment_details = Shipment::where('tracking_number', $tracking)->first();
                        $shipment_id = $shipment_details->id;
                        $shipment_ids[] = $shipment_id;
                        $weights[$shipment_id] = trim($row['weight']);


                        $tracking_numbers['Row #' . $row_id] = $tracking;
                    }

                    $carrefour_shipments = Shipment::whereIn('id', $shipment_ids);

                    if ($carrefour_shipments->exists()) {
                        $carrefour_shipments = $carrefour_shipments->get();
                        $valid_shipments = array();

                        foreach ($carrefour_shipments as $carrefour_shipment) {
                            if (!in_array($carrefour_shipment->id, $valid_shipments)) {
                                if ($carrefour_shipment) {
                                    V2AdminPickupsController::cancel($carrefour_shipment->id);

                                    $status_id = 2;

                                    $admin_id = 50;

                                    ShipmentsJourneyController::add($carrefour_shipment->id, $status_id, $status_id, NULL, NULL, NULL, $admin_id);

                                    $carrefour_shipment->shipper_status_id = $status_id;
                                    $carrefour_shipment->consignee_status_id = $status_id;

                                    $carrefour_shipment->actual_weight = $weights[$carrefour_shipment->id];

                                    $carrefour_shipment->save();

                                    ShipmentChargesController::weight($carrefour_shipment->id);
                                    ShipmentChargesController::cash_handling($carrefour_shipment->id);
                                    ShipmentChargesController::insurance($carrefour_shipment->id);
                                    ShipmentChargesController::fuel_surcharge($carrefour_shipment->id);
                                    $valid_shipments[] = $carrefour_shipment->id;
                                }
                            }
                        }
                    }
                    $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                        return $row . ': ' . $tracking_number;
                    }, array_keys($tracking_numbers), $tracking_numbers));

                    return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipment(s) marked as Arrived with Tracking Number(s):' . PHP_EOL . $tracking_numbers]);
                } else {
                    $errors = array_map(function ($row, $errors) {
                        return $row . ':' . PHP_EOL . implode(' | ', $errors);
                    }, array_keys($errors), $errors);

                    return redirect()->back()->withErrors($errors);
                }
            } else {
                return redirect()->back()->with('error', 'No Shipments in File');
            }
        }
    }

    public function carrefour_delivery_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),455);
        $routes = Route::where('status', 1);

        if (session('role_id') != 1) {
            $routes = $routes->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }

        $routes = $routes->get();
        $operation_rider_category = OperationRidersCategory::all();
        return view('admin.carrefour.delivery')->with(['operation_rider_category' => $operation_rider_category, 'routes' => $routes]);
    }
    public function carrefour_delivery_submit(Request $request)
    {
        $names = [
            'tracking_number' => 'Tracking Number',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
        ];
        $rules = [
            'tracking_number' => ['required', 'integer', Rule::exists('shipments', 'tracking_number')],
        ];
        $fields = [0 => 'tracking_number'];

        if ($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

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
                    return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
                } else {
                    unset($spreadsheet[0]);
                }
            }
            $valid_fields = true;
            if (!empty($spreadsheet) || !isset($spreadsheet)) {
                $rows = array();
                foreach ($spreadsheet as $spreadsheet_row) {
                    $row = array();

                    foreach ($spreadsheet_row as $key => $value) {
                        if(array_key_exists($key, $fields)){
                            $row[$fields[$key]] = $value;
                        }
                        else{
                            $valid_fields = false;
                        }
                    }

                    $rows[] = $row;
                }

                $errors = array();
                if ($valid_fields){
                    unset($spreadsheet);
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
                                } else {
                                    if (in_array($row['tracking_number'], $tracking_ids)) {
                                        $errors['Row #' . $row_id][] = 'Same Tracking Number as of Row #' . $tracking_id_row[$row['tracking_number']];
                                    } else {
                                        $tracking_ids[] = $row['tracking_number'];
                                        $tracking_id_row[$row['tracking_number']] = $row_id;
                                    }
                                }
                            }

                            $settings = GlobalSettings::where('type', 'carrefour_accounts');
                            $carrefour_accounts = array();
                            if ($settings->exists()) {
                                $settings = $settings->first();
                                $carrefour_accounts = array_map('intval', explode(',', $settings->text));
                            }
                            if (count($carrefour_accounts) > 0) {
                                if (!Shipment::where('tracking_number', $row['tracking_number'])->whereIn('user_id', $carrefour_accounts)->whereIn('shipper_status_id', [2, 7, 8])->exists()) {
                                    $errors['Row #' . $row_id][] = 'Shipment can\'t be updated with Tracking Number #' . $row['tracking_number'];
                                }
                            } else {
                                $errors['Row #' . $row_id][] = 'Carrefour Account Not Found' . $row['tracking_number'];
                            }
                        }
                    }
                    if (empty($errors)) {
                        $tracking_numbers = array();
                        $shipment_ids = array();

                        foreach ($rows as $key => $row) {
                            $row_id = $key + 2;
                            $tracking = trim($row['tracking_number']);
                            $shipment_details = Shipment::where('tracking_number', $tracking)->first();
                            $shipment_id = $shipment_details->id;
                            $shipment_ids[] = $shipment_id;


                            $tracking_numbers['Row #' . $row_id] = $tracking;
                        }

                        $carrefour_shipments = Shipment::whereIn('id', $shipment_ids);
                        $valid_tracking_numbers = array();
                        $invalid_tracking_numbers = array();
                        $rider_id = $request->rider;
                        $route_id = $request->route;
                        $rider = Rider::find($rider_id);
                        if ($carrefour_shipments->exists()) {
                            $carrefour_shipments = $carrefour_shipments->get();

                            $valid_shipments = array();
                            $shipments_count = 0;
                            $total_cod_amount = 0;
                            foreach ($carrefour_shipments as $carrefour_shipment) {
                                if ($carrefour_shipment) {
                                    if (!in_array($carrefour_shipment->id, $valid_shipments)) {
                                        $check = false;
                                        if($rider){
                                            if($rider->city->hub_id == $carrefour_shipment->consignee_city->hub_id){
                                                if($carrefour_shipment->consignee_city->hub_id == $carrefour_shipment->pickup_address->city->hub_id){
                                                    if(in_array($carrefour_shipment->shipper_status_id, [2, 7, 8])){
                                                        $check = true;
                                                    }
                                                }
//                                                else{
//                                                    if(in_array($carrefour_shipment->shipper_status_id, [2, 7, 8])){
//                                                        $check = true;
//                                                    }
//                                                }
                                            }
                                        }
                                        if($check){
                                            $valid_shipments[] = $carrefour_shipment->id;
                                            $valid_tracking_numbers[] = $carrefour_shipment->tracking_number;
                                            $shipments_count++;

                                            if ($carrefour_shipment->booking_type_id != 4 || ($carrefour_shipment->booking_type_id == 4 && $carrefour_shipment->charges_mode_id == 2)) {
                                                $total_cod_amount += $carrefour_shipment->amount;
                                            }
                                        }
                                        else{
                                            $invalid_tracking_numbers[] = $carrefour_shipment->tracking_number;
                                        }
                                    }
                                }
                            }
                            $order = false;
                            if ($shipments_count != 0) {
                                if($rider){
                                    $note = DeliveryNote::create([
                                        'hub_id' => $rider->city->hub_id,
                                        'rider_id' => $rider_id,
                                        'route_id' => $route_id,
                                        'shipments_count' => $shipments_count,
                                        'admin_id' => Auth::id(),
                                        'total_cod_amount' => $total_cod_amount,
                                        'password' => NULL,
                                        'last_updated_at' => Carbon::now(),
                                        'special_rider' => 0,
                                        'order' => $order
                                    ]);

                                    if ($note) {
                                        if (!$order) {  //Default
                                            sort($valid_shipments); //sort_valid_shipments;
                                        }
                                        $serial = 1;
                                        foreach ($valid_shipments as $index => $shipment) {
                                            DeliveryNoteShipment::create([
                                                'delivery_note_id' => $note->id,
                                                'shipment_id' => $shipment,
                                                'notification' => 0,
                                                'rider_information' => 0,
                                                'ordering' => $serial
                                            ]);
                                            $serial++;
                                        }
                                        foreach ($valid_shipments as $index => $shipment) {

                                            $shipment_data = Shipment::find($shipment);

                                            $shipment_data->shipper_status_id = 5;
                                            $shipment_data->consignee_status_id = 5;
                                            $shipment_data->save();

                                            ShipmentsJourneyController::add($shipment, 5, 5, NULL, NULL, NULL, Auth::id(), $note->id, $rider_id);
                                        }

                                        //rider attendance
                                        $attendance_datetime = Carbon::now()->format('Y-m-d H:i:s');
                                        $attendance_date = Carbon::now()->format('Y-m-d');

                                        $rider_attendance = EmployeeAttendance::where('employee_id', $rider->employee_id)
                                            ->whereDate('attendance_date', $attendance_date)
                                            ->where('employee_type', 2);
                                        if (!$rider_attendance->exists()) {
                                            $rider_attendance = new EmployeeAttendance();
                                            $rider_attendance->employee_id = $rider->employee_id;
                                            $rider_attendance->employee_type = 2;
                                            $rider_attendance->attendance_date = $attendance_date;
                                            $rider_attendance->clock_in_datetime = $attendance_datetime;
                                            $rider_attendance->clock_in_latitude = '0';
                                            $rider_attendance->clock_in_longitude = '0';
                                            $rider_attendance->save();

                                            $rider_attendance_action = new EmployeeAttendanceActionLog();
                                            $rider_attendance_action->employee_id = $rider->employee_id;
                                            $rider_attendance_action->employee_type = 2;
                                            $rider_attendance_action->action_id = 1;
                                            $rider_attendance_action->attendance_date = $attendance_date;
                                            $rider_attendance_action->action_date = $attendance_datetime;
                                            $rider_attendance_action->latitude = '0';
                                            $rider_attendance_action->longitude = '0';
                                            $rider_attendance_action->save();
                                        }
                                        //rider attendance end
                                        $success_check = false;
                                        $error_check = false;
                                        if(count($valid_tracking_numbers) > 0){
                                            $success_check = true;
                                            $valid_tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                                                return $row + 1 . ': ' . $tracking_number;
                                            }, array_keys($valid_tracking_numbers), $valid_tracking_numbers));
                                        }
                                        if(count($invalid_tracking_numbers) > 0){
                                            $error_check = true;
                                            $invalid_tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                                                return $row + 1 . ': ' . $tracking_number;
                                            }, array_keys($invalid_tracking_numbers), $invalid_tracking_numbers));
                                        }
                                        if($success_check && $error_check){
                                            return redirect()->back()->with(['success' => 'Delivery Note Created, Total ' . count($rows) . ' Shipment(s) with Tracking Number(s):' . PHP_EOL . $valid_tracking_numbers, 'error' => 'Delivery Note cannot be created against shipment(s) with Tracking Number:' . PHP_EOL . $invalid_tracking_numbers, 'print'=>$note->id]);
                                        }
                                        else{
                                            if($success_check){
                                                return redirect()->back()->with(['success' => 'Delivery Note Created, Total ' . count($rows) . ' Shipment(s) with Tracking Number(s):' . PHP_EOL . $valid_tracking_numbers,'print'=>$note->id]);
                                            }
                                            elseif ($error_check){
                                                return redirect()->back()->with(['error' => 'Delivery Note cannot be created against shipment(s) with Tracking Number:' . PHP_EOL . $invalid_tracking_numbers,'print'=>$note->id]);
                                            }
                                        }
                                    }
                                    else{
                                        return redirect()->back()->with(['error' => 'Delivery Note cannot be created!']);
                                    }
                                }
                                else{
                                    return redirect()->back()->with(['error' => 'Rider not found!']);
                                }
                            }
                            else{
                                return redirect()->back()->with(['error' => 'In-Valid Shipment(s)!']);
                            }
                        }
                        else{
                            return redirect()->back()->with(['error' => 'In-Valid Shipment(s)!']);
                        }
                    } else {
                        $errors = array_map(function ($row, $errors) {
                            return $row . ':' . PHP_EOL . implode(' | ', $errors);
                        }, array_keys($errors), $errors);

                        return redirect()->back()->withErrors($errors);
                    }
                }
                else{
                    $errors[] = 'In-Valid Fields';
                    return redirect()->back()->withErrors($errors);
                }
            } else {
                return redirect()->back()->with('error', 'No Shipments in File');
            }
        } else {
            return redirect()->back()->with('error', 'File not found');
        }
    }

    public function carrefour_return_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),456);
        $routes = Route::where('status', 1);

        if (session('role_id') != 1) {
            $routes = $routes->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }

        $routes = $routes->get();

        $hubs = City::where([['status',1],['hub',1]]);

        if(session('role_id') != 1)
        {
            $hubs = $hubs->WhereIn('id',session('hubs'));
        }

        $hubs = $hubs->get(['id','name']);
        return view('admin.carrefour.return')->with(['hubs' => $hubs, 'routes' => $routes]);
    }
    public function carrefour_return_submit(Request $request){
        $names = [
            'tracking_number' => 'Tracking Number',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
        ];
        $rules = [
            'tracking_number' => ['required', 'integer', Rule::exists('shipments', 'tracking_number')]
        ];
        $fields = [0 => 'tracking_number'];

        if ($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

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
                    return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
                } else {
                    unset($spreadsheet[0]);
                }
            }
            $valid_fields = true;
            if (!empty($spreadsheet) || !isset($spreadsheet)) {
                $rows = array();
                foreach ($spreadsheet as $spreadsheet_row) {
                    $row = array();

                    foreach ($spreadsheet_row as $key => $value) {
                        if(array_key_exists($key, $fields)){
                            $row[$fields[$key]] = $value;
                        }
                        else{
                            $valid_fields = false;
                        }
                    }

                    $rows[] = $row;
                }
                $errors = array();
                if($valid_fields){
                    unset($spreadsheet);
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
                                } else {
                                    if (in_array($row['tracking_number'], $tracking_ids)) {
                                        $errors['Row #' . $row_id][] = 'Same Tracking Number as of Row #' . $tracking_id_row[$row['tracking_number']];
                                    } else {
                                        $tracking_ids[] = $row['tracking_number'];
                                        $tracking_id_row[$row['tracking_number']] = $row_id;
                                    }
                                }
                            }

                            $settings = GlobalSettings::where('type', 'carrefour_accounts');
                            $carrefour_accounts = array();
                            if ($settings->exists()) {
                                $settings = $settings->first();
                                $carrefour_accounts = array_map('intval', explode(',', $settings->text));
                            }
                            if (count($carrefour_accounts) > 0) {
                                if (!Shipment::where('tracking_number', $row['tracking_number'])->whereIn('user_id', $carrefour_accounts)->whereIn('shipper_status_id', [20])->exists()) {
                                    $errors['Row #' . $row_id][] = 'Shipment can\'t be updated with Tracking Number #' . $row['tracking_number'];
                                }
                            } else {
                                $errors['Row #' . $row_id][] = 'Carrefour Account Not Found' . $row['tracking_number'];
                            }
                        }
                    }
                    if (empty($errors)) {
                        $tracking_numbers = array();
                        $shipment_ids = array();

                        foreach ($rows as $key => $row) {
                            $row_id = $key + 2;
                            $tracking = trim($row['tracking_number']);
                            $shipment_details = Shipment::where('tracking_number', $tracking)->first();
                            $shipment_id = $shipment_details->id;
                            $shipment_ids[] = $shipment_id;


                            $tracking_numbers['Row #' . $row_id] = $tracking;
                        }

                        $carrefour_shipments = Shipment::whereIn('id', $shipment_ids);
                        $valid_tracking_numbers = array();
                        $invalid_tracking_numbers = array();
                        $rider_id = $request->rider;
                        $route_id = $request->route;
                        $rider = Rider::find($rider_id);
                        if ($carrefour_shipments->exists()) {
                            $carrefour_shipments = $carrefour_shipments->get();
                            $valid_shipments = array();
                            $shipments_count = 0;
                            $total_cod_amount = 0;
                            foreach ($carrefour_shipments as $carrefour_shipment) {
                                if ($carrefour_shipment) {
                                    if (!in_array($carrefour_shipment->id, $valid_shipments)) {
                                        $check = false;
                                        if($rider) {
                                            if ($rider->city->hub_id == $carrefour_shipment->pickup_address->city->hub_id) {
                                                if ($carrefour_shipment->consignee_city->hub_id == $carrefour_shipment->pickup_address->city->hub_id) {
                                                    if ($carrefour_shipment->shipper_status_id == 20) {
                                                        $check = true;
                                                    }
                                                }
//                                                else {
//                                                    if ($carrefour_shipment->shipper_status_id == 20) {
//                                                        $check = true;
//                                                    }
//                                                }
                                            }
                                        }
                                        if($check){
                                            $valid_shipments[] = $carrefour_shipment->id;
                                            $shipments_count++;
                                            $valid_tracking_numbers[] = $carrefour_shipment->tracking_number;

                                            if ($carrefour_shipment->booking_type_id != 4 || ($carrefour_shipment->booking_type_id == 4 && $carrefour_shipment->charges_mode_id == 2)) {
                                                $total_cod_amount += $carrefour_shipment->amount;
                                            }
                                        }
                                        else{
                                            $invalid_tracking_numbers[] = $carrefour_shipment->tracking_number;
                                        }
                                    }
                                }
                            }

                            $order = false;
                            if ($shipments_count != 0) {
                                $rider = Rider::find($rider_id);
                                if($rider){
                                    $note = ReturnNote::create(['hub_id' => $rider->city->hub_id, 'rider_id' => $rider_id, 'route_id' => $route_id, 'shipments_count' => $shipments_count, 'admin_id' => Auth::id()]);
                                    if ($note) {
                                        foreach ($valid_shipments as $shipment_id) {
                                            $shipment = Shipment::where('id', $shipment_id);

                                            $shipment = $shipment->first();
                                            ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $shipment_id, 'status' => 1]);
                                            $shipment->shipper_status_id = 23;
                                            $shipment->consignee_status_id = 23;
                                            $shipment->save();
                                            ShipmentsJourneyController::add($shipment->id, 23, 23, NULL, NULL, NULL, Auth::id(), $note->id, $rider_id);
                                        }

                                        $success_check = false;
                                        $error_check = false;
                                        if(count($valid_tracking_numbers) > 0){
                                            $success_check = true;
                                            $valid_tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                                                return $row + 1 . ': ' . $tracking_number;
                                            }, array_keys($valid_tracking_numbers), $valid_tracking_numbers));
                                        }
                                        if(count($invalid_tracking_numbers) > 0){
                                            $error_check = true;
                                            $invalid_tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                                                return $row + 1 . ': ' . $tracking_number;
                                            }, array_keys($invalid_tracking_numbers), $invalid_tracking_numbers));
                                        }
                                        if($success_check && $error_check){
                                            return redirect()->back()->with(['success' => 'Return Note Created, Total ' . count($rows) . ' Shipment(s) with Tracking Number(s):' . PHP_EOL . $valid_tracking_numbers, 'error' => 'Return Note cannot be created against shipment(s) with Tracking Number:' . PHP_EOL . $invalid_tracking_numbers, 'print'=>$note->id]);
                                        }
                                        else{
                                            if($success_check){
                                                return redirect()->back()->with(['success' => 'Return Note Created, Total ' . count($rows) . ' Shipment(s) with Tracking Number(s):' . PHP_EOL . $valid_tracking_numbers,'print'=>$note->id]);
                                            }
                                            elseif ($error_check){
                                                return redirect()->back()->with(['error' => 'Return Note cannot be created against shipment(s) with Tracking Number:' . PHP_EOL . $invalid_tracking_numbers,'print'=>$note->id]);
                                            }
                                        }
                                    }
                                    else{
                                        return redirect()->back()->with(['error' => 'Return Note cannot be created!']);
                                    }
                                }
                                else{
                                    return redirect()->back()->with(['error' => 'Rider not found!']);
                                }
                            }
                            else{
                                return redirect()->back()->with(['error' => 'In-Valid Shipment(s)!']);
                            }
                        }
                        else{
                            return redirect()->back()->with(['error' => 'In-Valid Shipment(s)!']);
                        }
                    } else {
                        $errors = array_map(function ($row, $errors) {
                            return $row . ':' . PHP_EOL . implode(' | ', $errors);
                        }, array_keys($errors), $errors);

                        return redirect()->back()->withErrors($errors);
                    }
                }
                else{
                    $errors[] = 'In-Valid Fields';
                    return redirect()->back()->withErrors($errors);
                }
            } else {
                return redirect()->back()->with('error', 'No Shipments in File');
            }
        }else {
            return redirect()->back()->with('error', 'File not found');
        }
    }
    public function bulk_revert_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),435);
        return view('admin.telenor.bulk_revert');
    }

    public function bulk_revert_submit(Request $request){

        $names = [
            'tracking_number' => 'Tracking Number',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
        ];
        $rules = [
            'tracking_number' => ['required', 'integer', Rule::exists('shipments', 'tracking_number')],
        ];
        $fields = [0 => 'tracking_number'];

        if ($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number'];

            if (isset($spreadsheet)) {
                $header_correct = TRUE;

                foreach ($spreadsheet[0] as $index => $header_value) {
                    if (!isset($header[$index]) || $header_value != $header[$index]) {
                        $header_correct = FALSE;
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
                        if (!empty(trim($row['tracking_number'])) /*|| !empty(trim($row['reverted_by']*/) {
                            if (empty($tracking_ids)) {

                                $tracking_ids[] = $row['tracking_number'];
                                $tracking_id_row[$row['tracking_number']] = $row_id;
                            } else {
                                if (in_array($row['tracking_number'], $tracking_ids)) {
                                    $errors['Row #' . $row_id][] = 'Same Tracking Number as of Row #' . $tracking_id_row[$row['tracking_number']];
                                } else {
                                    $tracking_ids[] = $row['tracking_number'];
                                    $tracking_id_row[$row['tracking_number']] = $row_id;
                                }
                            }
                        }

                        $settings = GlobalSettings::where('type', 'nsa_accounts');
                        $nsa_accounts = array();
                        if ($settings->exists()) {
                            $settings = $settings->first();
                            $nsa_accounts = array_map('intval', explode(',', $settings->text));
                        }
                        if (count($nsa_accounts) > 0) {
                            if (!Shipment::where('tracking_number', $row['tracking_number'])->whereIn('user_id', $nsa_accounts)->where('shipper_status_id',14)->exists()) {
                                $errors['Row #' . $row_id][] = 'Shipment can\'t be updated with Tracking Number #' . $row['tracking_number'];
                            }
                        } else {
                            $errors['Row #' . $row_id][] = 'Nsa Account Not Found' . $row['tracking_number'];
                        }
                    }
                }
                if (empty($errors)) {
                    $tracking_numbers = array();
                    $shipment_ids = array();
                    $order = false;
                    foreach ($rows as $key => $row) {
                        $row_id = $key + 2;
                        $tracking = trim($row['tracking_number']);
                      /*  $reverted_by = trim($row['reverted_by']);*/
                        $shipment_details = Shipment::where('tracking_number', $tracking)->first();
                        $shipment_id = $shipment_details->id;
                        $shipment_ids[] = $shipment_id;


                        $tracking_numbers['Row #' . $row_id] = $tracking;
                    }

                    $nsa_shipments = Shipment::whereIn('id', $shipment_ids);

                    if ($nsa_shipments->exists()) {
                        $nsa_shipments = $nsa_shipments->get();
                        $settings = GlobalSettings::where('type', 'nsa_accounts')->first();
                        $rider_id = $settings->setting_value;
                        $valid_shipments = array();
                        foreach ($nsa_shipments as $shipment) {
                            if (!in_array($shipment->id, $valid_shipments)) {
                                $valid_shipments[] = $shipment->id;
                            }
                        }
                    }

                    if (!$order) {  //Default
                        sort($valid_shipments); //sort_valid_shipments;
                    }
                    $serial = 1;

                    foreach ($valid_shipments as $index => $shipment_id) {
                       /* $reverted_by = '';
                        $shipment = Shipment::find($shipment_id);
                        foreach ($rows as $key => $row) {
                            if ($row['tracking_number'] == $shipment->tracking_number) {
                                $reverted_by = $row['reverted_by'];
                            }
                        }*/

                        $delivery_note_shipment = DeliveryNoteShipment::where('shipment_id', $shipment_id)->where('status', 1)->latest()->first();
                        if ($delivery_note_shipment) {

                            $shipment = Shipment::find($shipment_id);

                                $delivery_note_shipment->status = 8;

                                $delivery_note_shipment->save();

                              /*  $delivery_note = $delivery_note_shipment->delivery_note;

                                $delivery_note->delivered_shipments = $delivery_note->delivered_shipments - 1;
                                $delivery_note->received_cod_amount = $delivery_note_amount;

                                $delivery_note->save();*/

                                $shipment->shipper_status_id = 13;
                                $shipment->consignee_status_id = 13;

                                $shipment->save();

                                ShipmentsJourneyController::add($shipment_id, 13, 13, NULL, NULL, NULL,50);
                                    }
                        else{
                            return redirect()->back()->with('error','\'Shipment can\'t be updated');
                        }

                    }

                    $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                        return $row . ': ' . $tracking_number;
                    }, array_keys($tracking_numbers), $tracking_numbers));

                    return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipment(s) marked as reverted with Tracking Number(s):' . PHP_EOL . $tracking_numbers]);
                } else {
                    $errors = array_map(function ($row, $errors) {
                        return $row . ':' . PHP_EOL . implode(' | ', $errors);
                    }, array_keys($errors), $errors);

                    return redirect()->back()->withErrors($errors);
                }
            } else {
                return redirect()->back()->with('error', 'No Shipments in File');
            }
        }
    }

}
