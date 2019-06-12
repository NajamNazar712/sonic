<?php

namespace App\Http\Controllers\Admins;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\User;
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
                $details['shipper']['city'] = $shipper->city->name;
    			$details['shipper']['phone_number_1'] = $shipper->phone;
    			$details['shipper']['phone_number_2'] = $shipper->phone2;
    			$details['shipper']['email'] = $shipper->email;

                $pickup = $shipment->pickup_address;

                $details['pickup']['point_of_contact'] = $pickup->poc;
                $details['pickup']['phone_number'] = $pickup->phone;
                $details['pickup']['email'] = $pickup->email;
                $details['pickup']['origin'] = $pickup->city->name;
                $details['pickup']['address'] = $pickup->pickup_address;

    			$details['consignee']['name'] = $shipment->consignee_name;
    			$details['consignee']['phone_number_1'] = $shipment->consignee_phone_number_1;
    			$details['consignee']['phone_number_2'] = $shipment->consignee_phone_number_2;
    			$details['consignee']['destination'] = $shipment->consignee_city->name;
    			$details['consignee']['address'] = $shipment->consignee_address;
                $details['consignee']['email'] = $shipment->consignee_email;

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

                $details['order_information']['charges_mode_id'] = $shipment->charges_mode_id;

                if ($shipment->charges_mode_id) {
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

                $shipment_amount_log = $shipment->amount_change_log;

                if ($shipment_amount_log) {
                    foreach ($shipment_amount_log as $journey) {
                        $journey_details = array();

                        $journey_details['date_time'] = Carbon::parse($journey->created_at)->toDateTimeString();
                        $journey_details['old_amount'] = number_format($journey->old_amount);
                        $journey_details['new_amount'] = number_format($journey->new_amount);
                        $journey_details['user'] = $journey->admin->name;

                        $details['amount_history'][] = $journey_details;
                    }
                }

                $shipment_weight_log = $shipment->weight_change_log;

                if ($shipment_weight_log) {
                    foreach ($shipment_weight_log as $journey) {
                        $journey_details = array();

                        $journey_details['date_time'] = Carbon::parse($journey->created_at)->toDateTimeString();
                        $journey_details['old_weight'] = number_format($journey->old_weight);
                        $journey_details['new_weight'] = number_format($journey->new_weight);
                        $journey_details['user'] = $journey->admin->name;

                        $details['weight_history'][] = $journey_details;
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
                $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->latest('id')->first();
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

    public function cx_quick_tracking_index(){
        $shippers = User::select('id', 'name')->get();
        return view('admin.tracking.cx_quick_tracking')->with('shippers', $shippers);
    }
    public function cx_quick_tracking_list(Request $request){
        $quick_tracking = Shipment::leftjoin('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities as dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->select('shipments.tracking_number as tracking_number', 'shipments.order_id', 'oc.name as origin', 'dc.name as destination', 'shipments.consignee_address as address', 'shipments.amount as cod_amount', 'ss.name as status', 'u.name as shipper_name', 'shipments.consignee_name as consignee_name', 'shipments.consignee_phone_number_1 as consignee_phone_no');
        $datatable = Datatables::of($quick_tracking)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            });
        if($tracking = $request->get('search_tracking')){
            $datatable->where('shipments.tracking_number', 'LIKE', '%'. $tracking . '%');
        }
        if($shipper = $request->get('search_shipper')){
            $datatable->where('u.id', 'LIKE', '%'. $shipper . '%');
        }
        if($phone_no = $request->get('search_phone_no')){
            $datatable->where('shipments.consignee_phone_number_1', 'LIKE', '%'. $phone_no . '%');
        }
        if($order_id = $request->get('search_order_id')){
            $datatable->where('shipments.order_id', 'LIKE', '%'. $order_id . '%');
        }
            return $datatable->make(true);
    }
}