<?php

namespace App\Http\Controllers\Admins\Settings\Shippers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\BaseRateRevision;
use App\Models\Admin\BaseRateType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\Datatables\Datatables;

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

    public function add_bulk_shipper_rate_adjustment_store(Request $request)
    {

        $rateAdjustmentTypeId = $request->adjustment_type;
        $adminId = Auth::id();

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

            //Validation for Duplicate Entries
            $duplicateValidation = Validator::make($rows,
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

                $data= [];
                $rate_adjustment_type_id = $rateAdjustmentTypeId;
                $baseRateRevision = BaseRateRevision::create([
                    'rate_type_id' => $rate_adjustment_type_id,
                    'added_by_admin_id' => $adminId,
                    'approval1_status' => 1,
                    'approval2_status' => 1
                ]);

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

                return redirect()->back()->with(['success' => count($rows) . ' Revision'.(count($rows) > 1 ? 's' : '').' Added']);
            }

         else {
            return redirect()->back()->with('error', 'Invalid Tracking Numbers');
        }
    }

    public function base_rate_revisions_list(Request $request)
    {
        // if ($request->get('excel') && $request->get('excel') == true) {
        //     ActivityTrailController::createActivityTrailLog(Auth::id(), 247);
        // }

        $baseRateRevisions = BaseRateRevision::withCount('shippersWithRateChange')
        ->with([
            'rateType:id,name',
            'addedByAdmin:id,name',
            'approved1ByAdmin:id,name',
            'approval1Status:id,name',
            'approved2ByAdmin:id,name',
            'approval2Status:id,name',
        ]);

        $datatable = Datatables::of($baseRateRevisions)
        ->addColumn('rate_type', function ($revision) {
            return $revision->rateType->name;
        })
        ->addColumn('file_view', function ($revision) {
            return $revision->id;
        })
        ->addColumn('added_by_admin', function ($revision) {
            return $revision->addedByAdmin->name;
        })
        ->addColumn('approved1_by_admin', function ($revision) {
            return $revision->approved1ByAdmin ? $revision->approved1ByAdmin->name : '-';
        })
        ->addColumn('approval1_status', function ($revision) {
            return $revision->approval1Status->name;
        })
        ->editColumn('approval1_at', function ($revision) {
            return $revision->approval1_at ?? '-';
        })
        ->addColumn('approved2_by_admin', function ($revision) {
            return $revision->approved2ByAdmin ? $revision->approved2ByAdmin->name : '-';
        })
        ->editColumn('approval2_at', function ($revision) {
            return $revision->approval2_at ?? '-';
        })
        ->addColumn('approval2_status', function ($revision) {
            return $revision->approval2Status->name;
        })
        ->addColumn('shippers_count', function ($revision) {
            return $revision->shippers_with_rate_change_count;
        })
        ->addColumn('action', function ($revision) {
            //Status = 2  => Approve
            //Status = 3  => Reject
            $approve = '<a href="'.route('admin.settings.shippers.base_rate_revisions.approval1_update',[$revision->id,2]).'" class="dropdown-item status"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Approve</div></div></a>';
            $reject = '<a href="'.route('admin.settings.shippers.base_rate_revisions.approval1_update',[$revision->id,3]).'" class="dropdown-item status"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x"></i></div><div class="col-9 offset-1">Reject</div></div></a>';

            $dropdown = '
                <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">';
            
            if (session('role_id') == 1 || in_array(503, session('permissions')))
            {
                $dropdown .= $approve;
                $dropdown .= $reject;
            }
                

    
            $dropdown .= '</div></div>';
            return $dropdown;
        });

        return $datatable->make(true);
    }

    public function approval1_update($baseRateRevisionId, $status)
    {
        $baseRateRevision = BaseRateRevision::find($baseRateRevisionId);
        $baseRateRevision->approval1_status = $status;
        $baseRateRevision->approval1_at = now();
        $baseRateRevision->approval1_by_admin_id = Auth::id();
        $baseRateRevision->save();
        return redirect()->back()->with([($status == 2 ? 'success' : 'error') => 'Base Rate Revision '.($status == 2 ? 'Approved' : 'Rejected')]);
    }

    public function approval2_update($baseRateRevisionId, $status)
    {
        $baseRateRevision = BaseRateRevision::find($baseRateRevisionId);
        $baseRateRevision->approval2_status = $status;
        $baseRateRevision->approval2_at = now();
        $baseRateRevision->approval2_by_admin_id = Auth::id();
        $baseRateRevision->save();
        return redirect()->back()->with([($status == 2 ? 'success' : 'error') => 'Base Rate Revision '.($status == 2 ? 'Approved' : 'Rejected')]);
    }

}
