<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\V2Dispute;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class V2AdminDisputeShipmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index(){
        return view('admin.v2_dispute.index');
    }
    public function list(Request $request){
        $dispute = V2Dispute::join('shipments', 'shipments.id', '=', 'v2_disputes.shipment_id');
    }
}
