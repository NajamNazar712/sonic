<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpectedShipmentPenaltyAdjustment;
use Yajra\Datatables\Datatables;


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


    }
}
