<?php

namespace App\Http\Controllers\Admins\Shippers\Accounts;

use Illuminate\Http\Request;
use App\Http\Models\Admin\Admin;
use App\Http\Models\SaleTierTag;
use App\Models\AccountTaggingLog;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Admins\ActivityTrailController;


class KAMBulkTaggingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 798);
        
        return view('admin.accounts.kam_bulk_tagging');
    }


    public function update(Request $request)
    {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 799);

        $de_tag = $request->input('tag') == 'on';

        $names = [
            'trax_id' => 'Trax ID',
            'shipper_id' => 'Account ID'
        ];
        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be a Numeric Value.',
        ];
        $rules = [
            'trax_id' => ['nullable', 'exists:admins,trax_id'],
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

        if (!isset($spreadsheet) || empty($spreadsheet)) {
            return redirect()->back()->with('error', 'Invalid Trax Ids / Account Numbers');
        }

        $rows = array();
        foreach ($spreadsheet as $spreadsheet_row) {
            $row = array();
            foreach ($spreadsheet_row as $key => $value) {
                $row[$fields[$key]] = $value;
            }
            $rows[] = $row;
        }
        unset($spreadsheet);

        //Validation for Duplicate Entries
        $duplicateValidation = Validator::make(
            $rows,
            ['*.shipper_id' => 'required|distinct'],
            [
                '*.shipper_id.distinct' => 'Duplicate Account Numbers Found!',
                '*.shipper_id.required' => 'Account Numbers are Required!'
            ]
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

            $adminIds = Admin::whereIn('trax_id', array_column($rows, 'trax_id'))->pluck('id', 'trax_id')->toArray();
            foreach ($rows as $row) {
                $trax_id = $row['trax_id'];
                $shipper_id = (int)$row['shipper_id'];
                $admin_id = (!$de_tag && isset($adminIds[$trax_id])) ? $adminIds[$trax_id] : null;

                $old_kam_id = SaleTierTag::where('user_id', $shipper_id)
                ->where('tier_id', 3)
                ->latest('id')
                ->value('kam');

                AccountTaggingLog::logTagging(
                    $shipper_id,        
                    Auth::id(),             
                    $old_kam_id,                 
                    $admin_id, 
                    3                
                );

                SaleTierTag::updateOrCreate(
                    ['user_id' => $shipper_id],
                    ['kam' => $admin_id]
                );
            }
        }
        return redirect()->back()->with(['success' => count($rows) . ($de_tag ? ' De-' : ' ') .'Taggings updated successfully']);
    }
}
