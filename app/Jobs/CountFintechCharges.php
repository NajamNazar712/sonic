<?php

namespace App\Jobs;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Models\Shipment;
use App\Http\Models\Admin\FintechCompany;
use App\Http\Models\Admin\UserFintectCharges;
use App\Http\Models\Admin\standard_fintech_charges;
use App\Http\Models\Admin\shipmentFintechCharges;
use App\Http\Models\Admin\TraxPayTransaction;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class CountFintechCharges implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $valid_shipment;
    protected $payment_link;
    protected $unique_key;
    protected $url;
    protected $trans_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($valid_shipment, $payment_link, $unique_key, $url,$trans_id)
    {
        return true;
        $this->queue = 'fintech_charges_count';
        $this->valid_shipment = $valid_shipment;
        $this->payment_link = $payment_link;
        $this->unique_key = $unique_key;
        $this->url = $url;
        $this->trans_id = $trans_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $valid_shipment = $this->valid_shipment;
        try {
            $shipment = Shipment::where('id', $valid_shipment)->first();
            $total_cod_amount = $shipment->amount;

            $check = true;
            if ($shipment->booking_type_id == 4) {
                if ($shipment->charges_mode_id == 2) {
                    $check = true;
                } else {
                    $check = false;
                }
            }

            if ($check == true) {
                $fintech_company = FintechCompany::where('id', '1');
                if ($fintech_company->exists()) {
                    $fintech_company_id = $fintech_company->first()->id;
                } else {
                    $fintech_company_id = '1';
                }
                $user_fintech_charges = UserFintectCharges::where('user_id', $shipment->user_id)->where('status', '1')->first();
                $standart_fintech_charges = standard_fintech_charges::where('id', '1')->first();
                if ($user_fintech_charges) {
                    $charges = $user_fintech_charges->fintech_charges;
                    $percentage = ($total_cod_amount / 100) * $charges;
                    $cal_fed = ($percentage / 100) * $standart_fintech_charges->standard_fed_charges;
                    $total_charges = round($percentage + $cal_fed, 2);
                    $charges_applicable = '1';
                } else {
                    $standard_charges = ($total_cod_amount / 100) * $standart_fintech_charges->standard_fintech_charges;
                    $standard_charges_FED = ($standard_charges / 100) * $standart_fintech_charges->standard_fed_charges;
                    $total_charges = round($standard_charges + $standard_charges_FED, 2);
                    $charges_applicable = '2';
                }
                $shipment_fintech_charges = new shipmentFintechCharges();
                $shipment_fintech_charges->shipment_id = $shipment->id;
                $shipment_fintech_charges->fintech_charges = $total_charges;
                $shipment_fintech_charges->applied_to = $charges_applicable;
                $shipment_fintech_charges->save();
                if ($charges_applicable == '1') {
                    $fintech_charges = 0;
                } else {
                    $fintech_charges = $total_charges;
                }
                $traxpaytransaction = new TraxPayTransaction();
                $traxpaytransaction::where('shipment_id', $valid_shipment)->update([
                    'link' => $this->payment_link,
                    'cod_amount' => $total_cod_amount,
                    'fintech_amount' => $fintech_charges,
                ]);
                $request_body = array(
                    'payment_link' => $this->payment_link,
                    'unique_code' => $this->unique_key,
                    'pay_type_id' => 1,
                    'fintech_company' => $fintech_company_id,
                    'cod_amount' => $total_cod_amount,
                    'fintech_amount' => $fintech_charges,
                    'trans_id' => $this->trans_id,
                );
                $options = [
                    'form_params' => $request_body,
                    'http_errors' => false,
                ];
                $client = new Client();
                $response = $client->request('Post', $this->url, $options);
                //Log::channel('trax_pay')->info('s ' . json_encode($response->getBody()->getContents()));
            }

        } catch (\Throwable $e) {
            Log::channel('trax_pay')->info('e ' . json_encode($e));
        }
    }

}