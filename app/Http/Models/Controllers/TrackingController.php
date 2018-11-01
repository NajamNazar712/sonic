<?php

namespace App\Http\Controllers;

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
      return view('tracking');
    }

    public function track(Request $request) {
    	$tracking_numbers = explode(',', $request->tracking_numbers);

    	$tracking = array();

    	foreach ($tracking_numbers as $tracking_number) {
    		$shipment = Shipment::where('tracking_number', $tracking_number);

    		if ($shipment->exists()) {
                $shipment = $shipment->first();

    			$details = array();

                $details['tracking_number'] = $tracking_number;

    			$shipper = $shipment->user;

    			$details['shipper']['name'] = $shipper->name;
    			$details['shipper']['origin'] = $shipper->city->name;

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

    	return $tracking;
    }

}