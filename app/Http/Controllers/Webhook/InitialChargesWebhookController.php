<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Models\Shipment;
use App\Http\Models\Webhook\InitialChargesSubscription;
use App\Jobs\ProcessInitialChargesWebhook;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InitialChargesWebhookController extends Controller
{
    static public function webhook_subscription($shipment_id){

        $date = Carbon::now()->toDateTimeString();
        $shipment = Shipment::find($shipment_id);
        $user_id = $shipment->user_id;

        $subscriber = InitialChargesSubscription::where('user_id', $user_id)->where('status', 1);
        if($subscriber->exists()){
            $subscriber = $subscriber->first();
            $data = array();

            $data['user_id'] = $user_id;
            $data['url'] = $subscriber->url;
            $data['tracking_number'] = $shipment->tracking_number;
            $data['origin'] = $shipment->pickup_address->city->name;
            $data['destination'] = $shipment->consignee_city->name;
            $data['cod_amount'] = $shipment->amount;
            $data['actual_weight'] = $shipment->actual_weight;
            $data['chargeable_weight'] = $shipment->chargeable_weight;
            $data['weight_charges'] = $shipment->weight_charges;
            $data['cash_handling_charges'] = $shipment->cash_handling_charges;
            $data['insurance_charges'] = $shipment->insurance_charges;
            $data['fuel_surcharges'] = $shipment->fuel_surcharges;
            $data['gst'] = $shipment->gst;
            $total_charges = $shipment->weight_charges + $shipment->cash_handling_charges + $shipment->insurance_charges + $shipment->fuel_surcharges;
            $data['total_charges'] = $total_charges;
            $data['net_payable'] = $shipment->amount - $total_charges - $shipment->gst;
            dispatch(new ProcessInitialChargesWebhook($data));

        }
    }

    static public function webhook_dispatch($url, $user_id, $data){
        $attempts = 5;
        $client = new Client(['base_uri' => $url, 'http_errors' => FALSE, 'connect_timeout' => 3, 'timeout' => 3]);
        for($i = 0; $i < $attempts; $i++){
            try{

                $response = $client->post('', [
                    'form_params' => [
                        'tracking_number' => $data['tracking_number'],
                        'origin' => $data['origin'],
                        'destination' => $data['destination'],
                        'cod_amount' => $data['cod_amount'],
                        'actual_weight' => $data['actual_weight'],
                        'chargeable_weight' => $data['chargeable_weight'],
                        'weight_charges' => $data['weight_charges'],
                        'cash_handling_charges' => $data['cash_handling_charges'],
                        'insurance_charges' => $data['insurance_charges'],
                        'fuel_surcharges' => $data['fuel_surcharges'],
                        'gst' => $data['gst'],
                        'total_charges' => $data['total_charges'],
                        'net_payable' => $data['net_payable'],
                    ]
                ]);
                $status_code = $response->getStatusCode();
                if ($status_code != 200) {
                    if($i == 4){
                        InitialChargesSubscription::where('user_id', $user_id)->update(['status' => 0]);
                        break;
                    }
                    continue;
                }
                break;
            }
            catch(RequestException $e){
                if($i == 4){
                    InitialChargesSubscription::where('user_id', $user_id)->update(['status' => 0]);
                    break;
                }
                continue;
            }
        }

    }
}
