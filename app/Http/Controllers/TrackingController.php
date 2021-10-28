<?php

namespace App\Http\Controllers;


use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestChannel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\Shipment;



class TrackingController extends Controller
{
    public function index() {
        $case_nature = CrmRequestCaseNature::get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
        $case_nature_channels = CrmRequestChannel::where('id', '!=', 1)->get();
        return view('tracking')->with(['case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_channels' => $case_nature_channels]);
    }

    public function track(Request $request) {
    	$tracking_numbers = explode(',', $request->tracking_numbers);

    	$tracking = array();

    	foreach ($tracking_numbers as $tracking_number) {
    		$shipment = Shipment::where('tracking_number', $tracking_number);

    		if ($shipment->exists()) {
                $shipment = $shipment->first();

                if ($shipment->user->blacklist == 0) {
        			$details = array();

                    $details['tracking_number'] = $tracking_number;

        			$details['shipper']['name'] = $shipment->user->name;

                    $details['pickup']['origin'] = $shipment->pickup_address->city->name;

        			$details['consignee']['name'] = $shipment->consignee_name;
        			$details['consignee']['destination'] = $shipment->consignee_city->name;

        			foreach ($shipment->shipment_journey as $journey) {
                        if ($journey->consignee_status_id) {
                            if ($journey->verification) {
                                $journey_details = array();

                                $journey_details['date_time'] = $journey->created_at->toDateTimeString();
                                $journey_details['status'] = $journey->shipment_status_consignee->name;

                                $details['tracking_history'][] = $journey_details;
                            }

                        }
        			}

        			$tracking['shipments'][$shipment->id] = $details;
                }
                else {
                    $tracking['invalid'][] = $tracking_number;
                }
    		}
    		else {
    			$tracking['invalid'][] = $tracking_number;
    		}
    	}

    	return $tracking;
    }


    public function add_request(Request $request){

        $case_nature = 1;
        $case_nature_type = $request->complaint_id;
        $request_channel = 2;
        $discription = $request->description;
        
        $shipment_id = $request->shipment_id;
        $launched_by = 4;
        $name = $request->complaint_name;
        $phoneno = $request->complaint_phone;
        if(!CrmRequest::where('shipment_id', $shipment_id)->where('case_nature_id', $case_nature)->exists()){
            $data = new CrmRequest();
            $data->case_nature_id = $case_nature;
            $data->case_nature_type_id =$case_nature_type;
            $data->description = 'Consignee :('.$name.') | Phone Number : ('.$phoneno.') | Complain : '. $discription;
            $data->channel_id =$request_channel;
            $data->status_id = 1;
            $data->launched_by = $launched_by;
            $data->shipment_id = $shipment_id;

            $data->save();

            $crm_request_status_history = new CrmRequestStatusHistory();
            $crm_request_status_history->crm_request_id =$data->id;
            $crm_request_status_history->status_id = 1;
            $crm_request_status_history->save();

            $id = str_pad($data->id, 6, 0, STR_PAD_LEFT);

            return response()->json(['status' => 1, 'success' => 'Request ('. $id .') successfully added']);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Complain already laucnched against your shipment!']);
        }
    }
}