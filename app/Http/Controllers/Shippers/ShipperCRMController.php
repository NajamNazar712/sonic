<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\CRM\CRMController;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ShipperCRMController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function add_request(Request $request){
        $nature_id = $request->case_nature_id;
        $complaint_id = $request->complaint_id;
        $shipment_ids = $request->shipment_ids;
        $description = $request->description;
        $launched_by = 1;
//        $present_shipments = array();
//        $flag = false;
        if(session('user_type') == 2){
            $launched_by = 2;
        }
        if(!empty($shipment_ids)){
            foreach ($shipment_ids as $shipment_id) {

//                $is_shipment = CrmRequest::where('shipment_id',$shipment_id)->first();
//                if($is_shipment){
//                    if($is_shipment->case_nature_id != $nature_id){
                        CRMController::add_request($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
//                    }else{
//                        $shipment = Shipment::find($shipment_id);
//                        $present_shipments[] = $shipment->tracking_number;
//                        $flag = true;
//                    }
//                }

            }
//            return ['status' => 1, 'success' => 'Request(s) successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];
            return ['status' => 1, 'success' => 'Request(s) successfully added'];
        }else{
            return ['status' => 0, 'error' => 'No shipments selected!'];
        }
    }

    public function add_feedback(Request $request){
        $nature_id = 3;
        $channel_id = 1;
        $description = $request->description;
        $launched_by = 1;
        if(session('user_type') == 2){
            $launched_by = 2;
        }
        if($channel_id == null){
            return ['status' => 0, 'error' => 'Channel Not selected!'];
        }
        if($description == null){
            return ['status' => 0, 'error' => 'Description Not Entered!'];
        }

        CRMController::add_request($nature_id, NULL, $channel_id, 1, Auth::id(), $launched_by, NULL, session('user_id'), NULL ,$description);
        return ['status' => 1, 'success' => 'Feedback successfully added'];
    }
}
