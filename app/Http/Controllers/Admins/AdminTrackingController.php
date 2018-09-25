<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\Shipment;
use App\Http\Models\ShipmentJourney;
use App\Http\Models\ShipmentPaymentJourney;
use App\Http\Models\Rider;
use App\Http\Models\CargoConsignment;

use Auth;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class AdminTrackingController extends Controller
{
    public function __construct() {
      $this->middleware('auth:admin');
    }

    public function index(Request $request) {

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

                $details['tracking_number'] = $tracking_number;

    			$shipper = $shipment->user;

    			$details['shipper']['name'] = $shipper->name;
    			$details['shipper']['account_number'] = str_pad($shipper->id, 6, '0', STR_PAD_LEFT);
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

    			$details['order_information']['weight'] = ($shipment->actual_weight) ? floatval($shipment->actual_weight) : floatval($shipment->estimated_weight);
    			$details['order_information']['instructions'] = $shipment->special_instructions;

    			foreach ($shipment->shipment_journey as $journey) {
    				$journey_details = array();

    				$journey_details['date_time'] = Carbon::parse($journey->created_at)->toDateTimeString();
    				$journey_details['status'] = $journey->shipment_status_shipper->name;

    				if ($journey->reference_1_id) {
    					$journey_details['status'] .= ' (' . str_pad($journey->reference_1_id, 6, '0', STR_PAD_LEFT);

    					if ($journey->reference_2_id) {
                            if (in_array($journey->shipper_status_id, [5, 23])) {
                                $rider = Rider::find($journey->reference_2_id);

                                $journey_details['status'] .= ' | ' . $rider->name;
                            }
                            else {
                                $journey_details['status'] .= ' | ' . str_pad($journey->reference_2_id, 6, '0', STR_PAD_LEFT);
                            }
    					}
                        else if (in_array($journey->shipper_status_id, [3, 21, 26, 32])) {
                            $cargo_consignment = CargoConsignment::find($journey->reference_1_id);

                            if ($cargo_consignment->builty_number && !empty($cargo_consignment->builty_number)) {
                                $journey_details['status'] .= ' | ' . $cargo_consignment->builty_number;
                            }
                        }

    					$journey_details['status'] .= ')';
    				}

    				$journey_details['status_reason'] = ($journey->status_reason_id) ? $journey->shipment_status_reason->name : NULL;
    				$journey_details['remarks'] = ($journey->remarks) ? $journey->remarks : '';
    				$journey_details['user'] = ($journey->admin_id) ? $journey->admin->name : $journey->user->name;
                    $journey_details['city'] = ($journey->city_id) ? $journey->city->name : '';

    				$details['tracking_history'][] = $journey_details;
    			}

                $shipment_payment_journey = $shipment->shipment_payment_journey;

                if ($shipment_payment_journey) {
                    foreach ($shipment_payment_journey as $journey) {
                        $journey_details = array();

                        $journey_details['date_time'] = Carbon::parse($journey->created_at)->toDateTimeString();
                        $journey_details['status'] = $journey->status->name;
                        $journey_details['user'] = $journey->admin->name;

                        $details['payment_history'][] = $journey_details;
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