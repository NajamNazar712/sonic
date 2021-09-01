<?php

namespace App\Http\Controllers;

use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestChannel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\Shipment;
use App\Http\Models\ShipmentJourney;

use Auth;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class TrackingController extends Controller
{
    public function index() {
        $case_nature = CrmRequestCaseNature::get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
        $case_nature_channels = CrmRequestChannel::where('id', '!=', 1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->get();
        return view('tracking')->with(['case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_channels' => $case_nature_channels, 'case_nature_type_claims' => $case_nature_type_claims]);
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

}