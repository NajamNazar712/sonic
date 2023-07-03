<?php

namespace App\Helpers;
use App\PayFastTransactionDetials;
use App\Http\Models\Shipment;
use GuzzleHttp\Client;
use App\Http\Models\Admin\TraxPayTransaction;
class PayfastApiCall
{
    /**
     * Generate a random alphanumeric string
     *
     * @param int $length
     * @return string
     */
    public static function ApiCall($note_id,$shipment)
    {
        $rand = rand(1111,9999).time();
        $tray_pay_tansaction = new TraxPayTransaction();
        $tray_pay_tansaction->shipment_id      = $shipment;
        $tray_pay_tansaction->delivery_note_id = $note_id;
        $tray_pay_tansaction->unique_code      = $rand;
        $tray_pay_tansaction->payment_name_id  = '1';
        $tray_pay_tansaction->save();


        $environment = config('app.env');
        if($environment == 'production'){
            $url          = "https://pay.trax.pk/api/online-transaction-details";
            $payment_link = "https://pay.trax.pk/pay/$rand";
        }
        else if($environment == 'staging'){
            $url          = "https://pay-staging.trax.pk/api/online-transaction-details";
            $payment_link = "https://pay-staging.trax.pk/pay/$rand";
        }
        else{
            $url          = "http://192.168.0.210:80/api/online-transaction-details";
            $payment_link = "http://192.168.0.210/pay/$rand";
        }
        $payment_details = ['unique_key' =>$rand, 'url' => $url, 'payment_link' => $payment_link ];

        return  $payment_details;
    }
}