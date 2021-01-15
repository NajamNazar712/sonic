<?php

namespace App\Http\Controllers\Retail;

use App\http\Models\Admin\Retail\RetailCashDeposit;
use App\http\Models\Admin\Retail\RetailCashDepositShipment;
use App\http\Models\Admin\Retail\RetailShippingMode;
use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class RetailCashDepositController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:retail');

//        $this->middleware('Permission');
    }

    public function index(){
        $shipping_modes = RetailShippingMode::all();
        return view('retail.cash_deposit.cash_deposit')->with(['shipping_modes' => $shipping_modes]);
    }

    public function list(Request $request){
        $cash_deposit = RetailCashDeposit::join('retail_shipping_modes as rsm', 'rsm.id', '=', 'retail_cash_deposits.shipping_mode_id')
            ->join('retail_users as ru', 'ru.id', '=', 'retail_cash_deposits.retail_user_id')
            ->select('retail_cash_deposits.id as performa_no', 'retail_cash_deposits.category as category', 'rsm.name as shipping_mode', 'ru.name as user', 'retail_cash_deposits.total_cn as total_shipments', 'retail_cash_deposits.total_cash as total_cash', DB::raw('DATE(retail_cash_deposits.created_at) AS booking_date'), 'ru.id as employee_id')
        ->where('retail_cash_deposits.retail_user_id', Auth::id());
        $datatable = Datatables::of($cash_deposit)
            ->addColumn('shipments_button', function ($data) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $data->total_shipments . '</button>';
            })
            ->editColumn('performa_no', function ($data) {
                return str_pad($data->performa_no, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('category', function ($data) {
                if($data->category == 1){
                    return 'Franchise';
                }
                else{
                    return 'Trax Center';
                }
            })
            ->editColumn('total_cash', function ($data) {
                return number_format($data->total_cash);
            })
            ->addColumn('booking_code', function ($data) {
                return str_pad($data->employee_id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('performa_button', function ($data) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . str_pad($data->performa_no, 6, '0', STR_PAD_LEFT) . '</button>';
            });

        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $cash_deposit->whereBetween('retail_cash_deposits.created_at', [$from,$to]);
        }
        return  $datatable->make(true);
    }

    public function shipments(Request $request){
        $cash_deposit_id = $request->input('performa_no');
        $cash_deposit_shipments = RetailCashDepositShipment::where('cash_deposit_id', $cash_deposit_id)->get();
        $shipments = array();
        if($cash_deposit_shipments->count() != 0){
            foreach ($cash_deposit_shipments as $cash_deposit_shipment){
                $shipment = Shipment::find($cash_deposit_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 1, 'success' => 'Cash Deposit Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Cash Deposit Shipments', 'shipments' => FALSE];
        }
    }
}
