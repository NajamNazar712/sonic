<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Admins\V2Pickup\V2AdminPickupsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\GlobalSettings;
use App\http\Models\Admin\NsaAccountShipment;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteShipment;
use App\Http\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
                        ShipmentsJourneyController::add($shipment, 12,12, 34, NULL, NULL, 50, $note->id, NULL, 0);
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

    public function delivery_index(){
        return view('admin.telenor.delivery');
    }

    public function delivery_submit(Request $request){
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

        if($file = $request->file('shipments')) {
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
                }
                else {
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

                        $settings = GlobalSettings::where('type', 'nsa_accounts');
                        $nsa_accounts = array();
                        if($settings->exists()){
                            $settings = $settings->first();
                            $nsa_accounts = array_map('intval', explode(',', $settings->text));
                        }
                        if(count($nsa_accounts) > 0){
                            if (!Shipment::where('tracking_number', $row['tracking_number'])->whereIn('user_id', $nsa_accounts)->where('shipper_status_id', 1)->exists()) {
                                $errors['Row #' . $row_id][] = 'Shipment not found with Tracking Number #' . $row['tracking_number'];
                            }
                        }
                        else{
                            $errors['Row #' . $row_id][] = 'Nsa Account Not Found' . $row['tracking_number'];
                        }
                    }
                }
                if(empty($errors)){
                    $tracking_numbers = array();
                    $shipment_ids = array();
                    foreach ($rows as $key => $row) {
                        $row_id = $key + 2;
                        $tracking = trim($row['tracking_number']);

                        $shipment_details = Shipment::where('tracking_number',$tracking)->first();
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
                                    V2AdminPickupsController::cancel($nsa_shipment->id);

                                    $status_id = 2;

                                    ShipmentsJourneyController::add($nsa_shipment->id, $status_id, $status_id, NULL, NULL, NULL, 57);

                                    if ($nsa_shipment->pickup_address->city_id != $nsa_shipment->consignee_city_id) {
                                        $status_id = 4;

                                        ShipmentsJourneyController::add($nsa_shipment->id, $status_id, $status_id, NULL, NULL, NULL, 57);
                                    }

                                    $nsa_shipment->shipper_status_id = $status_id;
                                    $nsa_shipment->consignee_status_id = $status_id;
                                    $nsa_shipment->actual_weight = 0.5;

                                    $nsa_shipment->save();

                                    ShipmentChargesController::weight($nsa_shipment->id);
                                    ShipmentChargesController::cash_handling($nsa_shipment->id);
                                    ShipmentChargesController::insurance($nsa_shipment->id);
                                    ShipmentChargesController::fuel_surcharge($nsa_shipment->id);
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
                                    $shipment_data->shipper_status_id = 14;
                                    $shipment_data->consignee_status_id = 14;
                                    $shipment_data->save();

                                    ShipmentsJourneyController::add($shipment, 5, 5, NULL, NULL, NULL, 50, $note->id, $rider_id);
                                    ShipmentsJourneyController::add($shipment, 14,14, NULL, NULL, NULL, 50, $note->id, NULL, 0);
                                    ShipmentsJourneyController::add($shipment, 14,14, NULL, NULL, NULL, 50, $note->id, NULL, 1);

                                    DeliveryNoteShipment::where(['delivery_note_id' => $note->id, 'shipment_id' => $shipment_data->id])->update(['status' => 1]);
                                }

                                DeliveryNote::where('id', $note->id)->update(['delivered_shipments' => 0, 'verified_by' => 50, 'received_cod_amount' => 0, 'status' => 1, 'last_updated_at' => Carbon::now(), 'status_verified_at' => Carbon::now()]);
                            }
                        }
                    }
                    $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                        return $row . ': ' . $tracking_number;
                    }, array_keys($tracking_numbers), $tracking_numbers));

                    return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipment(s) marked as delivered with Tracking Number(s):' . PHP_EOL . $tracking_numbers]);
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

    public function return_index(){
        return view('admin.telenor.return');
    }

    public function return_shipment_info(Request $request){
        $tracking_number = $request->tracking_number;
        $settings = GlobalSettings::where('type', 'nsa_accounts');
        $nsa_accounts = array();
        if($settings->exists()){
            $settings = $settings->first();
            $nsa_accounts = array_map('intval', explode(',', $settings->text));
        }
        $shipment = Shipment::where('tracking_number', $tracking_number)->where('shipper_status_id', 1)->whereIn('user_id', $nsa_accounts);
        if($shipment->exists()){
            $shipment = $shipment->select('shipments.id as id', 'shipments.tracking_number as tracking_number')->first();
            $data['id'] = $shipment->id;
            $data['tracking_number'] = $shipment->tracking_number;
            return response()->json(['status' => 1, 'success' => 'Shipment Added Successfully', 'details' => $data]);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Shipment already updated']);
        }
    }

    public function return_submit(Request $request){
        $shipment_ids = explode(',', $request->shipment_ids);
        $nsa_shipments = Shipment::whereIn('id', $shipment_ids)->where('shipper_status_id', 1);
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
                        V2AdminPickupsController::cancel($nsa_shipment->id);

                        $status_id = 2;

                        ShipmentsJourneyController::add($nsa_shipment->id, $status_id, $status_id, NULL, NULL, NULL, 57);

                        if ($nsa_shipment->pickup_address->city_id != $nsa_shipment->consignee_city_id) {
                            $status_id = 4;

                            ShipmentsJourneyController::add($nsa_shipment->id, $status_id, $status_id, NULL, NULL, NULL, 57);
                        }

                        $nsa_shipment->shipper_status_id = $status_id;
                        $nsa_shipment->consignee_status_id = $status_id;
                        $nsa_shipment->actual_weight = 0.5;

                        $nsa_shipment->save();

                        ShipmentChargesController::weight($nsa_shipment->id);
                        ShipmentChargesController::cash_handling($nsa_shipment->id);
                        ShipmentChargesController::insurance($nsa_shipment->id);
                        ShipmentChargesController::fuel_surcharge($nsa_shipment->id);

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
                        ShipmentsJourneyController::add($shipment, 12,12, 34, NULL, NULL, 50, $note->id, NULL, 0);
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
}
