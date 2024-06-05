<?php

namespace App\Http\Controllers\Admins\Settings\Shippers;

use App\Http\Controllers\Admins\ActivityTrailController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\CashHandlingCharge;
use App\Http\Models\CorporateCashHandlingCharge;
use App\Http\Models\CorporateDefaultCashHandlingCharge;
use App\Http\Models\CorporateDefaultFuelSurcharge;
use App\Http\Models\CorporateDefaultWeightCharge;
use App\Http\Models\CorporateFuelSurcharge;
use App\Http\Models\CorporateWeightCharge;
use App\Http\Models\CorporateWeightChargeZoneWise;
use App\Http\Models\FuelSurcharge;
use App\Http\Models\Shipper\User;
use App\Http\Models\WeightCharge;
use App\Models\Admin\BaseRateRevision;
use App\Models\Admin\BaseRateRevisionApprovalStatus;
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
        ActivityTrailController::createActivityTrailLog(Auth::id(), 796);

        $baseRateTypes = BaseRateType::all();
        $baseRateRevisionApprovalStatuses = BaseRateRevisionApprovalStatus::all();

        return view('admin.settings.shippers.base_rate_revision.index', ['baseRateTypes' => $baseRateTypes, 'baseRateRevisionApprovalStatuses' => $baseRateRevisionApprovalStatuses]);
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
            'percentage' => ['required', 'Numeric', 'not_in:0', 'min:-1000', 'max:1000']
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

            return redirect()->back()->with(['success' => count($rows) . ' Revision' . (count($rows) > 1 ? 's' : '') . ' Added']);
        } else {
            return redirect()->back()->with('error', 'Invalid Account Numbers');
        }
    }

    public function base_rate_revisions_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 797);
        }

        $baseRateRevisions = BaseRateRevision::withCount('shippersWithRateChange')
            ->with([
                'rateType:id,name',
                'addedByAdmin:id,name',
                'approved1ByAdmin:id,name',
                'approval1Status:id,name',
                'approved2ByAdmin:id,name',
                'approval2Status:id,name',
            ])
            ->orderBy('id','desc');
            // dd($baseRateRevisions->get());

        $datatable = Datatables::of($baseRateRevisions)
            ->addColumn('rate_type_id', function ($revision) {
                return $revision->rateType->name;
            })
            ->addColumn('file_view', function ($revision) {
                $btn = '<button type="button" class="btn btn-sm btn-outline-info align-middle fileViewButton" data-revision-id="' . $revision->id . '">
                <i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span>
                </button>';
                return $btn;
            })
            ->editColumn('created_at', function ($revision) {
                return $revision->created_at->format('Y-m-d H:i:s');
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
                return $revision->approval1_at ? $revision->approval1_at->format('Y-m-d H:i:s') : '-';
            })
            ->addColumn('approved2_by_admin', function ($revision) {
                return $revision->approved2ByAdmin ? $revision->approved2ByAdmin->name : '-';
            })
            ->editColumn('approval2_at', function ($revision) {
                return $revision->approval2_at ? $revision->approval2_at->format('Y-m-d H:i:s') : '-';
            })
            ->addColumn('approval2_status', function ($revision) {
                return $revision->approval2Status->name;
            })
            ->addColumn('action', function ($revision) {
                // Check if dropdown should be shown based on approval statuses and user role
                $showDropdown = false;
                $actions = '';

                // Define approval links
                $approveLink = function ($route, $icon, $text) {
                    return '<a href="' . $route . '" class="dropdown-item status">
                            <div class="row no-gutters align-items-center">
                                <div class="col-2"><i class="ft-' . $icon . '"></i></div>
                                <div class="col-9 offset-1">' . $text . '</div>
                            </div>
                        </a>';
                };

                // Approval 1
                if ($revision->approval1_status == 1) {
                    if (in_array(session('role_id'), [1, 4])) { // Super Admin or Sales Head
                        $actions .= $approveLink(route('admin.settings.shippers.base_rate_revisions.approval1_update', [$revision->id, 2]), 'check', 'Approve');
                        $actions .= $approveLink(route('admin.settings.shippers.base_rate_revisions.approval1_update', [$revision->id, 3]), 'x', 'Reject');
                        $showDropdown = true;
                    }
                }
                // Approval 2
                elseif ($revision->approval1_status == 2 && $revision->approval2_status == 1) {
                    if (in_array(session('role_id'), [1, 2])) { // Super Admin or Finance Head
                        $actions .= $approveLink(route('admin.settings.shippers.base_rate_revisions.approval2_update', [$revision->id, 2]), 'check', 'Approve');
                        $actions .= $approveLink(route('admin.settings.shippers.base_rate_revisions.approval2_update', [$revision->id, 3]), 'x', 'Reject');
                        $showDropdown = true;
                    }
                }

                // Return the dropdown if actions are available
                if ($showDropdown) {
                    return '<div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">' . $actions . '</div>
                        </div>';
                }

                return '';
            });

        return $datatable->make(true);
    }

    public function shippersWithRates($baseRateRevisionId)
    {
        $baseRateRevision = BaseRateRevision::with('shippersWithRateChange.shipper:id,name')->find($baseRateRevisionId);
        $shippers = $baseRateRevision->shippersWithRateChange;
        return response()->json([
            'data' => $shippers
        ]);
    }

    public function approval1_update($baseRateRevisionId, $status)
    {
        $baseRateRevision = BaseRateRevision::find($baseRateRevisionId);
        $baseRateRevision->approval1_status = $status;
        $baseRateRevision->approval1_at = now();
        $baseRateRevision->approval1_by_admin_id = Auth::id();
        $baseRateRevision->save();
        return redirect()->back()->with([($status == 2 ? 'success' : 'error') => 'Base Rate Revision ' . ($status == 2 ? 'Approved' : 'Rejected')]);
    }

    public function approval2_update($baseRateRevisionId, $status)
    {
        $baseRateRevision = BaseRateRevision::with('shippersWithRateChange')->find($baseRateRevisionId);
        $baseRateRevision->approval2_status = $status;
        $baseRateRevision->approval2_at = now();
        $baseRateRevision->approval2_by_admin_id = Auth::id();

        if ($baseRateRevision->save()) {

            if ($baseRateRevision->approval2_status == 2) //If approved
            {
                if ($baseRateRevision->rate_type_id == 1) // Base Rate (Weight charges)
                {
                    foreach ($baseRateRevision->shippersWithRateChange as $shipperWithRateChange) {

                        $change = $shipperWithRateChange->rate_change_percent;
                        $shipperId = $shipperWithRateChange->shipper_id;

                        $shipper = User::where('id', $shipperId)->select('account_type_id', 'corporate_rate_type_id')->first();

                        if ($shipper->account_type_id == 1) {

                            //Update Weight Charges
                            $weightCharges = WeightCharge::where('user_id', $shipperId)->get();

                            foreach ($weightCharges as $weightCharge) {

                                $weightCharge->local_or_6hr = $this->clampToZero($weightCharge->local_or_6hr * (1 + ($change / 100)));
                                $weightCharge->national_charges_class_0 = $this->clampToZero($weightCharge->national_charges_class_0 * (1 + ($change / 100)));

                                if ($weightCharge->national_charges_class_1) {
                                    if (strpos($weightCharge->national_charges_class_1, '%') !== false) {
                                        // If it's a percentage string
                                        $numericValue = intval(rtrim($weightCharge->national_charges_class_1, '%'));
                                        $updatedValue = $numericValue + intval($change);
                                        $weightCharge->national_charges_class_1 = $this->clampToZero($updatedValue) . '%';
                                    } else {
                                        // If it's a plain number
                                        $weightCharge->national_charges_class_1 = $this->clampToZero($weightCharge->national_charges_class_1 * (1 + ($change / 100)));
                                    }
                                }

                                if ($weightCharge->national_charges_class_2) {
                                    if (strpos($weightCharge->national_charges_class_2, '%') !== false) {
                                        // If it's a percentage string
                                        $numericValue = intval(rtrim($weightCharge->national_charges_class_2, '%'));
                                        $updatedValue = $numericValue + intval($change);
                                        $weightCharge->national_charges_class_2 = $this->clampToZero($updatedValue) . '%';
                                    } else {
                                        // If it's a plain number
                                        $weightCharge->national_charges_class_2 = $this->clampToZero($weightCharge->national_charges_class_2 * (1 + ($change / 100)));
                                    }
                                }

                                if ($weightCharge->national_charges_class_3) {
                                    if (strpos($weightCharge->national_charges_class_3, '%') !== false) {
                                        // If it's a percentage string
                                        $numericValue = intval(rtrim($weightCharge->national_charges_class_3, '%'));
                                        $updatedValue = $numericValue + intval($change);
                                        $weightCharge->national_charges_class_3 = $this->clampToZero($updatedValue) . '%';
                                    } else {
                                        // If it's a plain number
                                        $weightCharge->national_charges_class_3 = $this->clampToZero($weightCharge->national_charges_class_3 * (1 + ($change / 100)));
                                    }
                                }

                                $weightCharge->save();
                            }
                        } elseif ($shipper->account_type_id == 2 && $shipper->corporate_rate_type_id == 2) {

                            //Update Corporate Weight Charges Zone Wise
                            $corporateWeightChargeZoneWises = CorporateWeightChargeZoneWise::where('user_id', $shipperId)->get();

                            foreach ($corporateWeightChargeZoneWises as $corporateWeightChargeZoneWise) {

                                $corporateWeightChargeZoneWise->local = $this->clampToZero($corporateWeightChargeZoneWise->local * (1 + ($change / 100)));

                                if ($corporateWeightChargeZoneWise->same_zone) {
                                    if (strpos($corporateWeightChargeZoneWise->same_zone, '%') !== false) {
                                        // If it's a percentage string
                                        $numericValue = intval(rtrim($corporateWeightChargeZoneWise->same_zone, '%'));
                                        $updatedValue = $numericValue + intval($change);
                                        $corporateWeightChargeZoneWise->same_zone = $this->clampToZero($updatedValue) . '%';
                                    } else {
                                        // If it's a plain number
                                        $corporateWeightChargeZoneWise->same_zone = $this->clampToZero($corporateWeightChargeZoneWise->same_zone * (1 + ($change / 100)));
                                    }
                                }

                                if ($corporateWeightChargeZoneWise->different_zone) {
                                    if (strpos($corporateWeightChargeZoneWise->different_zone, '%') !== false) {
                                        // If it's a percentage string
                                        $numericValue = intval(rtrim($corporateWeightChargeZoneWise->different_zone, '%'));
                                        $updatedValue = $numericValue + intval($change);
                                        $corporateWeightChargeZoneWise->different_zone = $this->clampToZero($updatedValue) . '%';
                                    } else {
                                        // If it's a plain number
                                        $corporateWeightChargeZoneWise->different_zone = $this->clampToZero($corporateWeightChargeZoneWise->different_zone * (1 + ($change / 100)));
                                    }
                                }

                                $corporateWeightChargeZoneWise->save();
                            }
                        } elseif ($shipper->account_type_id == 2 && $shipper->corporate_rate_type_id == 3) {

                            //Update Corporate Default Weight Charges
                            $corporateDefaultWeightCharges = CorporateDefaultWeightCharge::where('user_id', $shipperId)->get();

                            foreach ($corporateDefaultWeightCharges as $corporateDefaultWeightCharge) {

                                $corporateDefaultWeightCharge->local_or_6hr = $this->clampToZero($corporateDefaultWeightCharge->local_or_6hr * (1 + ($change / 100)));
                                $corporateDefaultWeightCharge->national_charges_class_0 = $this->clampToZero($corporateDefaultWeightCharge->national_charges_class_0 * (1 + ($change / 100)));

                                if ($corporateDefaultWeightCharge->national_charges_class_1) {
                                    if (strpos($corporateDefaultWeightCharge->national_charges_class_1, '%') !== false) {
                                        // If it's a percentage string
                                        $numericValue = intval(rtrim($corporateDefaultWeightCharge->national_charges_class_1, '%'));
                                        $updatedValue = $numericValue + intval($change);
                                        $corporateDefaultWeightCharge->national_charges_class_1 = $this->clampToZero($updatedValue) . '%';
                                    } else {
                                        // If it's a plain number
                                        $corporateDefaultWeightCharge->national_charges_class_1 = $this->clampToZero($corporateDefaultWeightCharge->national_charges_class_1 * (1 + ($change / 100)));
                                    }
                                }

                                if ($corporateDefaultWeightCharge->national_charges_class_2) {
                                    if (strpos($corporateDefaultWeightCharge->national_charges_class_2, '%') !== false) {
                                        // If it's a percentage string
                                        $numericValue = intval(rtrim($corporateDefaultWeightCharge->national_charges_class_2, '%'));
                                        $updatedValue = $numericValue + intval($change);
                                        $corporateDefaultWeightCharge->national_charges_class_2 = $this->clampToZero($updatedValue) . '%';
                                    } else {
                                        // If it's a plain number
                                        $corporateDefaultWeightCharge->national_charges_class_2 = $this->clampToZero($corporateDefaultWeightCharge->national_charges_class_2 * (1 + ($change / 100)));
                                    }
                                }

                                if ($corporateDefaultWeightCharge->national_charges_class_3) {
                                    if (strpos($corporateDefaultWeightCharge->national_charges_class_3, '%') !== false) {
                                        // If it's a percentage string
                                        $numericValue = intval(rtrim($corporateDefaultWeightCharge->national_charges_class_3, '%'));
                                        $updatedValue = $numericValue + intval($change);
                                        $corporateDefaultWeightCharge->national_charges_class_3 = $this->clampToZero($updatedValue) . '%';
                                    } else {
                                        // If it's a plain number
                                        $corporateDefaultWeightCharge->national_charges_class_3 = $this->clampToZero($corporateDefaultWeightCharge->national_charges_class_3 * (1 + ($change / 100)));
                                    }
                                }

                                $corporateDefaultWeightCharge->save();
                            }
                        } elseif ($shipper->account_type_id == 2 && $shipper->corporate_rate_type_id == 1) {

                            //Update Corporate Weight Charges
                            $corporateWeightCharges = CorporateWeightCharge::where('user_id', $shipperId)->get();

                            foreach ($corporateWeightCharges as $corporateWeightCharge) {

                                $corporateWeightCharge->local_or_6hr = $this->clampToZero($corporateWeightCharge->local_or_6hr * (1 + ($change / 100)));
                                $corporateWeightCharge->national_charges_class_0 = $this->clampToZero($corporateWeightCharge->national_charges_class_0 * (1 + ($change / 100)));

                                if ($corporateWeightCharge->national_charges_class_1) {
                                    if (strpos($corporateWeightCharge->national_charges_class_1, '%') !== false) {
                                        // If it's a percentage string
                                        $numericValue = intval(rtrim($corporateWeightCharge->national_charges_class_1, '%'));
                                        $updatedValue = $numericValue + intval($change);
                                        $corporateWeightCharge->national_charges_class_1 = $this->clampToZero($updatedValue) . '%';
                                    } else {
                                        // If it's a plain number
                                        $corporateWeightCharge->national_charges_class_1 = $this->clampToZero($corporateWeightCharge->national_charges_class_1 * (1 + ($change / 100)));
                                    }
                                }

                                if ($corporateWeightCharge->national_charges_class_2) {
                                    if (strpos($corporateWeightCharge->national_charges_class_2, '%') !== false) {
                                        // If it's a percentage string
                                        $numericValue = intval(rtrim($corporateWeightCharge->national_charges_class_2, '%'));
                                        $updatedValue = $numericValue + intval($change);
                                        $corporateWeightCharge->national_charges_class_2 = $this->clampToZero($updatedValue) . '%';
                                    } else {
                                        // If it's a plain number
                                        $corporateWeightCharge->national_charges_class_2 = $this->clampToZero($corporateWeightCharge->national_charges_class_2 * (1 + ($change / 100)));
                                    }
                                }

                                if ($corporateWeightCharge->national_charges_class_3) {
                                    if (strpos($corporateWeightCharge->national_charges_class_3, '%') !== false) {
                                        // If it's a percentage string
                                        $numericValue = intval(rtrim($corporateWeightCharge->national_charges_class_3, '%'));
                                        $updatedValue = $numericValue + intval($change);
                                        $corporateWeightCharge->national_charges_class_3 = $this->clampToZero($updatedValue) . '%';
                                    } else {
                                        // If it's a plain number
                                        $corporateWeightCharge->national_charges_class_3 = $this->clampToZero($corporateWeightCharge->national_charges_class_3 * (1 + ($change / 100)));
                                    }
                                }

                                $corporateWeightCharge->save();
                            }
                        }
                    }
                } elseif ($baseRateRevision->rate_type_id == 2) // Fuel Surcharges
                {
                    foreach ($baseRateRevision->shippersWithRateChange as $shipperWithRateChange) {

                        $change = $shipperWithRateChange->rate_change_percent;
                        $shipperId = $shipperWithRateChange->shipper_id;

                        $shipper = User::where('id', $shipperId)->select('account_type_id', 'corporate_rate_type_id')->first();

                        if ($shipper->account_type_id == 1) {

                            //Update Fuel Surcharge
                            $fuelSurcharges = FuelSurcharge::where('user_id', $shipperId)->get();

                            foreach ($fuelSurcharges as $fuelSurcharge) {

                                if ($fuelSurcharge->fuel_surcharge) {
                                    $fuelSurcharge->fuel_surcharge = $this->clampToZero($fuelSurcharge->fuel_surcharge * (1 + ($change / 100)));
                                }

                                $fuelSurcharge->save();
                            }
                        } elseif ($shipper->account_type_id == 2 && ($shipper->corporate_rate_type_id == 2 || $shipper->corporate_rate_type_id == 1)) {

                            //Update Corporate Fuel Surcharges
                            $corporateFuelSurcharges = CorporateFuelSurcharge::where('user_id', $shipperId)->get();

                            foreach ($corporateFuelSurcharges as $corporateFuelSurcharge) {

                                if ($corporateFuelSurcharge->fuel_surcharge) {
                                    $corporateFuelSurcharge->fuel_surcharge = $this->clampToZero($corporateFuelSurcharge->fuel_surcharge * (1 + ($change / 100)));
                                }

                                $corporateFuelSurcharge->save();
                            }
                        } elseif ($shipper->account_type_id == 2 && $shipper->corporate_rate_type_id == 3) {

                            //Update Corporate Default Fuel Surcharges
                            $CorporateDefaultFuelSurcharges = CorporateDefaultFuelSurcharge::where('user_id', $shipperId)->get();

                            foreach ($CorporateDefaultFuelSurcharges as $CorporateDefaultFuelSurcharge) {

                                if ($CorporateDefaultFuelSurcharge->fuel_surcharge) {
                                    $CorporateDefaultFuelSurcharge->fuel_surcharge = $this->clampToZero($CorporateDefaultFuelSurcharge->fuel_surcharge * (1 + ($change / 100)));
                                }

                                $CorporateDefaultFuelSurcharge->save();
                            }
                        }
                    }
                } elseif ($baseRateRevision->rate_type_id == 3) //Cash Handling Charges
                {
                    foreach ($baseRateRevision->shippersWithRateChange as $shipperWithRateChange) {

                        $change = $shipperWithRateChange->rate_change_percent;
                        $shipperId = $shipperWithRateChange->shipper_id;

                        $shipper = User::where('id', $shipperId)->select('account_type_id', 'corporate_rate_type_id')->first();

                        if ($shipper->account_type_id == 1) {

                            //Update Cash Handling Charges
                            $cashHandlingCharges = CashHandlingCharge::where('user_id', $shipperId)->get();

                            foreach ($cashHandlingCharges as $cashHandlingCharge) {

                                if ($cashHandlingCharge->charges) {
                                    if (strpos($cashHandlingCharge->charges, '%') !== false) {
                                        // If it's a percentage string
                                        $numericValue = intval(rtrim($cashHandlingCharge->charges, '%'));
                                        $updatedValue = $numericValue + intval($change);
                                        $cashHandlingCharge->charges = $this->clampToZero($updatedValue) . '%';
                                    } else {
                                        // If it's a plain number
                                        $cashHandlingCharge->charges = $this->clampToZero($cashHandlingCharge->charges * (1 + ($change / 100)));
                                    }
                                }

                                $cashHandlingCharge->save();
                            }
                        } elseif ($shipper->account_type_id == 2 && ($shipper->corporate_rate_type_id == 2 || $shipper->corporate_rate_type_id == 1)) {

                            //Update Corporate Cash Handling Charges
                            $corporateCashHandlingCharges = CorporateCashHandlingCharge::where('user_id', $shipperId)->get();

                            foreach ($corporateCashHandlingCharges as $corporateCashHandlingCharge) {

                                if ($corporateCashHandlingCharge->charges) {
                                    if (strpos($corporateCashHandlingCharge->charges, '%') !== false) {
                                        // If it's a percentage string
                                        $numericValue = intval(rtrim($corporateCashHandlingCharge->charges, '%'));
                                        $updatedValue = $numericValue + intval($change);
                                        $corporateCashHandlingCharge->charges = $this->clampToZero($updatedValue) . '%';
                                    } else {
                                        // If it's a plain number
                                        $corporateCashHandlingCharge->charges = $this->clampToZero($corporateCashHandlingCharge->charges * (1 + ($change / 100)));
                                    }
                                }

                                $corporateCashHandlingCharge->save();
                            }
                        } elseif ($shipper->account_type_id == 2 && $shipper->corporate_rate_type_id == 3) {

                            //Update Corporate Default Cash Handling Charges
                            $CorporateDefaultCashHandlingCharges = CorporateDefaultCashHandlingCharge::where('user_id', $shipperId)->get();

                            foreach ($CorporateDefaultCashHandlingCharges as $CorporateDefaultCashHandlingCharge) {

                                if ($CorporateDefaultCashHandlingCharge->charges) {
                                    if (strpos($CorporateDefaultCashHandlingCharge->charges, '%') !== false) {
                                        // If it's a percentage string
                                        $numericValue = intval(rtrim($CorporateDefaultCashHandlingCharge->charges, '%'));
                                        $updatedValue = $numericValue + intval($change);
                                        $CorporateDefaultCashHandlingCharge->charges = $this->clampToZero($updatedValue) . '%';
                                    } else {
                                        // If it's a plain number
                                        $CorporateDefaultCashHandlingCharge->charges = $this->clampToZero($CorporateDefaultCashHandlingCharge->charges * (1 + ($change / 100)));
                                    }
                                }

                                $CorporateDefaultCashHandlingCharge->save();
                            }
                        }
                    }
                }
            }
        } else {
            return redirect()->back()->with('error', 'Something went wrong');
        }

        return redirect()->back()->with([($status == 2 ? 'success' : 'error') => 'Base Rate Revision ' . ($status == 2 ? 'Approved' : 'Rejected')]);
    }

    private function clampToZero($value)
    {
        //for returning minimum value of 0
        return max(0, round($value));
    }
}
