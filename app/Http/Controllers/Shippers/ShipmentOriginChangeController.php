<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\Admins\V2Pickup\V2AdminPickupsController;
use App\Http\Models\City;
use App\Http\Models\ReceivingSheetShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\UserShippingInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ShipmentOriginChangeController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    static public function phone_number($phone_number) {
        //Removing anything after Comma (,)
        $phone_number = preg_replace('/^([^,]*).*$/', '$1', $phone_number);

        //Removing anything after Slash (/)
        $phone_number = preg_replace('/^([^\/]*).*$/', '$1', $phone_number);

        //Removing all Dashes (-)
        $phone_number = str_replace('-', '', $phone_number);

        //Removing all Spaces ( )
        $phone_number = str_replace(' ', '', $phone_number);

        //Replace +92 with 0
        if (substr($phone_number, 0, 3) == '+92') {
            $phone_number =  '0' . substr($phone_number, 3);
        }
        //Replace 92 with 0
        else if (substr($phone_number, 0, 2) == '92') {
            $phone_number =  '0' . substr($phone_number, 2);
        }
        //Replace 0092 with 0
        else if (substr($phone_number, 0, 4) == '0092') {
            $phone_number =  '0' . substr($phone_number, 4);
        }
        //Addition of 0
        else if (substr($phone_number, 0, 1) != '0') {
            $phone_number =  '0' . $phone_number;
        }

        return $phone_number;
    }

    public function shipments_origin_index(){
        return view('client.shipment.origin_change.index');
    }

    public function shipments_origin_store(Request $request){
        $user_id = session('user_id');


        Validator::extend('phone_number', function($attribute, $value, $parameters) {
            if ($value) {
                $value = $this->phone_number($value);

                if (preg_match('/^((\+92)|(92)|(0092))-{0,1}\d{3}-{0,1}\d{7}$|^\d{3}-{1}\d{7}$|^\d{11}$|^\d{4}-\d{7}$|^\d{3}-\d{7}$|^\d{10}$/', $value)) {
                    return TRUE;
                }
                else {
                    return FALSE;
                }
            }
        });

        Validator::extend('receiving_sheet_check', function ($attribute, $value, $parameters, $validator) use ($user_id) {
            $data = $validator->getData();
            if(isset($data['tracking_number'])){
                $tracking_number = $data['tracking_number'];
            }
            else{
                return false;
            }

            if ($value) {
                $shipment = Shipment::where('tracking_number', $tracking_number)->where('shipper_status_id', 1);
                if($shipment->exists()){
                    $shipment = $shipment->select('id')->first();
                    if (!ReceivingSheetShipment::where('shipment_id', $shipment->id)->exists()) {
                        return true;
                    } else {
                        return false;
                    }
                }
                else {
                    return false;
                }

            }
        });

        $names = [
            'tracking_number' => 'Tracking Number',
            'pickup_address' => 'Pickup Address',
            'contact_person' => 'Person Of Contact',
            'vendor' => 'Vendor',
            'phone_number' => 'Phone Number',
            'city' => 'City',
            'email_address' => 'Email Address'
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'required_if' => ':attribute is Required when :other is :value.',
            'filled' => ':attribute is Optional but cannot be Empty if Present.',
            'integer' => ':attribute must be an Integer.',
            'numeric' => ':attribute must be a Number.',
            'boolean' => ':attribute must be 0 or 1.',
            'digits_between' => ':attribute must be between :min and :max Digits.',
            'email' => ':attribute must be a Valid Email Address.',
            'exists' => 'Given :attribute is of Invalid ID.',
            'unique' => ':attribute is already Present.',
            'date_format' => ':attribute must be of valid Format, required Format is: YYYY-MM-DD.',
            'in' => ':attribute must be No or Yes.',
            'phone_number.regex' => ':attribute format is Invalid, required Format is: 03000000000.',
            'receiving_sheet_check' => 'Shipment is already in receiving sheet!',
        ];

        $rules = [
            'tracking_number' => ['required', 'integer', 'distinct', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('shipper_status_id', 1);
            }), 'receiving_sheet_check'],
            'city' => ['required', 'string', 'between:1,100', Rule::exists('cities', 'name')->where('status', 1)->where('pickup', 1)],
            'contact_person' => ['required', 'between:1,100'],
            'pickup_address' => ['required', 'between:1,255'],
            'vendor' => ['required', 'between:1,100'],
            'phone_number' => ['required', 'phone_number'],
            'email_address' => ['required', 'email', 'between:0,100'],
        ];

        $fields = [0 => 'tracking_number', 1 => 'pickup_address', 2 => 'contact_person', 3 => 'vendor', 4 => 'phone_number', 5 => 'city', 6 => 'email_address'];

        if($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number', 'Pickup Address', 'Person Of Contact', 'Vendor', 'Phone Number', 'City', 'Email Address'];
        }
        if (isset($spreadsheet)) {
            $header_correct = TRUE;

            foreach ($spreadsheet[0] as $index => $header_value) {
                if($index == 2){
                }
                elseif (!isset($header[$index]) || $header_value != $header[$index]) {
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
            $tracking_numbers = array();

            foreach ($rows as $key => $row) {
                $row_id = $key + 2;

                $validate = Validator::make($row, $rules, $messages);

                $validate->setAttributeNames($names);

                if ($validate->fails()) {
                    $errors['Row #' . $row_id] = $validate->errors()->all();
                }

            }
            if(!empty($errors)){
                $errors = array_map(function ($row, $errors) {
                    return $row . ':' . PHP_EOL . implode(' | ', $errors);
                }, array_keys($errors), $errors);
                return redirect()->back()->withErrors($errors);
            }
            else{

                foreach($rows as $key => $row){
                    $shipment = Shipment::where('tracking_number', $row['tracking_number'])->where('shipper_status_id', 1)->first();


                    V2AdminPickupsController::cancel($shipment->id);

                    $pickup_address_id = $shipment->pickup_address_id;
                    $city = City::where('name', $row['city'])->first();

                    if(!UserShippingInfo::where('user_id', $user_id)->where('vendor', $row['vendor'])->where('city_id', $city->id)->exists()){
                        $pickup_address_id = ShipperShipmentBookController::add_pickup_address($user_id, $row['pickup_address'], $row['contact_person'], $row['vendor'], $row['phone_number'], $row['email_address'], $city->id,0);
                    }

                    $shipment->pickup_address_id = $pickup_address_id;
                    $shipment->save();
                    AdminPickupsController::generate($shipment->id);

                    $tracking_numbers[] = $shipment->tracking_number;
                }


                $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                    return $tracking_number;
                }, array_keys($tracking_numbers), $tracking_numbers));


                return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipment(s) Updated with Tracking Number(s):' . PHP_EOL . $tracking_numbers]);
            }

        }
        else {
            return redirect()->back()->with('error', 'No Shipments in File');
        }

    }
}
