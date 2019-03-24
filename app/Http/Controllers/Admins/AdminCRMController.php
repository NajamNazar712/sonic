<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\CRM\CRMController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminCRMController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function add_request(Request $request){
        $nature_id = $request->case_nature_id;
        $complaint_id = $request->complaint_id;
        $channel_id = $request->channel_id;
        $shipment_ids = $request->shipment_ids;
        if(!empty($shipment_ids)){
            foreach ($shipment_ids as $shipment_id) {
                CRMController::add_request($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id);
            }
            return ['status' => 1, 'success' => 'Request(s) successfully added'];
        }else{
            return ['status' => 0, 'error' => 'No shipments selected!'];
        }
    }
}
