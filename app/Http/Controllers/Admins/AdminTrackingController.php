<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\Shipment;
use App\Http\Models\ShipmentJourney;

use Auth;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class AdminTrackingController extends Controller
{
    public function __construct() {
      $this->middleware('auth:admin');
    }

    public function index() {
      return view('admin.tracking');
    }

    public function track(Request $request) {
    	$tracking_numbers = explode(',', $request->tracking_numbers);

    	$tracking = array();

    	foreach ($tracking_numbers as $tracking_number) {
    		$shipment = Shipment::where('tracking_number', $tracking_number);

    		if ($shipment->exists()) {
    			$shipment = $shipment->first();

    			$details = array();

    			$shipper = $shipment->user;

    			$details['shipper']['name'] = $shipper->name;
    			$details['shipper']['account_number'] = $shipper->id;
    			$details['shipper']['phone_number_1'] = $shipper->phone;
    			$details['shipper']['phone_number_2'] = $shipper->phone2;
    			$details['shipper']['origin'] = $shipper->city->name;
    			$details['shipper']['address'] = $shipper->address;

    			$details['consignee']['name'] = $shipment->consignee_name;
    			$details['consignee']['phone_number_1'] = $shipment->consignee_phone_number_1;
    			$details['consignee']['phone_number_2'] = $shipment->consignee_phone_number_2;
    			$details['consignee']['destination'] = $shipment->consignee_city->name;
    			$details['consignee']['address'] = $shipment->consignee_address;

    			foreach ($shipment->items as $item) {
    				$item_details = array();

    				$item_details['product_type'] = $item->product->product_name;
    				$item_details['description'] = $item->description;
    				$item_details['quantity'] = $item->quantity;

    				$details['order_information']['items'][] = $item_details;
    			}

    			$details['order_information']['weight'] = ($shipment->actual_weight) ? $shipment->actual_weight : $shipment->estimated_weight;
    			$details['order_information']['instructions'] = $shipment->special_instructions;

    			foreach ($shipment->shipment_journey as $journey) {
    				$journey_details = array();

    				$journey_details['date_time'] = Carbon::parse($journey->created_at)->format('d/m/Y H:i A');
    				$journey_details['status'] = $journey->shipment_status_shipper->name;

    				if ($journey->reference_1_id) {
    					$journey_details['status'] .= ' (' . $journey->reference_1_id;

    					if ($journey->reference_2_id) {
    						$journey_details['status'] .= ' | ' . $journey->reference_2_id;
    					}

    					$journey_details['status'] .= ')';
    				}

    				$journey_details['status_reason'] = ($journey->status_reason_id) ? $journey->shipment_status_reason->name : NULL;
    				$journey_details['remarks'] = $journey->remarks;
    				$journey_details['user'] = ($journey->admin_id) ? $journey->admin->name : $journey->user->name;

    				$details['tracking_history'][] = $journey_details;
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