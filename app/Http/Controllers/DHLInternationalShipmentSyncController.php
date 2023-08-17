<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\Admins\MasterCargoBagJourneyController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\Admins\V2Pickup\V2AdminPickupsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\MasterCargo\Bag;
use App\Http\Models\Admin\MasterCargo\BagShipment;
use App\Http\Models\Admin\MasterCargo\MasterCargo;
use App\Http\Models\Admin\MasterCargo\MasterCargoBag;
use App\Http\Models\City;
use App\Http\Models\InternationalShipment;
use App\Http\Models\ReceivingSheetReceived;
use App\Http\Models\Rider;
use App\Http\Models\SelfCollectionShipment;
use App\Http\Models\Shipment;
use App\Http\Models\V2Pickup\V2PickupNote;
use App\Http\Models\V2Pickup\V2PickupNoteRequest;
use App\Http\Models\V2Pickup\V2PickupReceivedShipment;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestAttempt;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
use App\Http\Models\Zone;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DHLInternationalShipmentSyncController extends Controller
{
    protected $rider_id = 3003;
    protected $admin_id = 184;
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');

        $settings = GlobalSettings::where('type', 'dhl_user_id');
        if ($settings->exists()) {
            $settings = $settings->first();
            $this->admin_id = $settings->setting_value;
        }

    }


    static public function dhl_tracking(){

        $dhl_api_key = 'EXYgAAqX3c5TYz3GqVLCS6e1fS57AAYO';
        $dhl_url = 'https://api-eu.dhl.com/track/';


        $arrival_status = ['Shipment picked up', 'Shipment Accepted'];
        $transit_status = ['pre-transit','transit'];
        $delivered_status = ['delivered'];
        $undelivered_status = ['Shipment on hold', 'Delivery attempted; recipient not home', 'Recipient refused delivery'];
        $returned_status = ['exception','failure', 'unknown'];

        $international_tracking_numbers = InternationalShipment::whereNotNull('international_tracking_number')->where('sync', 1)->pluck('international_tracking_number')->toArray();

        if(count($international_tracking_numbers) > 0){


            $chunk_international_shipments = array();

            $chunk_international_shipments = array_chunk($international_tracking_numbers, 3);

            foreach ($chunk_international_shipments as $chunk_international_shipment){
                $international_tracking_numbers = implode(',',$chunk_international_shipment);

                $client = new Client(['base_uri' => $dhl_url, 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
                try{
                    $response = $client->get('shipments', [
                        'headers' => [
                            'DHL-API-Key' => $dhl_api_key,
                        ],
                        'query' => [
                            'trackingNumber' => $international_tracking_numbers
                        ]
                    ]);
                    $status_code = $response->getStatusCode();

                    if($status_code == 200){
                        $response = $response->getBody()->getContents();
                        $response = json_decode($response);
                        $international_shipments = $response->shipments;
                        if(count($international_shipments) > 0){
                            foreach ($international_shipments as $international_shipment){
                                $intl_shipment = InternationalShipment::where('international_tracking_number', $international_shipment->id)->where('sync', 1)->first();
                                if($intl_shipment){
                                    $shipment = $intl_shipment->shipment;
                                    $shipper_status_id = $shipment->shipper_status_id;
                                    $international_shipment_status = NULL;
                                    $international_shipment_description = NULL;
                                    if(isset($international_shipment->status->statusCode)){
                                        $international_shipment_status = $international_shipment->status->statusCode;
                                    }
                                    if(isset($international_shipment->status->description)){
                                        $international_shipment_description = $international_shipment->status->description;
                                    }
                                    if(in_array($international_shipment_description, $arrival_status)){
                                        if($shipper_status_id == 1){
                                            (new self)->shipment_picked($shipment->id);
                                        }
                                    }
                                    else if(in_array($international_shipment_status, $transit_status)){
                                        if($shipper_status_id == 1){
                                            (new self)->shipment_picked($shipment->id);
                                            (new self)->shipment_intransit($shipment->id);
                                        }
                                        else if($shipper_status_id == 2){
                                            (new self)->shipment_intransit($shipment->id);
                                        }
                                    }
                                    else if(in_array($international_shipment_status, $delivered_status)){
                                        if($shipper_status_id == 1){
                                            (new self)->shipment_picked($shipment->id);
                                            (new self)->shipment_intransit($shipment->id);
                                            (new self)->shipment_arrived($shipment->id);
                                            (new self)->shipment_delivered($shipment->id);
                                        }
                                        else if($shipper_status_id == 2){
                                            (new self)->shipment_intransit($shipment->id);
                                            (new self)->shipment_arrived($shipment->id);
                                            (new self)->shipment_delivered($shipment->id);
                                        }
                                        else if($shipper_status_id == 4){
//                                            (new self)->shipment_arrived($shipment->id);
                                            (new self)->shipment_delivered($shipment->id);
                                        }
                                        else if(in_array($shipper_status_id, [8, 9])){
                                            (new self)->shipment_delivered($shipment->id);
                                        }

                                    }
                                    else if(in_array($international_shipment_description, $undelivered_status)){
                                        $shipment_status = NULL;
                                        $shipment_reason = NULL;
                                        foreach ($undelivered_status as $index => $status){
                                            if($index == 1){
                                                $shipment_status = 9;
                                            }
                                            else if($index == 2){
                                                $shipment_status = 8;
                                                $shipment_reason = 8;
                                            }
                                            else{
                                                $shipment_status = 8;
                                                $shipment_reason = 1;
                                            }
                                        }
                                        if($shipper_status_id == 1){
                                            (new self)->shipment_picked($shipment->id);
                                            (new self)->shipment_intransit($shipment->id);
                                            (new self)->shipment_arrived($shipment->id);
                                            (new self)->shipment_undelivered($shipment->id, $shipment_status, $shipment_reason);
                                        }
                                        else if($shipper_status_id == 2){
                                            (new self)->shipment_intransit($shipment->id);
                                            (new self)->shipment_arrived($shipment->id);
                                            (new self)->shipment_undelivered($shipment->id, $shipment_status, $shipment_reason);
                                        }
                                        else if($shipper_status_id == 4){
//                                            (new self)->shipment_arrived($shipment->id);
                                            (new self)->shipment_undelivered($shipment->id, $shipment_status, $shipment_reason);
                                        }
                                    }
                                    else if(in_array($international_shipment_status, $returned_status)){
                                        if($shipper_status_id == 1){
                                            (new self)->shipment_picked($shipment->id);
                                            (new self)->shipment_intransit($shipment->id);
                                            (new self)->shipment_arrived($shipment->id);
                                            (new self)->shipment_returned($shipment->id);
                                        }
                                        else if($shipper_status_id == 2){
                                            (new self)->shipment_intransit($shipment->id);
                                            (new self)->shipment_arrived($shipment->id);
                                            (new self)->shipment_returned($shipment->id);
                                        }
                                        else if($shipper_status_id == 4){
//                                            (new self)->shipment_arrived($shipment->id);
                                            (new self)->shipment_returned($shipment->id);
                                        }
                                        else if(in_array($shipper_status_id, [8, 9])){
                                            (new self)->shipment_returned($shipment->id);
                                        }

                                    }

                                }
                            }
                        }
                    }

                }catch (RequestException $exception){
                    Log::info($exception);
                }

            }

        }
    }
    //184 admin
    //3003 rider
    public function shipment_picked($shipment_id){
        $shipment = Shipment::find($shipment_id);
        $shipment->shipper_status_id = 2;
        $shipment->consignee_status_id = 2;
        $shipment->save();
        ShipmentsJourneyController::add($shipment_id, 2, 2, NULL, NULL, NULL, $this->admin_id);
    }
    public function shipment_intransit($shipment_id){
        $shipment = Shipment::find($shipment_id);
        $shipment->shipper_status_id = 3;
        $shipment->consignee_status_id = 3;
        $shipment->save();
        ShipmentsJourneyController::add($shipment_id, 3, 3, NULL, NULL, NULL, $this->admin_id);
    }
    public function shipment_arrived($shipment_id){
        $shipment = Shipment::find($shipment_id);
        $shipment->shipper_status_id = 4;
        $shipment->consignee_status_id = 4;
        $shipment->save();
        ShipmentsJourneyController::add($shipment_id, 4, 4, NULL, NULL, NULL, $this->admin_id);
    }
    public function shipment_delivered($shipment_id){
        $shipment = Shipment::find($shipment_id);
        $shipment->shipper_status_id = 14;
        $shipment->consignee_status_id = 14;
        $shipment->save();
        AdminFinanceController::add_payment($shipment_id, 0);
        ShipmentsJourneyController::add($shipment_id, 5, 5, NULL, NULL, NULL, $this->admin_id);
        ShipmentsJourneyController::add($shipment_id, 14, 14, NULL, NULL, NULL, $this->admin_id);

        $international_shipment = InternationalShipment::where('shipment_id', $shipment_id)->where('sync', 1);
        if($international_shipment->exists()){
            $international_shipment = $international_shipment->first();
            $international_shipment->sync = 0;
            $international_shipment->save();
        }
    }
    public function shipment_undelivered($shipment_id, $shipment_status, $shipment_reason){
        $shipment = Shipment::find($shipment_id);
        $shipment->shipper_status_id = $shipment_status;
        $shipment->consignee_status_id = $shipment_status;
        $shipment->save();
        ShipmentsJourneyController::add($shipment_id, 5, 5, NULL, NULL, NULL, $this->admin_id);
        ShipmentsJourneyController::add($shipment_id, $shipment_status, $shipment_reason, NULL, NULL, NULL, $this->admin_id);
    }
    public function shipment_returned($shipment_id){
        $shipment = Shipment::find($shipment_id);
        $shipment->shipper_status_id = 22;
        $shipment->consignee_status_id = 22;
        $shipment->save();
        AdminFinanceController::add_payment($shipment_id, 1);
        ShipmentsJourneyController::add($shipment_id, 5, 5, NULL, NULL, NULL, $this->admin_id);
        ShipmentsJourneyController::add($shipment_id, 12, 12, NULL, NULL, NULL, $this->admin_id);
        ShipmentsJourneyController::add($shipment_id, 20, 20, NULL, NULL, NULL, $this->admin_id);
        ShipmentsJourneyController::add($shipment_id, 22, 22, NULL, NULL, NULL, $this->admin_id);

        $international_shipment = InternationalShipment::where('shipment_id', $shipment_id)->where('sync', 1);
        if($international_shipment->exists()){
            $international_shipment = $international_shipment->first();
            $international_shipment->sync = 0;
            $international_shipment->save();
        }
    }


    public function shipment_arrived_old($shipment_id){
        $rider = Rider::find(3003);
        $pickup_request_id = NULL;
        $shipment = Shipment::find($shipment_id);
        if($shipment->shipper_status_id == 1){

            $global_rider_id = 3003;
            /*$settings = GlobalSettings::where('type', 'global_rider_id')->first();

            if ($settings) {
                $global_rider_id = $settings->setting_value;
            } else {
                $global_rider_id = 0;
            }*/
            $reference_1_id = NULL;
            $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id)->where('status', 0);
            if(!$pickup_request_shipment->exists()){
                AdminPickupsController::generate($shipment->id);
            }


            if ($receiving_sheet_shipment = $shipment->receiving_sheet_shipment) {
                $receiving_sheet_shipment->status = 1;
                $receiving_sheet_shipment->save();

                $receiving_sheet_id = $receiving_sheet_shipment->receiving_sheet_id;

                $receiving_sheet = $receiving_sheet_shipment->receiving_sheet;

                $receiving_sheet->received = $receiving_sheet->received + 1;

                $receiving_sheet->save();

                if (!ReceivingSheetReceived::where('shipment_id', $shipment_id)->exists()) {
                    $receiving_sheet_received = new ReceivingSheetReceived();

                    $receiving_sheet_received->receiving_sheet_id = $receiving_sheet_id;
                    $receiving_sheet_received->user_id = $shipment->user_id;
                    $receiving_sheet_received->pickup_address_id = $shipment->pickup_address_id;
                    $receiving_sheet_received->shipment_id = $shipment_id;

                    $receiving_sheet_received->save();
                }
            }
            else {
                if (!ReceivingSheetReceived::where('shipment_id', $shipment_id)->exists()) {
                    $receiving_sheet_received = new ReceivingSheetReceived();

                    $receiving_sheet_received->user_id = $shipment->user_id;
                    $receiving_sheet_received->pickup_address_id = $shipment->pickup_address_id;
                    $receiving_sheet_received->shipment_id = $shipment_id;

                    $receiving_sheet_received->save();
                }
            }

            $shipment->shipper_status_id = 2;
            $shipment->consignee_status_id = 2;

            $shipment->save();
            if($shipment->packaging_material_request == 0 && $shipment->shipment_type == 1){

                ShipmentChargesController::weight($shipment_id);
                if($shipment->business_category_id == 1) {
                    ShipmentChargesController::cash_handling($shipment_id);
                    ShipmentChargesController::insurance($shipment_id);
                    ShipmentChargesController::fuel_surcharge($shipment_id);
                }
                else{
                    ShipmentChargesController::international_fuel_surcharge($shipment_id);
                }

            }

            if ($shipment->charges_mode_id == 2 && $shipment->booking_type_id != 4) {
                $shipment = Shipment::find($shipment_id);

                $charges = $shipment->weight_charges + $shipment->cash_handling_charges + $shipment->insurance_charges + $shipment->fuel_surcharge;

                $gst = Zone::find($shipment->pickup_address->city->zone_id)->gst;

                $gst = ROUND(($charges * $gst), 0, PHP_ROUND_HALF_DOWN);

                $shipment->amount = $shipment->amount + $charges + $gst;

                $shipment->save();

            }

            if(($shipment->charges_mode_id == 2 || $shipment->charges_mode_id == 1) && $shipment->booking_type_id == 4){
                $shipment_ids = array($shipment->id);
                NotificationsController::send(85, $shipment_ids , 184);
            }

            $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id)->where('pickup_request_id', $pickup_request_id);

            if ($pickup_request_shipment->exists()) {
                $pickup_request_shipment = $pickup_request_shipment->first();
                $pickup_request_shipment->status = 1;
                $pickup_request_shipment->save();
                $pickup_request_received_shipment = new V2PickupReceivedShipment();
                $pickup_request_received_shipment->pickup_request_id = $pickup_request_id;
                $pickup_request_received_shipment->shipment_id = $shipment->id;
                $pickup_request_received_shipment->save();
                $pickup_request = $pickup_request_shipment->pickup_request;

                if($pickup_request->current_rider_id == NULL){
                    $pickup_note_id = $this->generate_assigned_pickup($pickup_request->id, $global_rider_id);
                }
                else{
                    $pickup_request->received = $pickup_request->received + 1;
                    $pickup_request->status_id = 2;
                    $pickup_request->save();
                }

                $pickup_note_request = V2PickupNoteRequest::where('pickup_request_id', $pickup_request_id)->where('status', 0)->first();
                if($pickup_note_request){
                    $pickup_note_id = $pickup_note_request->pickup_note_id;
                    $pickup_note_request->status = 1;
                    $pickup_note_request->save();
                    $pickup_note_requests_count = V2PickupNoteRequest::where('pickup_note_id', $pickup_note_id)->where('status', 0)->count();
                    $v2_pickup_note = V2PickupNote::where('id', $pickup_note_id)->first();
                    if($pickup_note_requests_count == 0){
                        $v2_pickup_note->status = 1;
                    }

                    $pickup_note_pickup_request_ids = V2PickupNoteRequest::where('pickup_note_id', $pickup_note_id)->pluck('pickup_request_id')->toArray();
                    if(count($pickup_note_pickup_request_ids) > 0){
                        $arrived_shipments = V2PickupRequest::whereIn('id', $pickup_note_pickup_request_ids)->sum('received');
                        $v2_pickup_note->arrived_shipments = $arrived_shipments;
                    }
                    $v2_pickup_note->save();
                }

                ShipmentsPickupJourneyController::add($shipment_id, 2, 184, $pickup_request->id);

            }
        }
    }
    public function shipment_intransit_old($shipment_id){
        $shipment = Shipment::find($shipment_id);
        if($shipment->shipper_status_id == 2){
            $admin = Admin::find(184);
            $origin_hub_id = $shipment->pickup_address->city->hub_id;
            $destination_hub_id = $shipment->consignee_city->hub_id;
            if($origin_hub_id != $destination_hub_id){

                $shipments = 1;
                $quantity = 1;
                $shipments_weight = $shipment->actual_weight;
                $bag = new Bag();

                $bag->origin_hub_id = $origin_hub_id;
                $bag->destination_hub_id = $destination_hub_id;
                $bag->junction_hub_1_id = $origin_hub_id;
                $bag->junction_hub_2_id = NULL;
                $bag->seal_number = $shipment->tracking_number;
                $bag->shipping_mode_id = 1;
                $bag->transport_mode_id = 2;
                $bag->transport_mode_vendor_id = 9;
                $bag->shipments = $shipments;
                $bag->quantity = $quantity;
                $bag->shipments_weight = $shipments_weight;
                $bag->actual_weight = $shipments_weight;
                $bag->created_by = $admin->id;
                $bag->type = 1;

                $bag->status_id = 9;

                $bag->save();

                $bag_id = $bag->id;

                MasterCargoBagJourneyController::add($bag_id, $bag->seal_number, 1, $admin->id, NULL, NULL);
                MasterCargoBagJourneyController::add($bag_id, $bag->seal_number, 2, $admin->id, NULL, NULL);
                MasterCargoBagJourneyController::add($bag_id, $bag->seal_number, 9, $admin->id, NULL, NULL);


                $bag_shipment = new BagShipment();

                $bag_shipment->bag_id = $bag_id;
                $bag_shipment->shipment_id = $shipment_id;

                $bag_shipment->save();

                $shipper_status_id = NULL;
                $consignee_status_id = NULL;

                $shipment->shipper_status_id = $shipper_status_id;
                $shipment->consignee_status_id = $consignee_status_id;

                $shipment->save();

                ShipmentsJourneyController::add($shipment_id, 3, 3, NULL, NULL, NULL, 184, $bag->id, $bag->builty_number);



                $bags = 1;
                $shipments = 1;
                $quantity = 1;
                $bags_weight = $shipments_weight;
                $master_cargo_status_id = 3;

                $master_cargo = new MasterCargo();

                $master_cargo->origin_hub_id = $origin_hub_id;
                $master_cargo->destination_hub_id = $destination_hub_id;
                $master_cargo->junction_hub_1_id = $origin_hub_id;
                $master_cargo->junction_hub_2_id = NULL;
                $master_cargo->shipping_mode_id = 1;
                $master_cargo->transport_mode_id = 2;
                $master_cargo->driver_name = $admin->name;
                $master_cargo->vehicle = 'DHL Plane';
                $master_cargo->phone_number = '0000-0000000';
                $master_cargo->bags = $bags;
                $master_cargo->shipments = $shipments;
                $master_cargo->quantity = $quantity;
                $master_cargo->bags_weight = $bags_weight;
                $master_cargo->actual_weight = $shipments_weight;
                $master_cargo->created_by = 184;
                $master_cargo->status_id = $master_cargo_status_id;
                $master_cargo->save();

                $master_cargo_id = $master_cargo->id;
                $mater_cargo_bags= new MasterCargoBag();

                $mater_cargo_bags->master_cargo_id = $master_cargo_id;
                $mater_cargo_bags->bag_id = $bag_id;

                $mater_cargo_bags->save();

                MasterCargoBagJourneyController::add($bag_id, $bag->seal_number, $bag->status_id, 184, $master_cargo_id, $master_cargo_status_id);

            }
        }
    }
    public function shipment_delivered_old($shipment_id){
        ShipmentsJourneyController::add($shipment_id, 4, 4, NULL, NULL, NULL, 184);
//        $settings = GlobalSettings::where('type', 'nsa_accounts')->first();
        $rider_id = 3003;

        $shipment = Shipment::find($shipment_id);

        $hub_id = City::find($shipment->consignee_city_id)->hub_id;
        $note = DeliveryNote::create([
            'hub_id' => $hub_id,
            'rider_id' => $rider_id,
            'route_id' => 2,
            'shipments_count' => 1,
            'admin_id' => 184,
            'total_cod_amount' => $shipment->amount,
            'password' => NULL,
            'last_updated_at' => Carbon::now(),
            'special_rider' => 0,
            'order' => FALSE
        ]);

        if($note){
            DeliveryNoteShipment::create([
                'delivery_note_id' => $note->id,
                'shipment_id' => $shipment_id,
                'notification' => 0,
                'rider_information' => 0,
                'ordering' => 1
            ]);

            $shipment->shipper_status_id = 14;
            $shipment->consignee_status_id = 14;
            $shipment->save();

            AdminFinanceController::done_payment($shipment_id, 0);
            ShipmentsJourneyController::add($shipment, 5, 5, NULL, NULL, NULL, 184, $note->id, $rider_id);
            ShipmentsJourneyController::add($shipment, 14, 14, NULL, NULL, NULL, 184, $note->id, NULL, 1);
            DeliveryNoteShipment::where(['delivery_note_id' => $note->id, 'shipment_id' => $shipment_id])->update(['status' => 6]);
            $date = Carbon::now();
            DeliveryNote::where('id', $note->id)->update(['delivered_shipments' => 1, 'verified_by' => 184, 'received_cod_amount' => $shipment->amount, 'status' => 1, 'last_updated_at' => $date, 'status_verified_at' => $date, 'pending_for_verification_at' => $date]);

            $international_shipment = InternationalShipment::where('shipment_id', $shipment_id)->first();
            if($international_shipment){
                $international_shipment->sync = 0;
                $international_shipment->save();
            }
        }

    }

    public function shipment_undelivered_old($shipment_id){
        ShipmentsJourneyController::add($shipment_id, 4, 4, NULL, NULL, NULL, 184);

//        $settings = GlobalSettings::where('type', 'nsa_accounts')->first();
        $rider_id = 3003;

        $shipment = Shipment::find($shipment_id);

        $hub_id = City::find($shipment->consignee_city_id)->hub_id;
        $note = DeliveryNote::create([
            'hub_id' => $hub_id,
            'rider_id' => $rider_id,
            'route_id' => 2,
            'shipments_count' => 1,
            'admin_id' => 184,
            'total_cod_amount' => $shipment->amount,
            'password' => NULL,
            'last_updated_at' => Carbon::now(),
            'special_rider' => 0,
            'order' => FALSE
        ]);

        if($note){
            DeliveryNoteShipment::create([
                'delivery_note_id' => $note->id,
                'shipment_id' => $shipment_id,
                'notification' => 0,
                'rider_information' => 0,
                'ordering' => 1
            ]);

            $shipment->shipper_status_id = 9;
            $shipment->consignee_status_id = 9;
            $shipment->save();

            AdminFinanceController::done_payment($shipment_id, 0);
            ShipmentsJourneyController::add($shipment, 5, 5, NULL, NULL, NULL, 184, $note->id, $rider_id);
            ShipmentsJourneyController::add($shipment, 9, 9, NULL, NULL, NULL, 184, $note->id, NULL, 1);
            DeliveryNoteShipment::where(['delivery_note_id' => $note->id, 'shipment_id' => $shipment_id])->update(['status' => 6]);
            $date = Carbon::now();
            DeliveryNote::where('id', $note->id)->update(['verified_by' => 184, 'status' => 1, 'last_updated_at' => $date, 'status_verified_at' => $date, 'pending_for_verification_at' => $date]);

        }

    }
    public function shipment_return_old($shipment_id){

//        $settings = GlobalSettings::where('type', 'nsa_accounts')->first();
        $rider_id = 3003;

        $shipment = Shipment::find($shipment_id);
        $hub_id = City::find($shipment->consignee_city_id)->hub_id;
        $note = DeliveryNote::create([
            'hub_id' => $hub_id,
            'rider_id' => $rider_id,
            'route_id' => 2,
            'shipments_count' => 1,
            'admin_id' => 184,
            'total_cod_amount' => $shipment->amount,
            'password' => NULL,
            'last_updated_at' => Carbon::now(),
            'special_rider' => 0,
            'order' => FALSE
        ]);

        if($note){
            DeliveryNoteShipment::create([
                'delivery_note_id' => $note->id,
                'shipment_id' => $shipment_id,
                'notification' => 0,
                'rider_information' => 0,
                'ordering' => 1
            ]);

            $shipment->shipper_status_id = 20;
            $shipment->consignee_status_id = 20;
            $shipment->save();


            ShipmentsJourneyController::add($shipment_id, 5, 5, NULL, NULL, NULL, 184, $note->id, $rider_id);
            ShipmentsJourneyController::add($shipment_id, 12, 12, 34, NULL, NULL, 184, $note->id, NULL, 0);
            ShipmentsJourneyController::add($shipment_id, 20, 20, 34, NULL, NULL, 184, $note->id, NULL, 1);

            DeliveryNoteShipment::where(['delivery_note_id' => $note->id, 'shipment_id' => $shipment_id])->update(['status' => 1]);

            $date = Carbon::now();
            DeliveryNote::where('id', $note->id)->update(['delivered_shipments' => 0, 'verified_by' => 184, 'status' => 1, 'last_updated_at' => $date, 'status_verified_at' => $date, 'pending_for_verification_at' => $date]);

            $shipment->refresh();

            $destination_hub_id = $shipment->pickup_address->city->hub_id;
            $origin_hub_id = $shipment->consignee_city->hub_id;
            if($origin_hub_id != $destination_hub_id){


                $shipments = 1;
                $quantity = 1;
                $shipments_weight = $shipment->actual_weight;
                $bag = new Bag();

                $bag->origin_hub_id = $origin_hub_id;
                $bag->destination_hub_id = $destination_hub_id;
                $bag->junction_hub_1_id = $origin_hub_id;
                $bag->junction_hub_2_id = NULL;
                $bag->seal_number = $shipment->tracking_number;
                $bag->shipping_mode_id = 1;
                $bag->transport_mode_id = 2;
                $bag->transport_mode_vendor_id = 9;
                $bag->shipments = $shipments;
                $bag->quantity = $quantity;
                $bag->shipments_weight = $shipments_weight;
                $bag->actual_weight = $shipments_weight;
                $bag->created_by = 184;
                $bag->type = 2;

                $bag->status_id = 9;

                $bag->save();

                $bag_id = $bag->id;

                MasterCargoBagJourneyController::add($bag_id, $bag->seal_number, 1, 184, NULL, NULL);
                MasterCargoBagJourneyController::add($bag_id, $bag->seal_number, 2, 184, NULL, NULL);
                MasterCargoBagJourneyController::add($bag_id, $bag->seal_number, 9, 184, NULL, NULL);


                $bag_shipment = new BagShipment();

                $bag_shipment->bag_id = $bag_id;
                $bag_shipment->shipment_id = $shipment_id;

                $bag_shipment->save();

                $shipment->shipper_status_id = 22;
                $shipment->consignee_status_id = 22;

                ShipmentsJourneyController::add($shipment_id, 21, 21, NULL, NULL, NULL, 184, $bag->id, $bag->builty_number);
                ShipmentsJourneyController::add($shipment_id, 22, 22, NULL, NULL, NULL, 184, $bag->id, $bag->builty_number);


                $bags = 1;
                $shipments = 1;
                $quantity = 1;
                $bags_weight = $shipments_weight;
                $master_cargo_status_id = 3;

                $master_cargo = new MasterCargo();

                $master_cargo->origin_hub_id = $origin_hub_id;
                $master_cargo->destination_hub_id = $destination_hub_id;
                $master_cargo->junction_hub_1_id = $origin_hub_id;
                $master_cargo->junction_hub_2_id = NULL;
                $master_cargo->shipping_mode_id = 1;
                $master_cargo->transport_mode_id = 2;
                $master_cargo->driver_name = 'International Driver';
                $master_cargo->vehicle = 'DHL Plane';
                $master_cargo->phone_number = '0000-0000000';
                $master_cargo->bags = $bags;
                $master_cargo->shipments = $shipments;
                $master_cargo->quantity = $quantity;
                $master_cargo->bags_weight = $bags_weight;
                $master_cargo->actual_weight = $shipments_weight;
                $master_cargo->created_by = 184;
                $master_cargo->status_id = $master_cargo_status_id;
                $master_cargo->save();

                $master_cargo_id = $master_cargo->id;
                $mater_cargo_bags= new MasterCargoBag();

                $mater_cargo_bags->master_cargo_id = $master_cargo_id;
                $mater_cargo_bags->bag_id = $bag_id;

                $mater_cargo_bags->save();

                MasterCargoBagJourneyController::add($bag_id, $bag->seal_number, $bag->status_id, 184, $master_cargo_id, $master_cargo_status_id);

            }

        }

    }
    public function generate_assigned_pickup($pickup_request_id, $rider_id){
        $pickup_request = V2PickupRequest::find($pickup_request_id);

        $pickup_request_attempt = new V2PickupRequestAttempt();
        $pickup_request_attempt->pickup_request_id = $pickup_request_id;
        $pickup_request_attempt->rider_id = $rider_id;
        $pickup_request_attempt->attempt_date = Carbon::now();
        $pickup_request_attempt->assigned_by = 184;
        $pickup_request_attempt->save();

        $pickup_request->rider_status = 2;
        $pickup_request->attempts = $pickup_request->attempts + 1;
        $pickup_request->current_rider_id = $rider_id;
        $pickup_request->last_updated_by = 184;
        $pickup_request->save();

        $pickup_note = V2PickupNote::where('rider_id', $rider_id)->where('status', 0);

        if ($pickup_note->exists()) {
            $pickup_note = $pickup_note->first();

            $pickup_note->pickups += 1;
            $pickup_note->shipments += $pickup_request->booked;

            $pickup_note->save();

            $pickup_note_id = $pickup_note->id;

            $pickup_note_requests = V2PickupNoteRequest::where('pickup_note_id', $pickup_note_id)->where('pickup_request_id', $pickup_request_id);
            if($pickup_note_requests->exists()){

                $pickup_note_request = $pickup_note_requests->first();

                $pickup_note_request->pickup_note_id = $pickup_note_id;
                $pickup_note_request->pickup_request_id = $pickup_request_id;
                $pickup_note_request->status = 1;

                $pickup_note_request->save();
            }
            else{
                $pickup_note_request = new V2PickupNoteRequest();

                $pickup_note_request->pickup_note_id = $pickup_note_id;
                $pickup_note_request->pickup_request_id = $pickup_request_id;
                $pickup_note_request->status = 1;

                $pickup_note_request->save();
            }
        }
        else {
            $pickup_note = new V2PickupNote();

            $pickup_note->rider_id = $rider_id;
            $pickup_note->pickups = 1;
            $pickup_note->shipments = $pickup_request->booked;
            $pickup_note->save();

            $pickup_note_id = $pickup_note->id;

            $pickup_note_request = new V2PickupNoteRequest();

            $pickup_note_request->pickup_note_id = $pickup_note_id;
            $pickup_note_request->pickup_request_id = $pickup_request_id;
            $pickup_note_request->status = 1;

            $pickup_note_request->save();
        }



        return $pickup_note_id;
    }
}
