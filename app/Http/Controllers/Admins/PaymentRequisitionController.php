<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\BanksList;
use App\Http\Models\Admin\PettyCashAccountTitle;
use App\Http\Models\Admin\AdminDepartment;
use App\Models\PaymentRequisition;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use App\Models\PaymentRequisitionApproval;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\Admin;
use DB;
use App\Models\PaymentRequisitionJourney;
use App\Models\PRFComment;

class PaymentRequisitionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $banks = BanksList::get();
        $accounts = PettyCashAccountTitle::get();
        $departments = AdminDepartment::where('id' , '!=' , session('department_id'))->get();
       // dd( session('department_id') );
        return view('admin.finance.prf.index')->with(['banks' => $banks, 'accounts' => $accounts, 'departments' => $departments]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // $request->validate([
        //     'invoice_id' => 'required|string|max:255',
        //     'payee_name' => 'required|string|max:255',
        //     'ntn_cnic' => 'nullable|string|max:255',
        //     'bank_name' => 'required',
        //     'bank_title' => 'required|string|max:255',
        //     'iban' => 'required|string|max:255',
        //     'amount' => 'required|numeric|min:1',
        //     'account_of' => 'required',
        //     'related_department' => 'nullable',
        //     'description' => 'nullable|string',
        //     'document1' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        //     'document2' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        //     'document3' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        //     'document4' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        // ]);
        $hasDocument = false;

        $admin_department = AdminRole::find(Auth::user()->role_id);

        $data = new PaymentRequisition;
        $data->requester_id = Auth::id();
        $data->invoice_no = $request->invoice_id;
        $data->payee_name = $request->payee_name;
        $data->ntn_cnic = $request->ntn_cnic;
        $data->bank_id = $request->bank_id;
        $data->bank_title =  $request->bank_title;
        $data->iban =  $request->iban;
        $data->amount =  $request->amount;
        $data->account_of_id =  $request->account_of_id;
        $data->related_department_id =  $request->related_department_id;
        $data->description =  $request->description;
        $data->requester_department_id =  $admin_department->department_id;
        

        foreach (['document1', 'document2', 'document3', 'document4'] as $doc) {
            if ($request->hasFile($doc)) {
                $hasDocument = true;
                $file = $request->file($doc);
                //$filename = $file->getClie
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/payment_requisitions/'), $filename);
                $data->{$doc} =  $filename;
            }
        }

        $data->status =  $hasDocument ? 3 : 2;
        //$data->status_updated_at = Carbon::now();
        $data->save();
        $this->logJourney($data->id, Auth::id(), 1, 'Request Submitted');
        $this->logJourney($data->id, Auth::id(), $hasDocument ? 3 : 2, $hasDocument ? 'Documents uploaded and sent for approval' : 'Documents not uploaded.' );

        $approvals = [];

        $requesterRole = AdminRole::find(Auth::user()->role_id);
        $requesterDept = AdminDepartment::find($requesterRole->department_id);

        if (
            $requesterDept->department_head_id &&
            $requesterDept->department_head_id != Auth::id() // skip if requester is the dept head
        ) {
            $deptHead = Admin::find($requesterDept->department_head_id);
            $approvals[] = [
                'payment_requisition_id' => $data->id,
                'approver_role_id' => $deptHead->role_id,
                'dept_id' => $requesterRole->department_id,
                'level' => 1,
                'status' => 'active', // first approver starts active
            ];
        }

        if ($request->related_department_id) {
            $relatedDept = AdminDepartment::find($request->related_department_id);

            if ($relatedDept && $relatedDept->department_head_id) {
                $relatedDeptHead = Admin::find($relatedDept->department_head_id);
                $approvals[] = [
                    'payment_requisition_id' => $data->id,
                    'approver_role_id' => $relatedDeptHead->role_id,
                    'dept_id' => $request->related_department_id,
                    'level' => count($approvals) + 1,
                    'status' => empty($approvals) ? 'active' : 'pending',
                ];
            }
        }

        $approvals[] = [
            'payment_requisition_id' => $data->id,
            'approver_role_id' => 2,
            'dept_id' => 4, 
            'level' => count($approvals) + 1,
            'status' => empty($approvals) ? 'active' : 'pending',
        ];

        foreach ($approvals as $approval) {
            PaymentRequisitionApproval::create($approval);
        }

        return redirect()->back()->with(['status' => 1, 'success' => 'Submitted successfully!']);
      
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        return view('admin.finance.prf.list');
    }

    public function ajaxList(Request $request)
    {
    
        $permissions = session('permissions', []);

        $userDept = session('department_id');
        $userRole = session('role_id');
        $approvableAllIds = DB::table('payment_requisition_approvals')
            ->where('dept_id', $userDept)
            ->where('approver_role_id', $userRole)
            ->pluck('payment_requisition_id')
            ->toArray();
            
        $approvableActiveIds = DB::table('payment_requisition_approvals')
            ->where('dept_id', $userDept)
            ->where('approver_role_id', $userRole)
            ->where('status', 'active')
            ->pluck('payment_requisition_id')
            ->toArray();

        $query = PaymentRequisition::query()
        ->select([
            'payment_requisitions.*',
            'u.name as requester_name',
            'ud.name as requester_department',
            'rd.name as requested_for_department',
            'ao.name as account_of_name',
            'b.name as bank_name',
            'status.name as status_name',
            DB::raw('GROUP_CONCAT(DISTINCT ad.name ORDER BY pra.level SEPARATOR ", ") as required_approvals'),
            DB::raw('GROUP_CONCAT(DISTINCT appr.name ORDER BY pra.level SEPARATOR ", ") as approved_by_names')
        ])
        ->leftJoin('banks_lists as b', 'b.id', 'payment_requisitions.bank_id' )
        ->leftJoin('admins as u', 'u.id', 'payment_requisitions.requester_id')
        // ->leftJoin('admin_roles as r', 'r.id',  'u.role_id')
        ->leftJoin('admin_departments as ud', 'ud.id',  'payment_requisitions.requester_department_id')
        ->leftJoin('admin_departments as rd', 'rd.id',  'payment_requisitions.related_department_id')
        ->leftJoin('petty_cash_account_titles as ao', 'ao.id',  'payment_requisitions.account_of_id')
        ->leftJoin('prf_statuses as status', 'status.id', 'payment_requisitions.status')
        ->leftJoin('payment_requisition_approvals as pra', 'pra.payment_requisition_id', '=', 'payment_requisitions.id')
        ->leftJoin('admin_departments as ad', 'ad.id', '=', 'pra.dept_id')
        ->leftJoin('admins as appr', 'appr.id', '=', 'pra.approved_by');
        // ->where(function ($q) use ($approvableAllIds) {
        //     $q->where('payment_requisitions.requester_id', Auth::id());
        //     if (!empty($approvableAllIds)) {
        //         $q->orWhereIn('payment_requisitions.id', $approvableAllIds);
        //     }
        // })

        if (!in_array(1048, $permissions) && !in_array(1049, $permissions)) {
            $query->where(function ($q) use ($approvableAllIds) {
                $q->where('payment_requisitions.requester_id', Auth::id());
                if (!empty($approvableAllIds)) {
                    $q->orWhereIn('payment_requisitions.id', $approvableAllIds);
                }
            });
        }

        
        $query->whereIn('payment_requisitions.status', [1, 2, 3, 4, 5])
        ->groupBy('payment_requisitions.id')
        ->orderBy('payment_requisitions.id', 'desc');

         return Datatables::of($query)
            ->addColumn('required_approvals', function ($data) {
                return $data->required_approvals ?: '-';
            })
             ->addColumn('approved_by', function ($data) {
                return $data->approved_by_names ?: '-';
            })
            ->addColumn('documents', function ($data) {
                $d = '';
                if ($data->document1) {
                    $d .= "<a href='" . asset('/uploads/payment_requisitions/'.$data->document1) . "' target='_blank'>Document 1</a><br>";
                }
                if ($data->document2) {
                    $d .= "<a href='" . asset($data->document2) . "' target='_blank'>Document 2</a><br>";
                }
                if ($data->document3) {
                    $d .= "<a href='" . asset($data->document3) . "' target='_blank'>Document 3</a><br>";
                }
                if ($data->document4) {
                    $d .= "<a href='" . asset($data->document4) . "' target='_blank'>Document 4</a><br>";
                }

                return $d ?: '-';
            })
            // ->addColumn('can_approve', function ($data) use ($approvableIds) {
            //     return (in_array($data->id, $approvableIds) && $data->status == 3)  ? 1 : 0;
            // })
            ->addColumn('can_approve', function ($data) use ($approvableActiveIds) {
                return (in_array($data->id, $approvableActiveIds) && $data->status == 3) ? 1 : 0;
            })
            ->addColumn("action", function ($data) {
                
                $dropdown = '
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                ';
                        
            
                if (( session('role_id') == 1 || $data->requester_id == Auth::id() ) && in_array($data->status, [1,2]  )  ) {
                    $dropdown .= '<button onclick="window.open(\'' . route('admin.finance.prf.edit', ['id' => $data->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                }

                $dropdown .='<button type="button" class="dropdown-item view_journey" data-target-id=' . $data->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-eye"></i></div><div class="col-9 offset-1">View Journey</div></button>';
                
                $dropdown .='<button type="button" class="dropdown-item approval_logs" data-target-id=' . $data->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-eye"></i></div><div class="col-9 offset-1">View Approval Logs</div></button>';
                if (session('role_id') == 1 || in_array(1048, session('permissions'))    ) {
                   $dropdown .= '<button type="button" class="dropdown-item add_cheque" data-target-id=' . $data->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Cheque No</div></button>';
                }
    

                if ( ( $data->requester_id == Auth::id() || (isset($approvalStatuses[$data->id]) && $approvalStatuses[$data->id] == 'active' ) )
                    
                     && in_array($data->status, [1,2,3])   
                ) {
                    $dropdown .= '<button type="button" class="dropdown-item cancel_requisition" data-target-id="' . $data->id . '">
                        <div class="row no-gutters align-items-center">
                            <div class="col-2"><i class="ft-x-circle text-danger"></i></div>
                            <div class="col-9 offset-1">Cancel</div>
                        </div>
                    </button>';
                }

                 $dropdown .= '<button onclick="window.open(\'' . route('admin.finance.prf.chat', ['id' => $data->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Chat</div></button>';

    
                $dropdown .= '
                    </div>
                </div>
                ';
                return $dropdown;
                
            })
            ->rawColumns(['documents', 'action'])
            ->make(true);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $requisition = PaymentRequisition::find($id);
        $banks = BanksList::get();
        $accounts = PettyCashAccountTitle::get();
        $departments = AdminDepartment::get();

        return view('admin.finance.prf.edit')->with(['requisition'=> $requisition ,'banks' => $banks, 'accounts' => $accounts, 'departments' => $departments]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $requisition = PaymentRequisition::find($id);

        // --- Validation ---
        // $request->validate([
        //     'invoice_id' => 'required|string|max:255',
        //     'payee_name' => 'required|string|max:255',
        //     'ntn_cnic' => 'required|string|max:255',
        //     'bank_id' => 'required|integer',
        //     'bank_title' => 'required|string|max:255',
        //     'iban' => 'required|string|max:255',
        //     'amount' => 'required|numeric|min:1',
        //     'account_of_id' => 'required|integer',
        //     'related_department_id' => 'nullable|integer',
        //     'description' => 'nullable|string',
        //     'document1' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        //     'document2' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        //     'document3' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        //     'document4' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        // ]);

        // --- Update Basic Fields ---
        $requisition->invoice_no = $request->invoice_id;
        $requisition->payee_name = $request->payee_name;
        $requisition->ntn_cnic = $request->ntn_cnic;
        $requisition->bank_id = $request->bank_id;
        $requisition->bank_title = $request->bank_title;
        $requisition->iban = $request->iban;
        $requisition->amount = $request->amount;
        $requisition->account_of_id = $request->account_of_id;
        $requisition->related_department_id = $request->related_department_id;
        $requisition->description = $request->description;
        //$requisition->updated_by = Auth::id(); // assuming you track updater

       
        $hasDocument = $requisition->status == 2 ? false : true ;
        foreach (['document1', 'document2', 'document3', 'document4'] as $doc) {
            if ($request->hasFile($doc)) {
                $hasDocument = true;
                //dd('t');
                $file = $request->file($doc);
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/payment_requisitions/'), $filename);
                $requisition->{$doc} = $filename;
            }
        }

        $requisition->status =  $hasDocument ? 3 : 2;
       
        $requisition->save();

        if($hasDocument) {
            $this->logJourney($requisition->id, Auth::id() , 3, 'Documents uploaded and sent for approval');
        }

        return redirect()
            ->route('admin.finance.prf.list')
            ->with('success', 'Payment Requisition updated successfully.');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request)
    {
        $data = PaymentRequisition::find($request->id);
        $data->status = 7;
        $data->save();

        $this->logJourney($data->id, Auth::id() , 7 , 'Request cancelled.!');
        return response()->json(['status' => 1, 'message' => 'Cancelled Successfully.']);

    }

    public function approve(Request $request)
    {
        $ids = $request->input('ids'); 
        $user = Auth::user();

        foreach ($ids as $id) {
            $requisition = PaymentRequisition::find($id);
            
            $activeApproval = PaymentRequisitionApproval::where('payment_requisition_id', $id)
                ->where('status', 'active')
                ->first();

            if (!$activeApproval ||
                $activeApproval->approver_role_id != $user->role_id ||
                $activeApproval->dept_id != session('department_id')) {
                
                continue;
            }

            $activeApproval->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => Carbon::now(),
            ]);

            $nextApproval = PaymentRequisitionApproval::where('payment_requisition_id', $id)
                ->where('level', $activeApproval->level + 1)
                ->first();

            if ($nextApproval) {
                $nextApproval->update(['status' => 'active']);
                //$requisition->update(['status' => 4]); 
            } else {
                if($requisition->cheque) {
                    $requisition->update(['status' => 5]);
                } else {
                    $requisition->update(['status' => 4]);
                    $this->logJourney($requisition->id, Auth::id() , 4, 'All level approved. Pending for payment creation.');
                }
            }

        }

        return response()->json(['status' => 1, 'message' => 'Selected payment requisitions approved successfully.']);
    }

    public function add_cheque(Request $request) {

        $cheque_no = $request->cheque_no;

        $requisition = PaymentRequisition::find($request->request_id);
        if($requisition) {

            $requisition->cheque_no = $cheque_no;
            $pendingApprovals =  PaymentRequisitionApproval::where('payment_requisition_id', $request->request_id)
                ->where('status', 'active')
                ->count();
            if($pendingApprovals == 0) {
                $requisition->status = 5;
                $this->logJourney($requisition->id, Auth::id() , 5, 'Approved and Created');
            }
            $requisition->save();
        }

        return redirect()->back()->with('success', 'Payment Requisition updated successfully.');
        
    }


    public function view_journey(Request $request) {


        $journey = PaymentRequisitionJourney::select(['payment_requisition_journeys.payment_requisition_id as request_id', 'payment_requisition_journeys.remarks as remarks', 'payment_requisition_journeys.created_at as created', 'prf_statuses.name as status', 'a.name as updated_by'])
        ->leftJoin('prf_statuses', 'prf_statuses.id' , 'payment_requisition_journeys.status')
        ->leftJoin('admins as a', 'a.id' , 'payment_requisition_journeys.changed_by')
        ->where('payment_requisition_journeys.payment_requisition_id', $request->id)
        ->get();

        return response()->json(['status' => 1 , 'data' => $journey]);


    }

    public function approval_logs(Request $request) {

        $logs = PaymentRequisitionApproval::select(['a.name as approved_by','payment_requisition_approvals.level as level', 'payment_requisition_approvals.updated_at as approved_at', 'payment_requisition_approvals.status as status', 'payment_requisition_approvals.payment_requisition_id as request_id' ])
        ->leftJoin('admins as a', 'a.id', 'payment_requisition_approvals.approved_by')
        ->where('payment_requisition_approvals.payment_requisition_id', $request->id )
        ->where('payment_requisition_approvals.status', 'approved')
        ->get();

        return response()->json(['status' => 1 , 'data' => $logs]);

    }
    protected function logJourney($paymentRequisitionId, $userId, $status, $remarks = null)
    {
        PaymentRequisitionJourney::create([
            'payment_requisition_id' => $paymentRequisitionId,
            'changed_by' => $userId,
            'status' => $status,
            'remarks' => $remarks,
        ]);
    }

    public function complete(Request $request)
    {
        $ids = $request->ids; // array of IDs

        if (empty($ids) || !is_array($ids)) {
            return response()->json(['status' => false, 'message' => 'No requests selected.']);
        }

        // Get all matching requisitions with status = 5
        $requisitions = PaymentRequisition::whereIn('id', $ids)
            ->where('status', 5)
            ->get();

        if ($requisitions->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'No valid requests found.']);
        }

        $completedIds = [];

        foreach ($requisitions as $req) {
            $req->status = 6; // e.g., Completed
            $req->save();

            $completedIds[] = $req->id;

            $this->logJourney($$req->id, Auth::id() , 6, 'Marked as Completed.');
        }

        return response()->json([
            'status' => true,
            'message' => 'Selected requests marked as completed successfully.',
            'completed_ids' => $completedIds
        ]);
    }


    public function completed_list (Request $request)
    {
        return view('admin.finance.prf.completed');
    }

    public function ajaxCompletedList (Request $request) 
      {
    
         $permissions = session('permissions', []);
        $userDept = session('department_id');
        $userRole = session('role_id');
        $approvableAllIds = DB::table('payment_requisition_approvals')
            ->where('dept_id', $userDept)
            ->where('approver_role_id', $userRole)
            ->pluck('payment_requisition_id')
            ->toArray();

        $query = PaymentRequisition::query()
        ->select([
            'payment_requisitions.*',
            'u.name as requester_name',
            'ud.name as requester_department',
            'rd.name as requested_for_department',
            'ao.name as account_of_name',
            'b.name as bank_name',
            'status.name as status_name',
            DB::raw('GROUP_CONCAT(DISTINCT ad.name ORDER BY pra.level SEPARATOR ", ") as required_approvals'),
            DB::raw('GROUP_CONCAT(DISTINCT appr.name ORDER BY pra.level SEPARATOR ", ") as approved_by_names')
        ])
        ->leftJoin('banks_lists as b', 'b.id', 'payment_requisitions.bank_id' )
        ->leftJoin('admins as u', 'u.id', 'payment_requisitions.requester_id')
        // ->leftJoin('admin_roles as r', 'r.id',  'u.role_id')
        ->leftJoin('admin_departments as ud', 'ud.id',  'payment_requisitions.requester_department_id')
        ->leftJoin('admin_departments as rd', 'rd.id',  'payment_requisitions.related_department_id')
        ->leftJoin('petty_cash_account_titles as ao', 'ao.id',  'payment_requisitions.account_of_id')
        ->leftJoin('prf_statuses as status', 'status.id', 'payment_requisitions.status')
        ->leftJoin('payment_requisition_approvals as pra', 'pra.payment_requisition_id', '=', 'payment_requisitions.id')
        ->leftJoin('admin_departments as ad', 'ad.id', '=', 'pra.dept_id')
        ->leftJoin('admins as appr', 'appr.id', '=', 'pra.approved_by');

        if (!in_array(1048, $permissions) && !in_array(1049, $permissions)) {
            $query->where(function ($q) use ($approvableAllIds) {
                $q->where('payment_requisitions.requester_id', Auth::id());
                if (!empty($approvableAllIds)) {
                    $q->orWhereIn('payment_requisitions.id', $approvableAllIds);
                }
            });
        }
        // ->where(function ($q) use ($approvableAllIds) {
        //     $q->where('payment_requisitions.requester_id', Auth::id());
        //     if (!empty($approvableAllIds)) {
        //         $q->orWhereIn('payment_requisitions.id', $approvableAllIds);
        //     }
        // })
         
        $query->whereIn('payment_requisitions.status', [6,7])
        ->groupBy('payment_requisitions.id')
        ->orderBy('payment_requisitions.id', 'desc');

         return Datatables::of($query)
            ->addColumn('required_approvals', function ($data) {
                return $data->required_approvals ?: '-';
            })
             ->addColumn('approved_by', function ($data) {
                return $data->approved_by_names ?: '-';
            })
            ->addColumn('documents', function ($data) {
                $d = '';
                if ($data->document1) {
                    $d .= "<a href='" . asset('/uploads/payment_requisitions/'.$data->document1) . "' target='_blank'>Document 1</a><br>";
                }
                if ($data->document2) {
                    $d .= "<a href='" . asset($data->document2) . "' target='_blank'>Document 2</a><br>";
                }
                if ($data->document3) {
                    $d .= "<a href='" . asset($data->document3) . "' target='_blank'>Document 3</a><br>";
                }
                if ($data->document4) {
                    $d .= "<a href='" . asset($data->document4) . "' target='_blank'>Document 4</a><br>";
                }

                return $d ?: '-';
            })
            ->addColumn("action", function ($data) {
                
                $dropdown = '
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                ';
                        
                $dropdown .='<button type="button" class="dropdown-item view_journey" data-target-id=' . $data->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-eye"></i></div><div class="col-9 offset-1">View Journey</div></button>';
                
                $dropdown .='<button type="button" class="dropdown-item approval_logs" data-target-id=' . $data->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-eye"></i></div><div class="col-9 offset-1">View Approval Logs</div></button>';
              
    
                $dropdown .= '
                    </div>
                </div>
                ';
                return $dropdown;
                
            })
            ->rawColumns(['documents', 'action'])
            ->make(true);

    }


    public function chat_view(Request $request, $id) {

        
        $requisition = PaymentRequisition::query()
        ->select([
            'payment_requisitions.*',
            'u.name as requester_name',
            'ud.name as requester_department',
            'rd.name as requested_for_department',
            'ao.name as account_of_name',
            'status.name as status_name',
        ])
        ->leftJoin('admins as u', 'u.id', 'payment_requisitions.requester_id')
        ->leftJoin('admin_departments as ud', 'ud.id',  'payment_requisitions.requester_department_id')
        ->leftJoin('admin_departments as rd', 'rd.id',  'payment_requisitions.related_department_id')
        ->leftJoin('petty_cash_account_titles as ao', 'ao.id',  'payment_requisitions.account_of_id')
         ->leftJoin('prf_statuses as status', 'status.id', 'payment_requisitions.status')
        ->where('payment_requisitions.id', $id)->first();
        
        $comments  = PRFComment::select(['prf_comments.comment as comment', 'prf_comments.created_at as created', 'a.name as name', 'prf_comments.comment_by_id as commenter_id'])
        ->leftJoin('admins as a', 'a.id', 'prf_comments.comment_by_id')->where('request_id',  $id)->get();
        $last_comment_id = PRFComment::where('request_id', $id)->latest()->pluck('id')->first();
   
        return view('admin.finance.prf.chat')->with(['requisition' => $requisition,  'comments' => $comments, 'last_comment_id' => $last_comment_id]);
    }


    public function add_comment(Request $request) {
        
 
        $comment = $request->comment;
        $request_id = $request->request_id;
       
        if($comment == null){
            return ['status' => 0, 'error' => 'Comment Not selected!'];
        }
        if(!$request_id){
            return ['status' => 0, 'error' => 'Request ID Not selected!'];
        }
        $comment = new PRFComment();
        $comment->request_id = $request->request_id;
        $comment->comment_by_id = Auth::id();
        $comment->comment = $request->comment;
        $comment->created_at = now();
        $comment->updated_at = now();
        $comment->save();

        return ['status' => 1, 'success' => 'Comment successfully added', 'last_comment_id' => $comment->id];
        
    }


    public function get_latest_comment(Request $request) {

        $comment_id = $request->comment_id;
        $request_id = $request->request_id;
        if(($comment_id != null) && ($request_id != null)){            

            $comments  = PRFComment::select(['prf_comments.id', 'prf_comments.comment as comment', 'prf_comments.created_at as created', 'a.name as name', 'prf_comments.comment_by_id as commenter_id'])
            ->leftJoin('admins as a', 'a.id', 'prf_comments.comment_by_id')->where('request_id',  $request_id)->where('prf_comments.id', '>', $comment_id)->first();
            if($comments) {
                return ['status' => 1, 'comment' => $comments ];
            }
   
        }
        
    }
}
