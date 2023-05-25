<?php

namespace App\Helpers;
use App\PayFastTransactionDetials;
use App\Http\Models\Shipment;
use GuzzleHttp\Client;
class PayfastApiCall
{
    /**
     * Generate a random alphanumeric string
     *
     * @param int $length
     * @return string
     */
    public static function ApiCall()
    {

        $environment = config('app.env');
        if($environment == 'production'){
            $rand = "";
            $url          = "";
            $payment_link = "";
        }
        else{
            $rand = rand(111111,999999);
            $url          = "http://192.168.0.210:8080/api/online-transaction-details";
            $payment_link = "http://192.168.0.210:8080/Pay-Online/$rand";
        }

        $payment_details = ['unique_key' =>$rand, 'url' => $url, 'payment_link' => $payment_link ];

        return   $payment_details;





        

    //     $customer_details = Shipment::where('shipments.id',$shipment)
    //     ->join('cities','shipments.consignee_city_id','cities.id')
    //     ->select(
    //     'shipments.tracking_number as TrankingID', 
    //     'shipments.consignee_name as Name',
    //     'shipments.consignee_address as Address',
    //     'cities.name as city_name')->first();

    //     if(!empty($customer_details)){

    //         $environment = config('app.env');
    //         if($environment == 'production'){
  
    //         }
    //         else{
    //           $rand = rand(111111,999999);
    //           $url          = "http://127.0.0.1:8000/api/online-transaction-details";
    //           $payment_link = "http://127.0.0.1:8000/Pay-Online/$rand";
    //         }
    //         //request body
    //         $request_body  = array(
    //             'tracking_no'       => $customer_details->TrankingID,
    //             'payment_link'      => $payment_link,
    //             'unique_key'        => $rand,
    //             'current_status'    => 'Out for Delivery',
    //         );
        
    //         $client = new Client();
    //         $response = $client->request('Post', $url, [
    //             'form_params' => $request_body,
    //         ]);

    //         $body = json_decode($response->getBody());
    //         return $payment_link;
    //     }
        
    }
}