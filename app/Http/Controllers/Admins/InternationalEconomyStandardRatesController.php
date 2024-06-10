<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\InternationalEconomyStandardRetailRate;
use Illuminate\Support\Facades\Auth;

class InternationalEconomyStandardRatesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function index(){
        return view('admin.retail.international_economy_rates.index');
    }

    public function list(Request $request)
    {

        $rates_list = InternationalEconomyStandardRetailRate::select('id', 'range_up', 'range_down', 'zone_1', 'zone_2', 'zone_3', 'zone_4', 'zone_5', 'zone_6', 'zone_7', 'zone_8', 'zone_9', 'zone_10', 'zone_11');
        if ($request->shipping_mode_id == 1){
            $rates_list = $rates_list->where('shipping_mode_id',1);
        }
        else if($request->shipping_mode_id == 2){
            $rates_list = $rates_list->where('shipping_mode_id',2);
        }
        else{
            $rates_list = $rates_list->where('shipping_mode_id',3);
        }

        return Datatables::of($rates_list)->make(true);
    }


    // public function upload_excel(Request $request)
    // {
    //     $names = [
    //         'range_up' => 'Range Up',
    //         'range_down' => 'Range Down',
    //         /*   'shipping_mode_id' => 'Shipping Mode',*/
    //         'zone_1' => 'Zone 1',
    //         'zone_2' => 'Zone 2',
    //         'zone_3' => 'Zone 3',
    //         'zone_4' => 'Zone 4',
    //         'zone_5' => 'Zone 5',
    //         'zone_6' => 'Zone 6',
    //         'zone_7' => 'Zone 7',
    //         'zone_8' => 'Zone 8',
    //         'zone_9' => 'Zone 9',
    //         'zone_10' => 'Zone 10',
    //         'zone_11' => 'Zone 11',
    //     ];

    //     $messages = [
    //         'required' => ':attribute is Required.',
    //         'integer' => ':attribute must be an Integer.',
    //         //'exists' => 'Given :attribute is Invalid.',
    //     ];
    //     $rules = [
    //         'range_up' => ['required', 'numeric', 'between:0.01,300' /*Rule::exists('international_standard_retail_rates', 'range_up')*/],
    //         'range_down' => ['required', 'numeric', 'between:0.01,300'/*, Rule::exists('international_standard_retail_rates', 'range_down')*/],
    //         /* 'shipping_mode_id' => ['required', 'numeric', 'between:1,3'],*/
    //         'zone_1' => ['required', 'numeric', 'between:0,1000000'],
    //         'zone_2' => ['required', 'numeric', 'between:0,1000000'],
    //         'zone_3' => ['required', 'numeric', 'between:0,1000000'],
    //         'zone_4' => ['required', 'numeric', 'between:0,1000000'],
    //         'zone_5' => ['required', 'numeric', 'between:0,1000000'],
    //         'zone_6' => ['required', 'numeric', 'between:0,1000000'],
    //         'zone_7' => ['required', 'numeric', 'between:0,1000000'],
    //         'zone_8' => ['required', 'numeric', 'between:0,1000000'],
    //         'zone_9' => ['required', 'numeric', 'between:0,1000000'],
    //         'zone_10' => ['required', 'numeric', 'between:0,1000000'],
    //         'zone_11' => ['required', 'numeric', 'between:0,1000000'],
    //     ];

    //     $fields = [0 => 'range_up', 1 => 'range_down', 2 => 'zone_1', 3 => 'zone_2', 4 => 'zone_3', 5 => 'zone_4', 6 => 'zone_5', 7 => 'zone_6', 8 => 'zone_7', 9 => 'zone_8', 10 => 'zone_9', 11 => 'zone_10', 12 => 'zone_11'];

    //     if ($file = $request->file('document_rates')) {
    //         $spreadsheet = IOFactory::createReaderForFile($file);
    //         $spreadsheet->setReadDataOnly(true);
    //         $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

    //         $header = ['Range Up', 'Range Down', 'Zone 1', 'Zone 2', 'Zone 3', 'Zone 4', 'Zone 5', 'Zone 6', 'Zone 7', 'Zone 8', 'Zone 9', 'Zone 10', 'Zone 11'];

    //         if (isset($spreadsheet)) {
    //             $header_correct = true;

    //             foreach ($spreadsheet[0] as $index => $header_value) {
    //                 if ($index == 13) {
    //                 } elseif (!isset($header[$index]) || $header_value != $header[$index]) {
    //                     $header_correct = false;
    //                     break;
    //                 }
    //             }

    //             if (!$header_correct) {
    //                 return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
    //             } else {
    //                 unset($spreadsheet[0]);
    //             }
    //         }

    //         if (!empty($spreadsheet) || !isset($spreadsheet)) {
    //             $rows = array();
    //             foreach ($spreadsheet as $spreadsheet_row) {
    //                 $row = array();

    //                 foreach ($spreadsheet_row as $key => $value) {
    //                     $row[$fields[$key]] = $value;
    //                 }

    //                 $rows[] = $row;
    //             }

    //             unset($spreadsheet);
    //             $errors = array();
    //             $rate_range_ids = array();
    //             $rate_range_id_row = array();
    //             foreach ($rows as $key => $row) {

    //                 $row_id = $key + 2;

    //                 $validate = Validator::make($row, $rules, $messages);

    //                 $validate->setAttributeNames($names);

    //                 if ($validate->fails()) {
    //                     $errors['Row #' . $row_id] = $validate->errors()->all();
    //                 }

    //                 if (empty($errors['Row #' . $row_id])) {

    //                     /*            if ($row['shipping_mode_id'] == 1) {*/
    //                     if ((!empty(trim($row['range_up']))) && (!empty(trim($row['range_down'])))) {
    //                         if (empty($rate_range_ids)) {
    //                             $rate_range_ids[] = $row['range_up'];
    //                             $rate_range_id_row[$row['range_up']] = $row_id;
    //                         } else {
    //                             if (in_array($row['range_up'], $rate_range_ids)) {
    //                                 $errors['Row #' . $row_id][] = 'Same Range as of Row #' . $rate_range_id_row[$row['range_up']];
    //                             } else {
    //                                 $rate_range_ids[] = $row['range_up'];
    //                                 $rate_range_id_row[$row['range_up']] = $row_id;
    //                             }
    //                         }
    //                     }

    //                     /* }
    //                      else{
    //                          return redirect()->back()->with(['error' => 'Non-document and box type are not allowed in document type']);
    //                      }*/
    //                 }
    //             }
    //             if (empty($errors)) {

    //                 $updated = 0;
    //                 $created = 0;
    //                 $not_updated = 0;
    //                 $shipping_mode_id = 1;
    //                 InternationalStandardRetailRates::where('shipping_mode_id', $shipping_mode_id)->delete();
    //                 foreach ($rows as $key => $row) {
    //                     $row_id = $key + 2;
    //                     $range_up = trim($row['range_up']);
    //                     $range_down = trim($row['range_down']);
    //                     $zone_1 = trim($row['zone_1']);
    //                     $zone_2 = trim($row['zone_2']);
    //                     $zone_3 = trim($row['zone_3']);
    //                     $zone_4 = trim($row['zone_4']);
    //                     $zone_5 = trim($row['zone_5']);
    //                     $zone_6 = trim($row['zone_6']);
    //                     $zone_7 = trim($row['zone_7']);
    //                     $zone_8 = trim($row['zone_8']);
    //                     $zone_9 = trim($row['zone_9']);
    //                     $zone_10 = trim($row['zone_10']);
    //                     $zone_11 = trim($row['zone_11']);

    //                     $standard_rate = new InternationalStandardRetailRates();
    //                     $standard_rate->range_up = $range_up;
    //                     $standard_rate->range_down = $range_down;
    //                     $standard_rate->shipping_mode_id = $shipping_mode_id;
    //                     $standard_rate->zone_1 = $zone_1;
    //                     $standard_rate->zone_2 = $zone_2;
    //                     $standard_rate->zone_3 = $zone_3;
    //                     $standard_rate->zone_4 = $zone_4;
    //                     $standard_rate->zone_5 = $zone_5;
    //                     $standard_rate->zone_6 = $zone_6;
    //                     $standard_rate->zone_7 = $zone_7;
    //                     $standard_rate->zone_8 = $zone_8;
    //                     $standard_rate->zone_9 = $zone_9;
    //                     $standard_rate->zone_10 = $zone_10;
    //                     $standard_rate->zone_11 = $zone_11;
    //                     $standard_rate->save();
    //                     $created++;


    //                 }
    //                 $error_msg = '';
    //                 if ($not_updated > 1) {
    //                     $error_msg = 'Total ' . $not_updated . ' rows could not updated!';
    //                 } else if ($created > 1) {
    //                     $updated = $created;
    //                 }

    //                 return redirect()->back()->with(['success' => 'Total ' . $updated . ' rows updated', 'error' => $error_msg]);
    //             } else {
    //                 $errors = array_map(function ($row, $errors) {
    //                     return $row . ':' . PHP_EOL . implode(' | ', $errors);
    //                 }, array_keys($errors), $errors);

    //                 return redirect()->back()->withErrors($errors);
    //             }

    //         } else {
    //             return redirect()->back()->with('error', 'No Rates in File');
    //         }

    //     }

    //     if ($file = $request->file('non_document_rates')) {
    //         $spreadsheet = IOFactory::createReaderForFile($file);
    //         $spreadsheet->setReadDataOnly(true);
    //         $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

    //         $header = ['Range Up', 'Range Down', 'Zone 1', 'Zone 2', 'Zone 3', 'Zone 4', 'Zone 5', 'Zone 6', 'Zone 7', 'Zone 8', 'Zone 9', 'Zone 10', 'Zone 11'];

    //         if (isset($spreadsheet)) {
    //             $header_correct = true;

    //             foreach ($spreadsheet[0] as $index => $header_value) {
    //                 if ($index == 13) {
    //                 } elseif (!isset($header[$index]) || $header_value != $header[$index]) {
    //                     $header_correct = false;
    //                     break;
    //                 }
    //             }

    //             if (!$header_correct) {
    //                 return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
    //             } else {
    //                 unset($spreadsheet[0]);
    //             }
    //         }

    //         if (!empty($spreadsheet) || !isset($spreadsheet)) {
    //             $rows = array();
    //             foreach ($spreadsheet as $spreadsheet_row) {
    //                 $row = array();

    //                 foreach ($spreadsheet_row as $key => $value) {
    //                     $row[$fields[$key]] = $value;
    //                 }

    //                 $rows[] = $row;
    //             }

    //             unset($spreadsheet);
    //             $errors = array();
    //             $rate_range_ids = array();
    //             $rate_range_id_row = array();
    //             foreach ($rows as $key => $row) {

    //                 $row_id = $key + 2;

    //                 $validate = Validator::make($row, $rules, $messages);

    //                 $validate->setAttributeNames($names);

    //                 if ($validate->fails()) {
    //                     $errors['Row #' . $row_id] = $validate->errors()->all();
    //                 }

    //                 if (empty($errors['Row #' . $row_id])) {

    //                     /*            if ($row['shipping_mode_id'] == 1) {*/
    //                     if ((!empty(trim($row['range_up']))) && (!empty(trim($row['range_down'])))) {
    //                         if (empty($rate_range_ids)) {
    //                             $rate_range_ids[] = $row['range_up'];
    //                             $rate_range_id_row[$row['range_up']] = $row_id;
    //                         } else {
    //                             if (in_array($row['range_up'], $rate_range_ids)) {
    //                                 $errors['Row #' . $row_id][] = 'Same Range as of Row #' . $rate_range_id_row[$row['range_up']];
    //                             } else {
    //                                 $rate_range_ids[] = $row['range_up'];
    //                                 $rate_range_id_row[$row['range_up']] = $row_id;
    //                             }
    //                         }
    //                     }

    //                     /* }
    //                      else{
    //                          return redirect()->back()->with(['error' => 'Non-document and box type are not allowed in document type']);
    //                      }*/
    //                 }
    //             }
    //             if (empty($errors)) {

    //                 $updated = 0;
    //                 $created = 0;
    //                 $not_updated = 0;
    //                 $shipping_mode_id = 2;
    //                 InternationalStandardRetailRates::where('shipping_mode_id', $shipping_mode_id)->delete();
    //                 foreach ($rows as $key => $row) {
    //                     $row_id = $key + 2;
    //                     $range_up = trim($row['range_up']);
    //                     $range_down = trim($row['range_down']);
    //                     $zone_1 = trim($row['zone_1']);
    //                     $zone_2 = trim($row['zone_2']);
    //                     $zone_3 = trim($row['zone_3']);
    //                     $zone_4 = trim($row['zone_4']);
    //                     $zone_5 = trim($row['zone_5']);
    //                     $zone_6 = trim($row['zone_6']);
    //                     $zone_7 = trim($row['zone_7']);
    //                     $zone_8 = trim($row['zone_8']);
    //                     $zone_9 = trim($row['zone_9']);
    //                     $zone_10 = trim($row['zone_10']);
    //                     $zone_11 = trim($row['zone_11']);

    //                     $standard_rate = new InternationalStandardRetailRates();
    //                     $standard_rate->range_up = $range_up;
    //                     $standard_rate->range_down = $range_down;
    //                     $standard_rate->shipping_mode_id = $shipping_mode_id;
    //                     $standard_rate->zone_1 = $zone_1;
    //                     $standard_rate->zone_2 = $zone_2;
    //                     $standard_rate->zone_3 = $zone_3;
    //                     $standard_rate->zone_4 = $zone_4;
    //                     $standard_rate->zone_5 = $zone_5;
    //                     $standard_rate->zone_6 = $zone_6;
    //                     $standard_rate->zone_7 = $zone_7;
    //                     $standard_rate->zone_8 = $zone_8;
    //                     $standard_rate->zone_9 = $zone_9;
    //                     $standard_rate->zone_10 = $zone_10;
    //                     $standard_rate->zone_11 = $zone_11;
    //                     $standard_rate->save();
    //                     $created++;

    //                 }
    //                 $error_msg = '';
    //                 if ($not_updated > 1) {
    //                     $error_msg = 'Total ' . $not_updated . ' rows could not updated!';
    //                 } else if ($created > 1) {
    //                     $updated = $created;
    //                 }

    //                 return redirect()->back()->with(['success' => 'Total ' . $updated . ' rows updated', 'error' => $error_msg]);
    //             } else {
    //                 $errors = array_map(function ($row, $errors) {
    //                     return $row . ':' . PHP_EOL . implode(' | ', $errors);
    //                 }, array_keys($errors), $errors);

    //                 return redirect()->back()->withErrors($errors);
    //             }

    //         } else {
    //             return redirect()->back()->with('error', 'No Rates in File');
    //         }

    //     }

    //     if ($file = $request->file('box_rates')) {
    //         $spreadsheet = IOFactory::createReaderForFile($file);
    //         $spreadsheet->setReadDataOnly(true);
    //         $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

    //         $header = ['Range Up', 'Range Down', 'Zone 1', 'Zone 2', 'Zone 3', 'Zone 4', 'Zone 5', 'Zone 6', 'Zone 7', 'Zone 8', 'Zone 9', 'Zone 10', 'Zone 11'];

    //         if (isset($spreadsheet)) {
    //             $header_correct = true;

    //             foreach ($spreadsheet[0] as $index => $header_value) {
    //                 if ($index == 13) {
    //                 } elseif (!isset($header[$index]) || $header_value != $header[$index]) {
    //                     $header_correct = false;
    //                     break;
    //                 }
    //             }

    //             if (!$header_correct) {
    //                 return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
    //             } else {
    //                 unset($spreadsheet[0]);
    //             }
    //         }

    //         if (!empty($spreadsheet) || !isset($spreadsheet)) {
    //             $rows = array();
    //             foreach ($spreadsheet as $spreadsheet_row) {
    //                 $row = array();

    //                 foreach ($spreadsheet_row as $key => $value) {
    //                     $row[$fields[$key]] = $value;
    //                 }

    //                 $rows[] = $row;
    //             }

    //             unset($spreadsheet);
    //             $errors = array();
    //             $rate_range_ids = array();
    //             $rate_range_id_row = array();
    //             foreach ($rows as $key => $row) {

    //                 $row_id = $key + 2;

    //                 $validate = Validator::make($row, $rules, $messages);

    //                 $validate->setAttributeNames($names);

    //                 if ($validate->fails()) {
    //                     $errors['Row #' . $row_id] = $validate->errors()->all();
    //                 }

    //                 if (empty($errors['Row #' . $row_id])) {

    //                     /*            if ($row['shipping_mode_id'] == 1) {*/
    //                     if ((!empty(trim($row['range_up']))) && (!empty(trim($row['range_down'])))) {
    //                         if (empty($rate_range_ids)) {
    //                             $rate_range_ids[] = $row['range_up'];
    //                             $rate_range_id_row[$row['range_up']] = $row_id;
    //                         } else {
    //                             if (in_array($row['range_up'], $rate_range_ids)) {
    //                                 $errors['Row #' . $row_id][] = 'Same Range as of Row #' . $rate_range_id_row[$row['range_up']];
    //                             } else {
    //                                 $rate_range_ids[] = $row['range_up'];
    //                                 $rate_range_id_row[$row['range_up']] = $row_id;
    //                             }
    //                         }
    //                     }

    //                     /* }
    //                      else{
    //                          return redirect()->back()->with(['error' => 'Non-document and box type are not allowed in document type']);
    //                      }*/
    //                 }
    //             }
    //             if (empty($errors)) {

    //                 $updated = 0;
    //                 $created = 0;
    //                 $not_updated = 0;

    //                 $shipping_mode_id = 3;
    //                 InternationalStandardRetailRates::where('shipping_mode_id', $shipping_mode_id)->delete();

    //                 foreach ($rows as $key => $row) {
    //                     $row_id = $key + 2;
    //                     $range_up = trim($row['range_up']);
    //                     $range_down = trim($row['range_down']);
    //                     $zone_1 = trim($row['zone_1']);
    //                     $zone_2 = trim($row['zone_2']);
    //                     $zone_3 = trim($row['zone_3']);
    //                     $zone_4 = trim($row['zone_4']);
    //                     $zone_5 = trim($row['zone_5']);
    //                     $zone_6 = trim($row['zone_6']);
    //                     $zone_7 = trim($row['zone_7']);
    //                     $zone_8 = trim($row['zone_8']);
    //                     $zone_9 = trim($row['zone_9']);
    //                     $zone_10 = trim($row['zone_10']);
    //                     $zone_11 = trim($row['zone_11']);

    //                     $standard_rate = new InternationalStandardRetailRates();
    //                     $standard_rate->range_up = $range_up;
    //                     $standard_rate->range_down = $range_down;
    //                     $standard_rate->shipping_mode_id = $shipping_mode_id;
    //                     $standard_rate->zone_1 = $zone_1;
    //                     $standard_rate->zone_2 = $zone_2;
    //                     $standard_rate->zone_3 = $zone_3;
    //                     $standard_rate->zone_4 = $zone_4;
    //                     $standard_rate->zone_5 = $zone_5;
    //                     $standard_rate->zone_6 = $zone_6;
    //                     $standard_rate->zone_7 = $zone_7;
    //                     $standard_rate->zone_8 = $zone_8;
    //                     $standard_rate->zone_9 = $zone_9;
    //                     $standard_rate->zone_10 = $zone_10;
    //                     $standard_rate->zone_11 = $zone_11;
    //                     $standard_rate->save();
    //                     $created++;

    //                 }
    //                 $error_msg = '';
    //                 if ($not_updated > 1) {
    //                     $error_msg = 'Total ' . $not_updated . ' rows could not updated!';
    //                 } else if ($created > 1) {
    //                     $updated = $created;
    //                 }

    //                 return redirect()->back()->with(['success' => 'Total ' . $updated . ' rows updated', 'error' => $error_msg]);
    //             } else {
    //                 $errors = array_map(function ($row, $errors) {
    //                     return $row . ':' . PHP_EOL . implode(' | ', $errors);
    //                 }, array_keys($errors), $errors);

    //                 return redirect()->back()->withErrors($errors);
    //             }

    //         } else {
    //             return redirect()->back()->with('error', 'No Rates in File');
    //         }

    //     }
    // }
}
