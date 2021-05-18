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
      "id": "7837177526",
      "service": "express",
      "origin": {
        "address": {
          "addressLocality": "KARACHI - KARACHI - PAKISTAN"
        }
      },
      "destination": {
        "address": {
          "addressLocality": "SOUTHERN ALBERTA, AB - CALGARY - CANADA"
        }
      },
      "status": {
        "timestamp": "2021-05-05T15:42:00",
        "location": {
          "address": {
            "addressLocality": "CALGARY"
          }
        },
        "statusCode": "delivered",
        "status": "delivered",
        "description": "Delivered"
      },
      "details": {
        "proofOfDelivery": {
          "timestamp": "2021-05-05T15:42:00",
          "signatureUrl": "https://webpod.dhl.com/webPOD/DHLePODRequest?hwb=OBvVocSjllvofDoLcGeeNg%3D%3D&pudate=v0a0iIfJ4nzlFxyuFR%2BYLw%3D%3D&appuid=u6Nea0eLLDV14sbJMpOHuw%3D%3D&language=en&country=G0",
          "documentUrl": "https://webpod.dhl.com/webPOD/DHLePODRequest?hwb=OBvVocSjllvofDoLcGeeNg%3D%3D&pudate=v0a0iIfJ4nzlFxyuFR%2BYLw%3D%3D&appuid=u6Nea0eLLDV14sbJMpOHuw%3D%3D&language=en&country=G0",
          "signed": {
            "@type": "Person",
            "name": "Delivered"
          }
        },
        "totalNumberOfPieces": 1,
        "pieceIds": [
          "JD014600008665770753"
        ]
      },
      "events": [
        {
          "timestamp": "2021-05-05T15:42:00",
          "location": {
            "address": {
              "addressLocality": "CALGARY"
            }
          },
          "description": "Delivered"
        },
        {
          "timestamp": "2021-05-05T13:08:00",
          "location": {
            "address": {
              "addressLocality": "SOUTHERN ALBERTA, AB - CANADA"
            }
          },
          "description": "With delivery courier"
        },
        {
          "timestamp": "2021-05-05T10:19:00",
          "location": {
            "address": {
              "addressLocality": "SOUTHERN ALBERTA, AB - CANADA"
            }
          },
          "description": "Clearance processing complete at SOUTHERN ALBERTA - CANADA"
        },
        {
          "timestamp": "2021-05-05T09:45:00",
          "location": {
            "address": {
              "addressLocality": "SOUTHERN ALBERTA, AB - CANADA"
            }
          },
          "description": "Arrived at Sort Facility SOUTHERN ALBERTA - CANADA"
        },
        {
          "timestamp": "2021-05-05T08:36:00",
          "location": {
            "address": {
              "addressLocality": "SOUTHERN ALBERTA, AB - CANADA"
            }
          },
          "description": "Customs status updated"
        },
        {
          "timestamp": "2021-05-05T06:55:00",
          "location": {
            "address": {
              "addressLocality": "CINCINNATI HUB, OH - USA"
            }
          },
          "description": "Departed Facility in CINCINNATI HUB - USA"
        },
        {
          "timestamp": "2021-05-04T23:13:00",
          "location": {
            "address": {
              "addressLocality": "CINCINNATI HUB, OH - USA"
            }
          },
          "description": "Processed at CINCINNATI HUB - USA"
        },
        {
          "timestamp": "2021-05-04T21:59:00",
          "location": {
            "address": {
              "addressLocality": "CINCINNATI HUB, OH - USA"
            }
          },
          "description": "Arrived at Sort Facility CINCINNATI HUB - USA"
        },
        {
          "timestamp": "2021-05-04T10:01:00",
          "location": {
            "address": {
              "addressLocality": "CINCINNATI HUB, OH - USA"
            }
          },
          "description": "Shipment on hold"
        },
        {
          "timestamp": "2021-05-03T08:08:00",
          "location": {
            "address": {
              "addressLocality": "CINCINNATI HUB, OH - USA"
            }
          },
          "description": "Shipment on hold"
        },
        {
          "timestamp": "2021-05-01T12:44:00",
          "location": {
            "address": {
              "addressLocality": "CINCINNATI HUB, OH - USA"
            }
          },
          "description": "Customs status updated"
        },
        {
          "timestamp": "2021-05-01T10:01:00",
          "location": {
            "address": {
              "addressLocality": "CINCINNATI HUB, OH - USA"
            }
          },
          "description": "Shipment on hold"
        },
        {
          "timestamp": "2021-04-30T21:25:00",
          "location": {
            "address": {
              "addressLocality": "BRUSSELS - BELGIUM"
            }
          },
          "description": "Departed Facility in BRUSSELS - BELGIUM"
        },
        {
          "timestamp": "2021-04-30T19:05:00",
          "location": {
            "address": {
              "addressLocality": "CINCINNATI HUB, OH - USA"
            }
          },
          "description": "Customs status updated"
        },
        {
          "timestamp": "2021-04-29T23:03:00",
          "location": {
            "address": {
              "addressLocality": "BRUSSELS - BELGIUM"
            }
          },
          "description": "Processed at BRUSSELS - BELGIUM"
        },
        {
          "timestamp": "2021-04-29T15:36:00",
          "location": {
            "address": {
              "addressLocality": "BRUSSELS - BELGIUM"
            }
          },
          "description": "Arrived at Sort Facility BRUSSELS - BELGIUM"
        },
        {
          "timestamp": "2021-04-29T06:21:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Departed Facility in KARACHI - PAKISTAN"
        },
        {
          "timestamp": "2021-04-28T22:09:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Processed at KARACHI - PAKISTAN"
        },
        {
          "timestamp": "2021-04-28T16:55:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Shipment picked up"
        },
        {
          "timestamp": "2021-04-28T16:55:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Shipment Accepted"
        },
        {
          "timestamp": "2020-05-03T08:08:00",
          "location": {
            "address": {
              "addressLocality": "CINCINNATI HUB, OH - USA"
            }
          },
          "description": "Shipment on hold"
        }
      ]
    },
    {
      "id": "9750874965",
      "service": "express",
      "origin": {
        "address": {
          "addressLocality": "KARACHI - KARACHI - PAKISTAN"
        }
      },
      "destination": {
        "address": {
          "addressLocality": "SHARJAH - SHARJAH - UNITED ARAB EMIRATES"
        }
      },
      "status": {
        "timestamp": "2021-05-10T13:22:00",
        "location": {
          "address": {
            "addressLocality": "SHARJAH"
          }
        },
        "statusCode": "delivered",
        "status": "delivered",
        "description": "Delivered - Signed for by: MOHAMED. HUZEF"
      },
      "details": {
        "proofOfDelivery": {
          "timestamp": "2021-05-10T13:22:00",
          "signatureUrl": "https://webpod.dhl.com/webPOD/DHLePODRequest?hwb=h0ThTPQUqTynzkN7k9bQBA%3D%3D&pudate=daN%2FePkbEue%2By9vInUFPnA%3D%3D&appuid=7gywRO%2Fa19CkfG2jckECFw%3D%3D&language=en&country=G0",
          "documentUrl": "https://webpod.dhl.com/webPOD/DHLePODRequest?hwb=h0ThTPQUqTynzkN7k9bQBA%3D%3D&pudate=daN%2FePkbEue%2By9vInUFPnA%3D%3D&appuid=7gywRO%2Fa19CkfG2jckECFw%3D%3D&language=en&country=G0",
          "signed": {
            "@type": "Person",
            "name": "MOHAMED. HUZEF"
          }
        },
        "totalNumberOfPieces": 1,
        "pieceIds": [
          "JD014600008687507677"
        ]
      },
      "events": [
        {
          "timestamp": "2021-05-10T13:22:00",
          "location": {
            "address": {
              "addressLocality": "SHARJAH"
            }
          },
          "description": "Delivered - Signed for by: MOHAMED. HUZEF"
        },
        {
          "timestamp": "2021-05-10T13:22:00",
          "location": {
            "address": {
              "addressLocality": "SHARJAH - UNITED ARAB EMIRATES"
            }
          },
          "description": "Payment is received and recorded for shipment related fees"
        },
        {
          "timestamp": "2021-05-10T08:30:00",
          "location": {
            "address": {
              "addressLocality": "SHARJAH - UNITED ARAB EMIRATES"
            }
          },
          "description": "With delivery courier"
        },
        {
          "timestamp": "2021-05-08T15:41:00",
          "location": {
            "address": {
              "addressLocality": "SHARJAH - UNITED ARAB EMIRATES"
            }
          },
          "description": "Delivery attempted; recipient not home"
        },
        {
          "timestamp": "2021-05-08T08:58:00",
          "location": {
            "address": {
              "addressLocality": "SHARJAH - UNITED ARAB EMIRATES"
            }
          },
          "description": "With delivery courier"
        },
        {
          "timestamp": "2021-05-06T14:44:00",
          "location": {
            "address": {
              "addressLocality": "SHARJAH - UNITED ARAB EMIRATES"
            }
          },
          "description": "Delivery attempted; recipient not home"
        },
        {
          "timestamp": "2021-05-06T13:34:00",
          "location": {
            "address": {
              "addressLocality": "SHARJAH - UNITED ARAB EMIRATES"
            }
          },
          "description": "With delivery courier"
        },
        {
          "timestamp": "2021-05-06T13:09:00",
          "location": {
            "address": {
              "addressLocality": "SHARJAH - UNITED ARAB EMIRATES"
            }
          },
          "description": "Shipment on hold"
        },
        {
          "timestamp": "2021-05-06T13:05:00",
          "location": {
            "address": {
              "addressLocality": "SHARJAH - UNITED ARAB EMIRATES"
            }
          },
          "description": "Arrived at Delivery Facility in SHARJAH - UNITED ARAB EMIRATES"
        },
        {
          "timestamp": "2021-05-06T12:15:00",
          "location": {
            "address": {
              "addressLocality": "DUBAI - UNITED ARAB EMIRATES"
            }
          },
          "description": "Departed Facility in DUBAI - UNITED ARAB EMIRATES"
        },
        {
          "timestamp": "2021-05-06T11:14:00",
          "location": {
            "address": {
              "addressLocality": "DUBAI - UNITED ARAB EMIRATES"
            }
          },
          "description": "Processed at DUBAI - UNITED ARAB EMIRATES"
        },
        {
          "timestamp": "2021-05-06T10:49:00",
          "location": {
            "address": {
              "addressLocality": "DUBAI - UNITED ARAB EMIRATES"
            }
          },
          "description": "Clearance processing complete at DUBAI - UNITED ARAB EMIRATES"
        },
        {
          "timestamp": "2021-05-06T10:03:00",
          "location": {
            "address": {
              "addressLocality": "DUBAI - UNITED ARAB EMIRATES"
            }
          },
          "description": "Arrived at Sort Facility DUBAI - UNITED ARAB EMIRATES"
        },
        {
          "timestamp": "2021-05-06T05:05:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Departed Facility in KARACHI - PAKISTAN"
        },
        {
          "timestamp": "2021-05-06T04:54:00",
          "location": {
            "address": {
              "addressLocality": "DUBAI - UNITED ARAB EMIRATES"
            }
          },
          "description": "Customs status updated"
        },
        {
          "timestamp": "2021-05-05T22:12:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Processed at KARACHI - PAKISTAN"
        },
        {
          "timestamp": "2021-05-05T19:29:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Shipment Accepted"
        },
        {
          "timestamp": "2021-05-05T19:29:00",
          "location": {
            "address": {
              "addressLocality": "KARACHI - PAKISTAN"
            }
          },
          "description": "Shipment picked up"
        }
      ]
    }
  ]
}';

        $status_codes = ['pre-transit', 'transit', 'delivered', 'failure', 'unknown'];

        $international_tracking_numbers = InternationalShipment::whereNotNull('international_tracking_number')->where('sync', 1)->pluck('international_tracking_number')->toArray();

        if(count($international_tracking_numbers) > 0){

            $international_tracking_numbers = implode(',',$international_tracking_numbers);

            dd($international_tracking_numbers);
            $current_search_trackings = array();
            $already_searched_trackings = array();
            foreach ($international_tracking_numbers as $international_tracking_number) {
                if(!in_array($already_searched_trackings, $international_tracking_number)){
//                    $current_search_trackings =
                }
            }
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
                        Log::info($response);
                        dd(123);
                        $response = json_decode($response);
                        $international_shipments = $response->shipments;
                        if(count($international_shipments) > 0){
                            foreach ($international_shipments as $international_shipment){
                                $intl_shipment = InternationalShipment::where('international_tracking_number', $international_shipment->id)->where('sync', 1)->first();
                                dd($intl_shipment);
                                if($intl_shipment){
                                    $shipment = $intl_shipment->shipment;
                                    dd($shipment);
                                }
                            }
                        }
                    }

                }catch (RequestException $exception){
                    Log::info($exception);
                }
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
