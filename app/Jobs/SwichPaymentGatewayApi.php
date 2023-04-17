<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Exception;
use App\Http\Models\Shipment;
class SwichPaymentGatewayApi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
        protected $shipments_id,$payment_option,$items;     
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shipments_id,$payment_option,$items)
    {
        $this->shipments_id    = $shipments_id;
        $this->payment_option  = $payment_option;
        $this->items           = $items;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $customer_details = Shipment::where('id',$this->shipments_id)->first();
        $total_amount = $customer_details->amount + $customer_details->fintech_charges;
        // Payment APi Parameters
        $shipments_id           = $this->shipments_id; //customerTransactionId
        $client_id              = '48b65b60d8364ad5b2ed1e31bc1ca399'; //clientId
        $paymentOption          = $this->payment_option; //categoryId
        $items                  = $this->items; //item
        $amount                 = $total_amount; //amount
        $customer_contact_no    = $customer_details->consignee_phone_number_1; //msisdn
        $customer_cnic          = ''; //cnic
        $customer_email         = $customer_details->consignee_email; //email
        //End Payment APi Parameters
        $base_url               = 'https://sandbox-api.swichnow.com';
        $grant_type             = '11FCEAE87B85864V';
        $client_secret          = '85e508d8aac54a8b8c81d7dfd3b542eeba72958ca83e4bb189ea4d445558d48c';
        $payin_url              = $base_url . '/gateway/payin/purchase/ewallet';
        $environment            = config('app.env');

        if($environment == 'production'){
            $authentication_url  = 'https://auth.swichnow.com/connect/token';
        }else{
            $authentication_url = 'https://sandbox-auth.swichnow.com/connect/token';
        }
        //Check mandaroy fields will not null
        if($shipments_id != '' && $client_id != '' && $paymentOption != '' && $items !='' && $amount != '' && $customer_contact_no != '' && $customer_cnic !='' && $customer_email != ''){
            //Amount Policy
            if ($amount < 10 || $amount > 5000) {
              return response()->json([
                'status'  => '400',
                'message' => 'Please Enter a Valid Amount, It Sholud be Greater than 10 Rs. or Less than 5,000 Rs.'
              ]);
            }

        $client = new Client();

        //Authentication APi Start
        $response = $client->post($authentication_url, [
            'form_params' => [
                'grant_type'    => $grant_type,
                'client_id'     => $client_id,
                'client_secret' => $client_secret
            ]
        ]);
        $body = json_decode($response->getBody());
        $access_token = $body->access_token;
    
        //Channel Selected
        if($paymentOption == 'Easypaisa'){
            $channel_id = 8;
        }
        else{
            $channel_id = 10;
        }

    // Call E-Wallet Payin API
    $response = $client->post($payin_url, [
        'headers' => [
            'Authorization' => 'Bearer ' . $access_token
        ],
        'json' => [
            'customerTransactionId' => $shipments_id,
            'clientId'              => $client_id,
            'categoryId'            => $paymentOption,
            'channelId'             => $channel_id,
            'item'                  => $items,
            'amount'                => $amount,
            'msisdn'                => $customer_contact_no,
            'cnic'                  => $customer_cnic,
            'email'                 => $customer_email
            // 'remoteIPAddress'       => 'your_remote_ip_address',
            // 'ucid'                  => 'your_ucid',
        ]
    ]);
            $body = json_decode($response->getBody());
            if ($response->getStatusCode() == 200 && $body->status == 'success') {
                return response()->json([
                    'status'  => '200',
                    'message' => 'Payment Done Successfully'
                  ]);
            } 
            else {
                return response()->json([
                    'status'  => '400',
                    'message' => 'Payment Not Done'
                  ]);
       
            }
        }
        else{
            return response()->json([
                'status'  => '400',
                'message' => 'Invalid Request'
            ]);
        }
    }
}
