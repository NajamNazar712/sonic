<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\InternationalEconomyStandardRetailRate;
use App\Http\Traits\CommonTrait;
use Illuminate\Support\Facades\Auth;

class InternationalEconomyStandardRatesController extends Controller
{
    use CommonTrait;
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 800);
        $zoneColumnsArray = $this->zoneMarginColumnName()['zoneColumnArray'];
        
        return view('admin.retail.international_economy_rates.index', compact('zoneColumnsArray'));
    }

    public function list(Request $request)
    {
        $zoneColumnsArray = $this->zoneMarginColumnName()['zoneColumnArray'];

        $rates_list = InternationalEconomyStandardRetailRate::select(
            array_merge(['id', 'range_up', 'range_down'], $zoneColumnsArray)
        );

        if ($request->shipping_mode_id == 1) {
            $rates_list = $rates_list->where('shipping_mode_id', 1);
        } else if ($request->shipping_mode_id == 2) {
            $rates_list = $rates_list->where('shipping_mode_id', 2);
        } else {
            $rates_list = $rates_list->where('shipping_mode_id', 3);
        }

        return Datatables::of($rates_list)->make(true);
    }


    public function upload_excel(Request $request)
    {
        $zoneColumnsArray = $this->zoneMarginColumnName()['zoneColumnArray'];
        
        $names = array_merge([
            'range_up' => 'Range Up',
            'range_down' => 'Range Down',
        ], array_combine(
            $zoneColumnsArray,
            array_map(fn($zone) => ucwords(str_replace('_', ' ', $zone)), $zoneColumnsArray)
        ));

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
            //'exists' => 'Given :attribute is Invalid.',
        ];
        $rules = [
            'range_up' => ['required', 'numeric', 'between:0.01,300' /*Rule::exists('international_standard_retail_rates', 'range_up')*/],
            'range_down' => ['required', 'numeric', 'between:0.01,300'/*, Rule::exists('international_standard_retail_rates', 'range_down')*/],
        ];
         
            // Add zone rules dynamically
        foreach ($zoneColumnsArray as $zone) {
            $rules[$zone] = ['required', 'numeric', 'between:0,1000000'];
        }
        $fields = array_merge([
            0 => 'range_up',
            1 => 'range_down'
        ], array_values($zoneColumnsArray));
        
        $fields = array_values(array_keys($names));
        if ($file = $request->file('document_rates')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = array_merge([
                'Range Up',
                'Range Down'
            ], array_map(function ($zone) {
                return ucwords(str_replace('_', ' ', $zone)); 
            }, $zoneColumnsArray));
            
            if (isset($spreadsheet)) {
                $header_correct = true;
                
                foreach ($spreadsheet[0] as $index => $header_value) {
                    
                    if ($index == count($header)) {
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
                $rate_range_ids = array();
                $rate_range_id_row = array();
                foreach ($rows as $key => $row) {

                    $row_id = $key + 2;

                    $validate = Validator::make($row, $rules, $messages);

                    $validate->setAttributeNames($names);

                    if ($validate->fails()) {
                        $errors['Row #' . $row_id] = $validate->errors()->all();
                    }

                    if (empty($errors['Row #' . $row_id])) {

                        /*            if ($row['shipping_mode_id'] == 1) {*/
                        if ((!empty(trim($row['range_up']))) && (!empty(trim($row['range_down'])))) {
                            if (empty($rate_range_ids)) {
                                $rate_range_ids[] = $row['range_up'];
                                $rate_range_id_row[$row['range_up']] = $row_id;
                            } else {
                                if (in_array($row['range_up'], $rate_range_ids)) {
                                    $errors['Row #' . $row_id][] = 'Same Range as of Row #' . $rate_range_id_row[$row['range_up']];
                                } else {
                                    $rate_range_ids[] = $row['range_up'];
                                    $rate_range_id_row[$row['range_up']] = $row_id;
                                }
                            }
                        }

                        /* }
                         else{
                             return redirect()->back()->with(['error' => 'Non-document and box type are not allowed in document type']);
                         }*/
                    }
                }
                if (empty($errors)) {

                    $updated = 0;
                    $created = 0;
                    $not_updated = 0;
                    $shipping_mode_id = 2;
                    InternationalEconomyStandardRetailRate::where('shipping_mode_id', $shipping_mode_id)->delete();
                    foreach ($rows as $key => $row) {
                        $row_id = $key + 2;
                        $range_up = trim($row['range_up']);
                        $range_down = trim($row['range_down']);

                        // Create new record
                        $standard_rate = new InternationalEconomyStandardRetailRate();
                        $standard_rate->range_up = $range_up;
                        $standard_rate->range_down = $range_down;
                        $standard_rate->shipping_mode_id = $shipping_mode_id;

                        // Dynamically assign zone values
                        foreach ($zoneColumnsArray as $zoneColumn) {
                            if (isset($row[$zoneColumn])) {
                                $standard_rate->$zoneColumn = trim($row[$zoneColumn]);
                            }
                        }

                        $standard_rate->save();
                        $created++;
                    }
                    $error_msg = '';
                    if ($not_updated > 1) {
                        $error_msg = 'Total ' . $not_updated . ' rows could not updated!';
                    } else if ($created > 1) {
                        $updated = $created;
                    }

                    return redirect()->back()->with(['success' => 'Total ' . $updated . ' rows updated', 'error' => $error_msg]);
                } else {
                    $errors = array_map(function ($row, $errors) {
                        return $row . ':' . PHP_EOL . implode(' | ', $errors);
                    }, array_keys($errors), $errors);

                    return redirect()->back()->withErrors($errors);
                }

            } else {
                return redirect()->back()->with('error', 'No Rates in File');
            }

        }
    }
}
