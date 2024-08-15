<?php

namespace App\Http\Controllers\Admins\Retail;

use App\RetailDiscountCode;
use App\Http\Controllers\Admins\ActivityTrailController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\Datatables\Datatables;

class RetailDiscountCodesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 801);

        $discounts = RetailDiscountCode::all();

        return view('admin.settings.retail.retail_discount_codes.index', ['discounts' => $discounts]);
    }

    public function list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 802);
        }

        $baseRateRevisions = RetailDiscountCode::orderBy('id','desc')->get();

        $datatable = Datatables::of($baseRateRevisions);

        return $datatable->make(true);
    }

    public function add_bulk_retail_discount_codes_store(Request $request)
    {
        $names = [
            'code' => 'Code',
            'discount_percentage' => 'Discount Percentage'
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'alpha_num' => ':attribute must be a Alpha-Numeric Value.',
            'unique' => 'Code :input already Exists.',
        ];

        $rules = [
            'code' => ['required', 'alpha_num', 'unique:retail_discount_codes,code'],
            'discount_percentage' => ['required', 'Numeric', 'min:0', 'max:100']
        ];

        $fields = [0 => 'code', 1 => 'discount_percentage'];

        if ($file = $request->file('discount_codes')) {

            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Code', 'Discount Percentage'];
        }

        if (isset($spreadsheet)) {
            $header_correct = true;

            foreach ($spreadsheet[0] as $index => $header_value) {
                if ($index == 1) {
                    continue;
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
                ['*.code' => 'required|distinct'],
                ['*.code.distinct' => 'Duplicate Discount Codes Found!']
            );

            if ($duplicateValidation->fails()) {
                return redirect()->back()->withErrors($duplicateValidation->errors()->first());
            }

            $now = now();

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
                foreach ($rows as $key => $row) {

                    $discountCode = $row['code'];
                    $discount_percent = floatval($row['discount_percentage']);
                    $data[] = [
                        'code' => $discountCode,
                        'discount_percentage' => $discount_percent,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }

                RetailDiscountCode::insert($data);
            }

            return redirect()->back()->with(['success' => count($rows) . ' Discount Code' . (count($rows) > 1 ? 's' : '') . ' Added']);
        } else {
            return redirect()->back()->with('error', 'Invalid Account Numbers');
        }
    }
}
