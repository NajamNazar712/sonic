<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Models\Shipment;
use App\Http\Models\Webhook\FinalChargesSubscription;
use App\Jobs\ProcessFinalChargesWebhook;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FinalChargesWebhookController extends Controller
{
    static public function webhook_subscription($shipment_id){

        $date = Carbon::now()->toDateTimeString();
        $shipment = Shipment::find($shipment_id);
        $user_id = $shipment->user_id;

        $subscriber = FinalChargesSubscription::where('user_id', $user_id)->where('status', 1);
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
            if($shipment->shipper_status_id == 20)
            {
                $cash_handling_charges = 0;
                $replacement_charges = 0;
                $try_buy_charges = 0;
            }
            else{
                $cash_handling_charges = $shipment->cash_handling_charges;
                $replacement_charges = $shipment->replacement_charges;
                $try_buy_charges = $shipment->try_and_buy_charges;
            }
            $data['cash_handling_charges'] = $cash_handling_charges;
            $data['replacement_charges'] = $replacement_charges;
            $data['try_buy_charges'] = $try_buy_charges;
            $data['insurance_charges'] = $shipment->insurance_charges;
            $data['fuel_surcharges'] = $shipment->fuel_surcharge;
            $data['packaging_charges'] = $shipment->packaging_charges;
            $data['return_charges'] = $shipment->return_charges;
            $data['intercept_charges'] = $shipment->intercept_charges;
            $data['nsa_charges'] = $shipment->nsa_osa_charges;
            $total_charges = $shipment->weight_charges + $cash_handling_charges + $shipment->insurance_charges + $shipment->fuel_surcharge + $shipment->packaging_charges + $shipment->return_charges + $replacement_charges + $try_buy_charges + $shipment->intercept_charges + $shipment->nsa_osa_charges;
            $gst = 0;
            $gst = (($total_charges * $shipment->pickup_address->city->zone->gst)) ?? 0;
            $data['gst'] = $gst;
            $data['total_charges'] = $total_charges;
            $data['net_payable'] = $shipment->amount - $total_charges - $gst;
            dispatch(new ProcessFinalChargesWebhook($data));

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
                        'cod_amount' => $data['cod_amount'] ?? 0,
                        'actual_weight' => $data['actual_weight'] ?? 0,
                        'chargeable_weight' => $data['chargeable_weight'] ?? 0,
                        'weight_charges' => $data['weight_charges'] ?? 0,
                        'cash_handling_charges' => $data['cash_handling_charges'] ?? 0,
                        'insurance_charges' => $data['insurance_charges'] ?? 0,
                        'fuel_surcharges' => $data['fuel_surcharges'] ?? 0,
                        'packaging_charges' => $data['packaging_charges'] ?? 0,
                        'return_charges' => $data['return_charges'] ?? 0,
                        'replacement_charges' => $data['replacement_charges'] ?? 0,
                        'try_buy_charges' => $data['try_buy_charges'] ?? 0,
                        'intercept_charges' => $data['intercept_charges'] ?? 0,
                        'nsa_charges' => $data['nsa_charges'] ?? 0,
                        'gst' => $data['gst'] ?? 0,
                        'total_charges' => $data['total_charges'] ?? 0,
                        'net_payable' => $data['net_payable'] ?? 0,
                    ]
                ]);
                $status_code = $response->getStatusCode();
                if ($status_code != 200) {
                    if($i == 4){
                        FinalChargesSubscription::where('user_id', $user_id)->update(['status' => 0]);
                        break;
                    }
                    continue;
                }
                break;
            }
            catch(RequestException $e){
                if($i == 4){
                    FinalChargesSubscription::where('user_id', $user_id)->update(['status' => 0]);
                    break;
                }
                continue;
            }
        }

    }
}
