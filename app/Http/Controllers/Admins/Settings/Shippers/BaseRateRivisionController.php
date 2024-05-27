<?php

namespace App\Http\Controllers\Admins\Settings\Shippers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\BaseRateType;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BaseRateRivisionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function index()
    {
        $baseRateTypes = BaseRateType::all();

        return view('admin.settings.shippers.base_rate_revision.index',['baseRateTypes' => $baseRateTypes]);
    }

    public function add_bulk_shipper_adjustment_store(Request $request)
    {

        $rateAdjustmentTypeId = $request->adjustment_type;

        $names = [
            'shipper_id' => 'Account Number',
            'percentage' => 'Percentage'
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be a Numeric Value.',
        ];

        $rules = [
            'shipper_id' => ['required', 'integer', 'exists:users,id'],
            'percentage' => ['required', 'Numeric','not_in:0','min:-1000','max:1000']
        ];

        $fields = [0 => 'shipper_id', 1 => 'percentage'];

        if ($file = $request->file('shippers')) {

            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Account Number (Shipper Id)', 'Percentage'];
        }

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

        if (!isset($spreadsheet) || !empty($spreadsheet)) {
            $rows = array();

            if (isset($spreadsheet)) {
                foreach ($spreadsheet as $spreadsheet_row) {
                    $row = array();

                    foreach ($spreadsheet_row as $key => $value) {
                        $row[$fields[$key]] = $value;
                    }

                    $rows[] = $row;
                }

                unset($spreadsheet);
            }

            // dd($rows);
            $duplicateValidation = Validator::make($rows,
            ['*.shipper_id' => 'required|unique']
            );

            foreach ($rows as $key => $row) {
                $row_id = $key + 1;

                $validate = Validator::make($row, $rules, $messages);

                $validate->setAttributeNames($names);

                if ($validate->fails()) {
                    $errors['Row #' . $row_id] = $validate->errors()->all();
                }
            }
            if (isset($errors)) {
                $errors = array_map(function ($row, $errors) {
                    return $row . ':' . PHP_EOL . implode(' | ', $errors);
                }, array_keys($errors), $errors);
                return redirect()->back()->withErrors($errors);
            } else {
                foreach ($rows as $key => $row) {

                    $shipper_id = (int)$row['shipper_id'];
                    $rateAdjustmentPercentage = floatval($row['percentage']);
                    $rate_adjustment_type_id = $rateAdjustmentTypeId;
                    // $this->add_adjustment($shipment->id, $payable, $remarks, $adjustment_type_id);

                    }
                }
                return redirect()->back()->with(['success' => count($rows) . ' Adjustment Added']);
            }

         else {
            return redirect()->back()->with('error', 'Invalid Tracking Numbers');
        }
    }
}
