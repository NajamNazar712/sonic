<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\Admins\MasterCargoBagJourneyController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\Admins\V2Pickup\V2AdminPickupsController;
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
use App\http\Models\SelfCollectionShipment;
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
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }



    static public function dhl_tracking(){

        $dhl_api_key = 'EXYgAAqX3c5TYz3GqVLCS6e1fS57AAYO';
        $dhl_url = 'https://api-eu.dhl.com/track/';

        $response = '{
  "shipments": [
    {
      "id": "7837361350",
      "service": "express",
      "origin": {
        "address": {
          "addressLocality": "KARACHI - KARACHI - PAKISTAN"
        }
      },
      "destination": {
        "address": {
          "addressLocality": "KARACHI - OSWEGO - PAKISTAN"
        }
      },
      "status": {
        "timestamp": "2021-05-04T17:47:00",
        "location": {
          "address": {
            "addressLocality": "KARACHI - PAKISTAN"
          }
        },
        "statusCode": "failure",
        "status": "exception",
        "description": "Returned to shipper"
      },
      "details": {
        "proofOfDelivery": {
          "signatureUrl": "https://webpod.dhl.com/webPOD/DHLePODRequest?hwb=HIyI5ZQw9ML%2BPB%2FgRhc6ZA%3D%3D&pudate=H%2FLXM5bvdh9v4JvRMeM7Gw%3D%3D&appuid=XGULCnoKLyskTnvIFkscaQ%3D%3D&language=en&country=G0",
          "documentUrl": "https://webpod.dhl.com/webPOD/DHLePODRequest?hwb=HIyI5ZQw9ML%2BPB%2FgRhc6ZA%3D%3D&pudate=H%2FLXM5bvdh9v4JvRMeM7Gw%3D%3D&appuid=XGULCnoKLyskTnvIFkscaQ%3D%3D&language=en&country=G0"
        },
        "totalNumberOfPieces": 1,
        "pieceIds": [
          "JD014600008665793657"
        ]
      },
      "events": [
        {
          "timestamp": "2021-05-04T17:47:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Returned to shipper"
        },
        {
          "timestamp": "2021-05-04T15:53:00",
          "location": {
            "address": {
              "addressLocality": "DUBAI - UNITED ARAB EMIRATES"
            }
          },
          "description": "Arrived at Sort Facility DUBAI - UNITED ARAB EMIRATES"
        },
        {
          "timestamp": "2021-05-04T09:49:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Departed Facility in KARACHI - PAKISTAN"
        },
        {
          "timestamp": "2021-05-04T00:51:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Processed at KARACHI - PAKISTAN"
        },
        {
          "timestamp": "2021-05-03T22:01:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Shipment Accepted"
        },
        {
          "timestamp": "2021-05-03T22:01:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Shipment picked up"
        }
      ]
    },
    {
      "id": "1600370450",
      "service": "express",
      "origin": {
        "address": {
          "addressLocality": "KARACHI - KARACHI - PAKISTAN"
        }
      },
      "destination": {
        "address": {
          "addressLocality": "KARACHI - HIALEAH - PAKISTAN"
        }
      },
      "status": {
        "timestamp": "2021-05-04T17:48:00",
        "location": {
          "address": {
            "addressLocality": "KARACHI - PAKISTAN"
          }
        },
        "statusCode": "failure",
        "status": "exception",
        "description": "Returned to shipper"
      },
      "details": {
        "proofOfDelivery": {
          "signatureUrl": "https://webpod.dhl.com/webPOD/DHLePODRequest?hwb=bYFqpz3ou%2Bpvz7tW8SZ%2BUA%3D%3D&pudate=HafB9vPKf700yKYzuH%2FG4g%3D%3D&appuid=l%2Bq%2FHtc6RizTu%2FvQ4fKztQ%3D%3D&language=en&country=G0",
          "documentUrl": "https://webpod.dhl.com/webPOD/DHLePODRequest?hwb=bYFqpz3ou%2Bpvz7tW8SZ%2BUA%3D%3D&pudate=HafB9vPKf700yKYzuH%2FG4g%3D%3D&appuid=l%2Bq%2FHtc6RizTu%2FvQ4fKztQ%3D%3D&language=en&country=G0"
        },
        "totalNumberOfPieces": 1,
        "pieceIds": [
          "JD014600008668192770"
        ]
      },
      "events": [
        {
          "timestamp": "2021-05-04T17:48:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Returned to shipper"
        },
        {
          "timestamp": "2021-05-04T15:53:00",
          "location": {
            "address": {
              "addressLocality": "DUBAI - UNITED ARAB EMIRATES"
            }
          },
          "description": "Arrived at Sort Facility DUBAI - UNITED ARAB EMIRATES"
        },
        {
          "timestamp": "2021-05-04T09:49:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Departed Facility in KARACHI - PAKISTAN"
        },
        {
          "timestamp": "2021-05-04T01:43:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Processed at KARACHI - PAKISTAN"
        },
        {
          "timestamp": "2021-05-03T16:20:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Shipment picked up"
        },
        {
          "timestamp": "2021-05-03T16:20:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Shipment Accepted"
        }
      ]
    },
    {
      "id": "1297318621",
      "service": "express",
      "origin": {
        "address": {
          "addressLocality": "KARACHI - KARACHI - PAKISTAN"
        }
      },
      "destination": {
        "address": {
          "addressLocality": "NEW YORK, NY - EAST ELMHURST - USA"
        }
      },
      "status": {
        "timestamp": "2021-04-26T11:42:00",
        "location": {
          "address": {
            "addressLocality": "EAST ELMHURST"
          }
        },
        "statusCode": "delivered",
        "status": "delivered",
        "description": "Delivered"
      },
      "details": {
        "proofOfDelivery": {
          "timestamp": "2021-04-26T11:42:00",
          "signatureUrl": "https://webpod.dhl.com/webPOD/DHLePODRequest?hwb=a%2FWPGBbwKAuAhUodE78X3Q%3D%3D&pudate=tXMCnmf%2BbnPg9tzxoYCXdQ%3D%3D&appuid=4L5D0s%2BN1GZjoQHER9S9XA%3D%3D&language=en&country=G0",
          "documentUrl": "https://webpod.dhl.com/webPOD/DHLePODRequest?hwb=a%2FWPGBbwKAuAhUodE78X3Q%3D%3D&pudate=tXMCnmf%2BbnPg9tzxoYCXdQ%3D%3D&appuid=4L5D0s%2BN1GZjoQHER9S9XA%3D%3D&language=en&country=G0",
          "signed": {
            "@type": "Person",
            "name": "Delivered"
          }
        },
        "totalNumberOfPieces": 1,
        "pieceIds": [
          "JD014600008650258972"
        ]
      },
      "events": [
        {
          "timestamp": "2021-04-26T11:42:00",
          "location": {
            "address": {
              "addressLocality": "EAST ELMHURST"
            }
          },
          "description": "Delivered"
        },
        {
          "timestamp": "2021-04-26T10:10:00",
          "location": {
            "address": {
              "addressLocality": "NEW YORK, NY - USA"
            }
          },
          "description": "With delivery courier"
        },
        {
          "timestamp": "2021-04-26T06:57:00",
          "location": {
            "address": {
              "addressLocality": "NEW YORK, NY - USA"
            }
          },
          "description": "Arrived at Delivery Facility in NEW YORK - USA"
        },
        {
          "timestamp": "2021-04-26T04:03:00",
          "location": {
            "address": {
              "addressLocality": "NEW YORK CITY GATEWAY, NY - USA"
            }
          },
          "description": "Departed Facility in NEW YORK CITY GATEWAY - USA"
        },
        {
          "timestamp": "2021-04-25T18:21:00",
          "location": {
            "address": {
              "addressLocality": "NEW YORK CITY GATEWAY, NY - USA"
            }
          },
          "description": "Processed at NEW YORK CITY GATEWAY - USA"
        },
        {
          "timestamp": "2021-04-25T18:21:00",
          "location": {
            "address": {
              "addressLocality": "NEW YORK CITY GATEWAY, NY - USA"
            }
          },
          "description": "Clearance processing complete at NEW YORK CITY GATEWAY - USA"
        },
        {
          "timestamp": "2021-04-25T15:00:00",
          "location": {
            "address": {
              "addressLocality": "NEW YORK CITY GATEWAY, NY - USA"
            }
          },
          "description": "Arrived at Sort Facility NEW YORK CITY GATEWAY - USA"
        },
        {
          "timestamp": "2021-04-25T11:08:00",
          "location": {
            "address": {
              "addressLocality": "BRUSSELS - BELGIUM"
            }
          },
          "description": "Departed Facility in BRUSSELS - BELGIUM"
        },
        {
          "timestamp": "2021-04-25T09:07:00",
          "location": {
            "address": {
              "addressLocality": "NEW YORK CITY GATEWAY, NY - USA"
            }
          },
          "description": "Customs status updated"
        },
        {
          "timestamp": "2021-04-23T18:53:00",
          "location": {
            "address": {
              "addressLocality": "BRUSSELS - BELGIUM"
            }
          },
          "description": "Processed at BRUSSELS - BELGIUM"
        },
        {
          "timestamp": "2021-04-23T17:51:00",
          "location": {
            "address": {
              "addressLocality": "BRUSSELS - BELGIUM"
            }
          },
          "description": "Arrived at Sort Facility BRUSSELS - BELGIUM"
        },
        {
          "timestamp": "2021-04-23T02:54:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Departed Facility in KARACHI - PAKISTAN"
        },
        {
          "timestamp": "2021-04-22T21:26:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Processed at KARACHI - PAKISTAN"
        },
        {
          "timestamp": "2021-04-22T16:41:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Shipment picked up"
        },
        {
          "timestamp": "2021-04-22T16:41:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Shipment Accepted"
        }
      ]
    }
  ]
}';

        $arrival_status = ['pre-transit'];
        $intransit_status = ['transit'];
        $delivered_status = ['delivered'];
        $returned_status = ['failure', 'unknown'];

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
//                                    $origin_city_id = $shipment->pickup_address->city_id;
//                                    $destination_city_id = $shipment->consignee_city_id;
                                    $shipment_id = $shipment->id;
                                    $shipper_status_id = $shipment->shipper_status_id;
                                    $international_shipment_status = $intl_shipment->status->status;
                                    if(in_array($international_shipment_status, $arrival_status)){
                                        if($shipper_status_id == 1){
                                            (new self)->shipment_arrived($shipment->id);
                                        }
                                    }
                                    else if(in_array($international_shipment_status, $intransit_status)){
                                        if($shipper_status_id == 1){
                                            (new self)->shipment_arrived($shipment->id);
                                            (new self)->shipment_intransit($shipment->id);
                                        }
                                        else if($shipper_status_id == 2){
                                            (new self)->shipment_intransit($shipment->id);
                                        }
                                    }
                                    else if(in_array($international_shipment_status, $delivered_status)){
                                        if($shipper_status_id == 1){
                                            (new self)->shipment_arrived($shipment->id);
                                            (new self)->shipment_intransit($shipment->id);
                                            (new self)->shipment_delivered($shipment->id);
                                        }
                                        else if($shipper_status_id == 2){
                                            (new self)->shipment_intransit($shipment->id);
                                            (new self)->shipment_delivered($shipment->id);
                                        }
                                        else if($shipper_status_id == 4){
                                            (new self)->shipment_delivered($shipment->id);
                                        }
                                    }
                                    else if(in_array($international_shipment_status, $returned_status)){
                                        if($shipper_status_id == 1){
                                            (new self)->shipment_arrived($shipment->id);
                                            (new self)->shipment_intransit($shipment->id);
                                            (new self)->shipment_return($shipment->id);
                                        }
                                        else if($shipper_status_id == 2){
                                            (new self)->shipment_intransit($shipment->id);
                                            (new self)->shipment_return($shipment->id);
                                        }
                                        else if($shipper_status_id == 4){
                                            (new self)->shipment_return($shipment->id);
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

    public function shipment_arrived($shipment_id){
        $pickup_request_id = NULL;
        $shipment = Shipment::find($shipment_id);
        if($shipment->shipper_status_id == 1){

            $settings = GlobalSettings::where('type', 'global_rider_id')->first();

            if ($settings) {
                $global_rider_id = $settings->setting_value;
            } else {
                $global_rider_id = 0;
            }
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
                NotificationsController::send(85, $shipment_ids , 50);
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
                    if($pickup_note_requests_count == 0){
                        V2PickupNote::where('id', $pickup_note_id)->update(['status' => 1]);
                    }
                }

                ShipmentsPickupJourneyController::add($shipment_id, 2, 50, $pickup_request->id);

            }
        }
    }
    public function shipment_intransit($shipment_id){
        $shipment = Shipment::find($shipment_id);
        if($shipment->shipper_status_id == 2){
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
                $bag->seal_number = 123213123;
                $bag->shipping_mode_id = 1;
                $bag->transport_mode_id = 2;
                $bag->transport_mode_vendor_id = 9;
                $bag->shipments = $shipments;
                $bag->quantity = $quantity;
                $bag->shipments_weight = $shipments_weight;
                $bag->actual_weight = $shipments_weight;
                $bag->created_by = 50;
                $bag->type = 1;

                $bag->status_id = 9;

                $bag->save();

                $bag_id = $bag->id;

                MasterCargoBagJourneyController::add($bag_id, $bag->seal_number, 1, 50, NULL, NULL);
                MasterCargoBagJourneyController::add($bag_id, $bag->seal_number, 2, 50, NULL, NULL);
                MasterCargoBagJourneyController::add($bag_id, $bag->seal_number, 9, 50, NULL, NULL);


                $bag_shipment = new BagShipment();

                $bag_shipment->bag_id = $bag_id;
                $bag_shipment->shipment_id = $shipment_id;

                $bag_shipment->save();

                $shipper_status_id = NULL;
                $consignee_status_id = NULL;
                if ($shipment->shipper_status_id != 20) {
                    if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 4 || $shipment->booking_type_id == 5) {
                        $shipper_status_id = 21;
                        $consignee_status_id = 21;
                    }
                    else if ($shipment->booking_type_id == 2) {
                        $shipper_status_id = 26;
                        $consignee_status_id = 26;
                    }
                    else if ($shipment->booking_type_id == 3) {
                        $shipper_status_id = 32;
                        $consignee_status_id = 32;
                    }
                    else {
                        $shipper_status_id = 21;
                        $consignee_status_id = 21;
                    }
                }
                $shipment->shipper_status_id = $shipper_status_id;
                $shipment->consignee_status_id = $consignee_status_id;

                $shipment->save();

                ShipmentsJourneyController::add($shipment_id, 3, 3, NULL, NULL, NULL, 50, $bag->id, $bag->builty_number);
                ShipmentsJourneyController::add($shipment_id, 4, 4, NULL, NULL, NULL, 50, $bag->id, $bag->builty_number);


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
                $master_cargo->created_by = 50;
                $master_cargo->status_id = $master_cargo_status_id;
                $master_cargo->save();

                $master_cargo_id = $master_cargo->id;
                $mater_cargo_bags= new MasterCargoBag();

                $mater_cargo_bags->master_cargo_id = $master_cargo_id;
                $mater_cargo_bags->bag_id = $bag_id;

                $mater_cargo_bags->save();

                MasterCargoBagJourneyController::add($bag_id, $bag->seal_number, $bag->status_id, 50, $master_cargo_id, $master_cargo_status_id);

            }
        }
    }
    public function shipment_delivered($shipment_id){

        $settings = GlobalSettings::where('type', 'nsa_accounts')->first();
        $rider_id = $settings->setting_value;

        $shipment = Shipment::find($shipment_id);

        $hub_id = City::find($shipment->consignee_city_id)->hub_id;
        $note = DeliveryNote::create([
            'hub_id' => $hub_id,
            'rider_id' => $rider_id,
            'route_id' => 2,
            'shipments_count' => 1,
            'admin_id' => 50,
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
            ShipmentsJourneyController::add($shipment, 5, 5, NULL, NULL, NULL, 50, $note->id, $rider_id);
            ShipmentsJourneyController::add($shipment, 14, 14, NULL, NULL, NULL, 50, $note->id, NULL, 1);
            DeliveryNoteShipment::where(['delivery_note_id' => $note->id, 'shipment_id' => $shipment_id])->update(['status' => 6]);
            $date = Carbon::now();
            DeliveryNote::where('id', $note->id)->update(['delivered_shipments' => 1, 'verified_by' => 50, 'received_cod_amount' => $shipment->amount, 'status' => 1, 'last_updated_at' => $date, 'status_verified_at' => $date, 'pending_for_verification_at' => $date]);

            $international_shipment = InternationalShipment::where('shipment_id', $shipment_id)->first();
            if($international_shipment){
                $international_shipment->sync = 0;
                $international_shipment->save();
            }
        }


    }
    public function shipment_return($shipment_id){

        $settings = GlobalSettings::where('type', 'nsa_accounts')->first();
        $rider_id = $settings->setting_value;

        $shipment = Shipment::find($shipment_id);
        $hub_id = City::find($shipment->consignee_city_id)->hub_id;
        $note = DeliveryNote::create([
            'hub_id' => $hub_id,
            'rider_id' => $rider_id,
            'route_id' => 2,
            'shipments_count' => 1,
            'admin_id' => 50,
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


            ShipmentsJourneyController::add($shipment_id, 5, 5, NULL, NULL, NULL, 50, $note->id, $rider_id);
            ShipmentsJourneyController::add($shipment_id, 12, 12, 34, NULL, NULL, 50, $note->id, NULL, 0);
            ShipmentsJourneyController::add($shipment_id, 20, 20, 34, NULL, NULL, 50, $note->id, NULL, 1);

            DeliveryNoteShipment::where(['delivery_note_id' => $note->id, 'shipment_id' => $shipment_id])->update(['status' => 1]);

            $date = Carbon::now();
            DeliveryNote::where('id', $note->id)->update(['delivered_shipments' => 0, 'verified_by' => 50, 'status' => 1, 'last_updated_at' => $date, 'status_verified_at' => $date, 'pending_for_verification_at' => $date]);

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
                $bag->seal_number = 123213123;
                $bag->shipping_mode_id = 1;
                $bag->transport_mode_id = 2;
                $bag->transport_mode_vendor_id = 9;
                $bag->shipments = $shipments;
                $bag->quantity = $quantity;
                $bag->shipments_weight = $shipments_weight;
                $bag->actual_weight = $shipments_weight;
                $bag->created_by = 50;
                $bag->type = 2;

                $bag->status_id = 9;

                $bag->save();

                $bag_id = $bag->id;

                MasterCargoBagJourneyController::add($bag_id, $bag->seal_number, 1, 50, NULL, NULL);
                MasterCargoBagJourneyController::add($bag_id, $bag->seal_number, 2, 50, NULL, NULL);
                MasterCargoBagJourneyController::add($bag_id, $bag->seal_number, 9, 50, NULL, NULL);


                $bag_shipment = new BagShipment();

                $bag_shipment->bag_id = $bag_id;
                $bag_shipment->shipment_id = $shipment_id;

                $bag_shipment->save();

                $shipment->shipper_status_id = 22;
                $shipment->consignee_status_id = 22;

                ShipmentsJourneyController::add($shipment_id, 21, 21, NULL, NULL, NULL, 50, $bag->id, $bag->builty_number);
                ShipmentsJourneyController::add($shipment_id, 22, 22, NULL, NULL, NULL, 50, $bag->id, $bag->builty_number);


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
                $master_cargo->created_by = 50;
                $master_cargo->status_id = $master_cargo_status_id;
                $master_cargo->save();

                $master_cargo_id = $master_cargo->id;
                $mater_cargo_bags= new MasterCargoBag();

                $mater_cargo_bags->master_cargo_id = $master_cargo_id;
                $mater_cargo_bags->bag_id = $bag_id;

                $mater_cargo_bags->save();

                MasterCargoBagJourneyController::add($bag_id, $bag->seal_number, $bag->status_id, 50, $master_cargo_id, $master_cargo_status_id);

            }

        }

    }
    public function generate_assigned_pickup($pickup_request_id, $rider_id){
        $pickup_request = V2PickupRequest::find($pickup_request_id);

        $pickup_request_attempt = new V2PickupRequestAttempt();
        $pickup_request_attempt->pickup_request_id = $pickup_request_id;
        $pickup_request_attempt->rider_id = $rider_id;
        $pickup_request_attempt->attempt_date = Carbon::now();
        $pickup_request_attempt->assigned_by = 50;
        $pickup_request_attempt->save();

        $pickup_request->rider_status = 2;
        $pickup_request->attempts = $pickup_request->attempts + 1;
        $pickup_request->current_rider_id = $rider_id;
        $pickup_request->last_updated_by = 50;
        $pickup_request->save();

        $pickup_note = V2PickupNote::where('rider_id', $rider_id)->where('status', 0);

        if ($pickup_note->exists()) {
            $pickup_note = $pickup_note->first();

            $pickup_note->pickups += 1;

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
