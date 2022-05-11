<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Models\Admin\TelenorOtherCouriers;
use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TelenorOtherCourierController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function index(){
        return view('client.telenor.other_couriers');
    }

    public function store(Request $request)
    {

        $names = [
            'tracking_number' => 'Tracking Number',
            'consignee_number' => 'Consignee Number',
            'status' => 'Status',
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
            'string' => ':attribute must be String.',
        ];
        $rules = [
            'tracking_number' => ['required', 'integer'],
            'consignee_number' => ['required', 'integer'],
            'status' => ['required', 'string'],
        ];
        $fields = [0 => 'tracking_number', 1 => 'consignee_number',2 => 'status'];

        if ($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number', 'Consignee Number','Status'];

            if(isset($spreadsheet)) {
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
                        if (!empty(trim($row['tracking_number'])) || !empty(trim($row['consignee_number'] )) || !empty(trim($row['status'] ))) {
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

                        if (Shipment::where('tracking_number', $row['tracking_number'])->exists()) {
                            $errors['Row #' . $row_id][] = 'Shipment can\'t be added with Tracking Number #' . $row['tracking_number'];
                        }

                    }
                }
                if (empty($errors)) {
                    $tracking_numbers = array();
                    $shipment_ids = array();

                    foreach ($rows as $key => $row) {
                        $row_id = $key + 2;
                        $tracking = trim($row['tracking_number']);
                        $consignee_number = trim($row['consignee_number']);
                        $status = $row['status'];

                        $courier = TelenorOtherCouriers::where('tracking_number',$row['tracking_number']);
                        if(!$courier->exists()) {
                            $shipment_details = new TelenorOtherCouriers();
                            $shipment_details->tracking_number = $tracking;
                            $shipment_details->consignee_number = $consignee_number;
                            $shipment_details->status = $status;
                            $shipment_details->save();
                        }

                    }

                    $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                        return $row . ': ' . $tracking_number;
                    }, array_keys($tracking_numbers), $tracking_numbers));

                    return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipments Added:']);
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
