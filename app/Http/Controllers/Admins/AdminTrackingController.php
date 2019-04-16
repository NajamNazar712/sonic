<?php

namespace App\Http\Controllers\Admins;
use App\Http\Models\ShipmentsJourney;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Shipment;
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
    			$details['shipper']['email'] = $shipper->email;
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

                $details['order_information']['order_id'] = $shipment->order_id;
    			$details['order_information']['weight'] = ($shipment->actual_weight) ? floatval($shipment->actual_weight) : floatval($shipment->estimated_weight);
                $details['order_information']['shipping_mode'] = $shipment->shipping_mode->mode;

                $details['order_information']['booking_type_id'] = $shipment->booking_type_id;

                if ($shipment->booking_type_id != 4) {
                    $details['order_information']['amount'] =  number_format($shipment->amount);
                }
                else {
                    if ($shipment->charges_mode_id == 1) {
                        $details['order_information']['amount'] = 0;
                    }
                    else {
                        $details['order_information']['amount'] = number_format($shipment->amount);
                    }
                }

                $details['order_information']['account_type_id'] = $shipment->user->account_type_id;

                if ($shipment->user->account_type_id == 2 || $shipment->booking_type_id == 4) {
                    $details['order_information']['charges_mode'] = $shipment->charges_mode->charges_mode;
                }

    			$details['order_information']['instructions'] = $shipment->special_instructions;

    			foreach ($shipment->shipment_journey as $journey) {
    				$journey_details = array();

    				$journey_details['date_time'] = Carbon::parse($journey->created_at)->toDateTimeString();
    				$journey_details['status'] = $journey->shipment_status_shipper->name;

    				if ($journey->reference_1_id) {
                        if (in_array($journey->shipper_status_id, [3, 21, 26, 32])) {
                            $journey_details['status'] .= ' (<button class="btn btn-sm btn-outline-info align-middle cargo_consignment_details" data-id="' . $journey->reference_1_id . '">' . str_pad($journey->reference_1_id, 6, '0', STR_PAD_LEFT) . '</button>';
                        }
                        else {
                            $journey_details['status'] .= ' (' . str_pad($journey->reference_1_id, 6, '0', STR_PAD_LEFT);

                            if ($journey->reference_2_id) {
                                if (in_array($journey->shipper_status_id, [5, 23, 28, 34])) {
                                    $rider = Rider::find($journey->reference_2_id);

                                    $journey_details['status'] .= ' | <button class="btn btn-sm btn-outline-info align-middle rider_information" data-id="' . $rider->id . '">' . $rider->name . '</button>';
                                }
                                else {
                                    $journey_details['status'] .= ' | ' . str_pad($journey->reference_2_id, 6, '0', STR_PAD_LEFT);
                                }
                            }
                        }

    					$journey_details['status'] .= ')';
    				}

    				$journey_details['status_reason'] = ($journey->status_reason_id) ? $journey->shipment_status_reason->name : NULL;
    				$journey_details['remarks'] = ($journey->remarks) ? $journey->remarks : '';
    				$journey_details['user'] = ($journey->admin_id) ? $journey->admin->name : $journey->user->name;
                    $journey_details['city'] = ($journey->city_id) ? $journey->city->name : '';
                    $journey_details['received_or_refused_by'] = ($journey->received_or_refused_by) ? $journey->received_or_refused_by : '';
                    $journey_details['ip'] = ($journey->ip_address) ? $journey->ip_address : '';

    				$details['tracking_history'][] = $journey_details;
    			}

                $shipment_payment_journey = $shipment->shipment_payment_journey;

                if ($shipment_payment_journey) {
                    foreach ($shipment_payment_journey as $journey) {
                        $journey_details = array();

                        $journey_details['date_time'] = Carbon::parse($journey->created_at)->toDateTimeString();
                        $journey_details['status'] = $journey->status->name;
                        $journey_details['user'] = $journey->admin->name;
                        $journey_details['payable_remarks'] = ($journey->payable_remarks) ? $journey->payable_remarks : '';

                        $details['payment_history'][] = $journey_details;
                    }
                }

                $shipment_pickup_journey = $shipment->shipment_pickup_journey;

                if ($shipment_pickup_journey) {
                    foreach ($shipment_pickup_journey as $journey) {
                        $journey_details = array();

                        $journey_details['date_time'] = Carbon::parse($journey->created_at)->toDateTimeString();
                        $journey_details['status'] = $journey->status->name;

                        if ($journey->reference_1_id) {
                            $journey_details['status'] .= ' (' . str_pad($journey->reference_1_id, 6, '0', STR_PAD_LEFT);

                            if ($journey->reference_2_id) {
                                if ($journey->status_id == 2) {
                                    $rider = Rider::find($journey->reference_2_id);

                                    $journey_details['status'] .= ' | <button class="btn btn-sm btn-outline-info align-middle rider_information" data-id="' . $rider->id . '">' . $rider->name . '</button>';
                                }
                                else {
                                    $journey_details['status'] .= ' | ' . str_pad($journey->reference_2_id, 6, '0', STR_PAD_LEFT);
                                }
                            }

                            $journey_details['status'] .= ')';
                        }

                        $admin = $journey->admin;

                        if ($admin) {
                            $journey_details['user'] = $admin->name;
                        }
                        else {
                            $journey_details['user'] = '';
                        }

                        $details['pickup_history'][] = $journey_details;
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

    public function rider_information(Request $request) {
        $rider = Rider::find($request->id);

        $information = array();

        $information['name'] = $rider->name;
        $information['phone_number'] = $rider->phone;
        $information['city'] = $rider->city->name;
        $information['category'] = $rider->rider_category->name;
        $information['route'] = $rider->route->code . ' (' . $rider->route->start . ' to ' . $rider->route->end . ')';

        return $information;
    }

    public function cargo_consignment_details(Request $request) {
        $cargo_consignment = CargoConsignment::find($request->id);

        $details = array();

        $details['junction_hub_1'] = $cargo_consignment->junction_hub_1->name;
        $details['junction_hub_2'] = ($cargo_consignment->junction_hub_2_id) ? $cargo_consignment->junction_hub_2->name : '';
        $details['expected_arrival_date'] = Carbon::parse($cargo_consignment->expected_arrival_date)->format('d/m/Y');
        $details['shipping_mode'] = $cargo_consignment->shipping_mode->mode;
        $details['transport_mode'] = $cargo_consignment->transport_mode->name;
        $details['transport_mode_vendor'] = $cargo_consignment->transport_mode_vendor->name;
        $details['seal_number'] = $cargo_consignment->seal_number;
        $details['builty_number'] = $cargo_consignment->builty_number;
        $details['shipments_weight'] = $cargo_consignment->shipments_weight;
        $details['actual_weight'] = $cargo_consignment->actual_weight;
        $details['vendor_weight'] = $cargo_consignment->vendor_weight;
        $details['weight_charges_per_kg'] = number_format($cargo_consignment->weight_charges_per_kg);
        $details['extra_charges'] = number_format($cargo_consignment->extra_charges);
        $details['total_weight_charges'] = number_format($cargo_consignment->total_weight_charges);
        $details['sender_name'] = Admin::find($cargo_consignment->sender_id)->name;
        $details['receiver_name'] = ($cargo_consignment->receiver_id) ? Admin::find($cargo_consignment->receiver_id)->name : '';

        return $details;
    }

    public function quick_tracking_index(){
        return view('admin.tracking.quick_tracking');
    }

    public function quick_tracking_shipment_info(Request $request){
        $tracking_no = $request->tracking;
        if($tracking_no != null){
            $shipment = Shipment::where('tracking_number', $tracking_no);
            if ($shipment->exists()) {
                $shipment = $shipment->first();

                $details = array();

                $details['tracking_number'] = $tracking_no;
                $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->latest()->first();
                $details['status'] = $journey->shipment_status_shipper->name;
                if($journey->status_reason_id != null){

                    $details['reason'] = $journey->shipment_status_reason->name;
                }else{
                    $details['reason'] = null;
                }
                $details['remarks'] = $journey->remarks;
                $details['status_id'] = $journey->shipper_status_id;
                $details['current_status_date'] = Carbon::parse($journey->created_at)->toDateTimeString();
                $details['origin'] = $shipment->pickup_address->city->name;
                $details['destination'] = $shipment->consignee_city->name;
                return response()->json(['status' => 1, 'details' => $details]);
            }
            else{
                return response()->json(['status' => 0, 'error' => 'Tracking Number not found!']);
            }
        }
    }
}