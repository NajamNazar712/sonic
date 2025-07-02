<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\PendingDispatch;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Exception;
use App\Http\Models\Shipment;
use Illuminate\Support\Facades\Cache;
use App\PayFastTransactionDetials;
class SwichPaymentGatewayApi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
        protected $shipments_id;     
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shipments_id )
    {
        $this->queue = 'switch_payment_gateway';
        $this->shipments_id    = $shipments_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        //Pay Fast Api Integration Payment Link Start
        $customer_details = Shipment::where('id',$this->shipments_id)->first();
        if(!empty($customer_details)){
            $url              = 'https://invoice.apps.net.pk:7088/api/merchant/invoice/create';
            $client_id        = 'b207f5c8-e8b9-11ed-898c-005056a4e164';
            $client_secret    = '3854b5d902f0fe5f7e9bca547aad92bc6bb8574ef1656e1788bbc41e41bc7834';

            //Parameters
            $email              = 'info@slgtrax.com';
            $recipient_email    = 'info@slgtrax.com';
            $Bill_cat           = 'Bill';
            $total_amount       =  $customer_details->amount + $customer_details->fintech_charges;
            $billing_month      =  date('Y-m');
            $description        = 'Payment for COD shipment '.$customer_details->tracking_number;
        
            //request body
            $request_body  = http_build_query([
                'customer_email'   => $email,
                'total_amount'     => $total_amount,
                'invoice_ref_id'   => $customer_details->tracking_number,
                'billing_month'    => $billing_month,
                'bill_category'    => $Bill_cat,
                'due_in_days'      => 10,
                'expires_in_days'  => 15,
                'description'      => $description,
                'recipient_email'  => $recipient_email
            ]);
        
            $client = new Client();
            $response = $client->request('Post', $url, [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $client_secret),
                ],
                'body' => $request_body,
            ]);
    
            $body       = $response->getBody()->getContents();
            $data       = json_decode($body, true);
            $dueDate    = date("Y-m-d", strtotime($data['due_date']));
            $expireDate = date("Y-m-d", strtotime($data['expiry_date']));
            $payfast    = new PayFastTransactionDetials();
            $payfast->invoice_key          = $data['invoice_key'];
            $payfast->bill_consumer_number = $data['bill_consumer_number'];
            $payfast->invoice_number       = $data['invoice_number'];
            $payfast->invoice_id           = $data['invoice_id'];
            $payfast->invoice_ref_id       = $data['invoice_ref_id'];
            $payfast->total_amount         = $data['total_amount'];
            $payfast->payment_link         = $data['payment_link'];
            $payfast->due_date             = $dueDate ;
            $payfast->expiry_date          = $expireDate;
            $payfast->save();

            $result = 42;

        return $result;


        }
        

        //Pay Fast Api Integration Payment Link End     

        /* 
        // Pay Fast APi Integration Url Redirection Start
        
        $payment_data = [];
        
        //Authentication
        $Url              = 'https://ipguat.apps.net.pk/Ecommerce/api/Transaction/GetAccessToken';
        $customer_details = Shipment::where('id',$this->shipments_id)->first();
        $total_amount     = $customer_details->amount + $customer_details->fintech_charges;
        $merchant_id      = '17727'; // this is demo marchant ID.
        $secured_key      = 'WOXN60IICwSRuLQ2CdLlD9uNCrlo';   // Secure Hash 
        $BASKET_ID        = $this->shipments_id;
        $TXNAMT           = $total_amount;
        //End

        //Authntication Api. 
        $client = new Client();
        $response = $client->post($Url, [
            'form_params' => [
                'merchant_id'   => $merchant_id,
                'secured_key'   => $secured_key,
                'BASKET_ID'     => $BASKET_ID,
                'TXNAMT'        => $TXNAMT,
            ]
        ]);
        $body = json_decode($response->getBody());
        $Access_token = $body->ACCESS_TOKEN;

        //redirect parametes to payfast portal
        $payment_data['MERCHANT_ID']    =  $merchant_id;
        $payment_data['MERCHANT_NAME']  = 'digiHS'; // repalce it with original name
        $payment_data['TOKEN']          =  $Access_token; 
        $payment_data['PROCCODE']       = '00'; // it always 00
        $payment_data['TXNAMT']         =  $TXNAMT;
        $payment_data['SUCCESS_URL']    = 'http://sonic.test/payfast-payment'; // replace with the actual route
        $payment_data['FAILURE_URL']    = 'http://sonic.test/payfast-payment'; // replace with the actual route
        $payment_data['BASKET_ID']      =  $BASKET_ID;
        $payment_data['ORDER_DATE']     =  date('Y-m-d');
        $payment_data['CHECKOUT_URL']   = 'http://sonic.test'; // repalce its with application hostname.
        //End

        //  dd($payment_data);
            return response()->json(['data' =>  $payment_data]);

        // Pay Fast APi Integration Url Redirection End
        */


        /* 
        // Swich Api Integration Start
   
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
       
        // Swich Api Integration Start      
    */
    }
}
