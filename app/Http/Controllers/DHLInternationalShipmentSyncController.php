<?php

namespace App\Http\Controllers;

use App\Http\Models\InternationalShipment;
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
        $tracking_number = 9750874965;
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

        $status_codes = ['pre-transit', 'transit', 'delivered', 'failure', 'unknown'];

        $international_tracking_numbers = InternationalShipment::whereNotNull('international_tracking_number')->where('sync', 1)->pluck('international_tracking_number')->toArray();

        if(count($international_tracking_numbers) > 0){

            print_r($international_tracking_numbers);

            print("Hello \n");

            $chunk_international_shipments = array();

//            $chunk_international_shipments = array_chunk($international_tracking_numbers, 2);

//            foreach ($chunk_international_shipments as $chunk_international_shipment){
//                $international_tracking_numbers = implode(',',$chunk_international_shipment);

//                $client = new Client(['base_uri' => $dhl_url, 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
//                try{
//                    $response = $client->get('shipments', [
//                        'headers' => [
//                            'DHL-API-Key' => $dhl_api_key,
//                        ],
//                        'query' => [
//                            'trackingNumber' => $international_tracking_numbers
//                        ]
//                    ]);
//                    $status_code = $response->getStatusCode();

//                    if($status_code == 200){
//                        $response = $response->getBody()->getContents();
                        $response = json_decode($response);
                        $international_shipments = $response->shipments;
                        if(count($international_shipments) > 0){
                            foreach ($international_shipments as $international_shipment){
                                $intl_shipment = InternationalShipment::where('international_tracking_number', $international_shipment->id)->where('sync', 1)->first();
                                if($intl_shipment){
                                    $shipment = $intl_shipment->shipment;
                                    $origin_city_id = $shipment->pickup_address->city_id;
                                    $destination_city_id = $shipment->consignee_city_id;
                                    $shipment_id = $shipment->id;
                                }
                            }
                        }
//                    }

//                }catch (RequestException $exception){
//                    Log::info($exception);
//                }

//            }




//            }
        }
    }

    public function shipment_arrived($shipment_id){

    }
    public function shipment_intransit($shipment_id){

    }
    public function shipment_delivered($shipment_id){

    }
    public function shipment_return($shipment_id){

    }
}
