<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpectedShipmentPenaltyAdjustment;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\PendingPaymentCalculation;
use Illuminate\Support\Str;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\ShipmentsPaymentJourneyController;
use Carbon\Carbon;


class ExpectedShipmentPenaltyAdjustmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function index(Request $request) {

        return view('admin.finance.expected_shipment_penalties');
    }


    public function list (Request $request) {

        $data = DB::table('expected_shipment_penalty_adjustments as esp')
        ->leftJoin('shipments as s', 's.id', '=', 'esp.shipment_id')
        ->leftJoin('users as u', 'u.id', '=', 'esp.user_id')
        ->leftJoin('adjustment_types as at', 'at.id', '=', 'esp.adjustment_type_id')
        ->leftJoin('admins as a', 'a.id', '=', 'esp.status_updated_by')
        ->select([
            's.tracking_number as tracking_number',
            'u.name as shipper_name',
            'at.name as adjust_name',
            'a.name as updated_by',
            'esp.*'
        ])
        ->orderByDesc('esp.created_at');

        return Datatables::of($data) 
        ->editColumn('status', function ($row) {

            if($row->status == 1){
                return 'Created';
            }else if($row->status == 2) {
                return 'Approved';
            } else if($row->status == 3) {
                return 'Rejected';
            }
        })
        ->editColumn('applied_month', function ($row) {

            if($row->applied_month) {

                return Carbon::parse($row->applied_month)->format('F Y');
            } else {
                return '-';
            }   
        })
        ->addColumn('action', function ($row) {

            $dropdown = '<div class="btn-group">
            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
            <div class="dropdown-menu dropdown-menu-sm">';

            if($row->status == 1) {
                 $dropdown .= '<button type="button" class="dropdown-item approve"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Approve</div></button>';

                $dropdown .= '<button type="button" class="dropdown-item reject"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Reject</div></button>';
            }

            $dropdown .= '
                    </div>
                  </div>
            ';
            return $dropdown;
        })
        ->rawColumns(['action'])
        ->make(true);

    }

    public function approve(Request $request) {

        $transaction_id = (string) Str::uuid();
        $record = ExpectedShipmentPenaltyAdjustment::find($request->id);
        $record->status = 2;
        $record->status_updated_by = Auth::id();
        $record->status_updated_at = now();
        $record->save();

        $pending_payment = PendingPayment::where('user_id', $record->user_id);

        $payable = 0 - $record->amount;
        if ($pending_payment->exists()) {
            $pending_payment = $pending_payment->first();

            $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
            $pending_payment->adjusted_shipments = $pending_payment->adjusted_shipments + 1;

            $pending_payment->save();
        } else {
            $pending_payment = new PendingPayment();

            $pending_payment->user_id = $record->user_id;
            $pending_payment->total_shipments = 1;
            $pending_payment->delivered_shipments = 0;
            $pending_payment->returned_shipments = 0;
            $pending_payment->adjusted_shipments = 1;
            $pending_payment->arrival_shipment = 0;

            $pending_payment->save();
        }

        $pending_payment_shipment = new PendingPaymentShipment();

        $pending_payment_shipment->pending_payment_id = $pending_payment->id;
        $pending_payment_shipment->shipment_id = $record->shipment_id;
        $pending_payment_shipment->type = 2;
        $pending_payment_shipment->amount = 0;
        $pending_payment_shipment->charges = 0;
        $pending_payment_shipment->gst = 0;
        $pending_payment_shipment->payable = $payable;
        $pending_payment_shipment->transaction_id = $transaction_id;
        $pending_payment_shipment->save();

        AdminFinanceController::add_pending_payment_charges($pending_payment->id, 0, 0, 0, $payable);
        AdminFinanceController::adjustment_logs_add($pending_payment_shipment->shipment_id, 4, $pending_payment_shipment->payable, 'Low Acheivement Penalty', $pending_payment_shipment->id, 1);
        ShipmentsPaymentJourneyController::add($pending_payment_shipment->shipment_id, 4, Auth::id(),'Low Acheivement Penalty');

        return redirect()->back()->with(['success', 'Rejected Successfully..!']);
    }   

    public function reject(Request $request){

        $record = ExpectedShipmentPenaltyAdjustment::find($request->id);
        $record->status = 3;
        $record->status_updated_by = Auth::id();
        $record->status_updated_at = now();
        $record->save();

        return redirect()->back()->with(['success', 'Rejected Successfully..!']);
    }
}
