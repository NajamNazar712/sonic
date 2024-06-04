<?php

namespace App\Http\Controllers\Admins\Shippers\Accounts;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;


class KAMBulkTaggingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function index()
    {
        return view('admin.accounts.kam_bulk_tagging');
    }


    public function update(Request $request)
    {
        dd($request->all());

        $rateAdjustmentTypeId = $request->adjustment_type;
        $adminId = Auth::id();
        $names = [
            'trax_id' => 'Trax ID',
            'shipper_id' => 'Account ID'
        ];
        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be a Numeric Value.',
        ];
        $rules = [
            'trax_id' => ['required', 'integer', 'exists:admins,trax_id'],
            'shipper_id' => ['required', 'integer', 'exists:users,id']
        ];
        $fields = [0 => 'trax_id', 1 => 'shipper_id'];
        if ($file = $request->file('ids')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();
            $header = ['Trax ID', 'Account ID'];
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
            //Validation for Duplicate Entries
            $duplicateValidation = Validator::make(
                $rows,
                ['*.shipper_id' => 'required|distinct'],
                ['*.shipper_id.distinct' => 'Duplicate Account Numbers Found!']
            );
            if ($duplicateValidation->fails()) {
                return redirect()->back()->withErrors($duplicateValidation->errors()->first());
            }
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
                $data = [];
                $rate_adjustment_type_id = $rateAdjustmentTypeId;
                // $baseRateRevision = BaseRateRevision::create([
                //     'rate_type_id' => $rate_adjustment_type_id,
                //     'added_by_admin_id' => $adminId,
                //     'approval1_status' => 1,
                //     'approval2_status' => 1
                // ]);
                foreach ($rows as $key => $row) {
                    $shipper_id = (int)$row['shipper_id'];
                    $rateAdjustmentPercentage = floatval($row['percentage']);
                    $data[] = [
                        'shipper_id' => $shipper_id,
                        'rate_change_percent' => $rateAdjustmentPercentage
                    ];
                }
                $baseRateRevision->shippersWithRateChange()->createMany($data);
            }
            return redirect()->back()->with(['success' => count($rows) . ' Revision' . (count($rows) > 1 ? 's' : '') . ' Added']);
        } else {
            return redirect()->back()->with('error', 'Invalid Trax Ids / Account Numbers');
        }
    }
}
