<?php

namespace App\Jobs;

use App\Http\Controllers\Webhook\InitialChargesWebhookController;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessInitialChargesWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $shipment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $shipment)
    {
        $this->queue = 'initial_charges_webhook';
        $this->shipment = $shipment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = array();
        $data['tracking_number'] = $this->shipment['tracking_number'];
        $data['origin'] = $this->shipment['origin'];
        $data['destination'] = $this->shipment['destination'];
        $data['cod_amount'] = $this->shipment['cod_amount'];
        $data['actual_weight'] = $this->shipment['actual_weight'];
        $data['chargeable_weight'] = $this->shipment['chargeable_weight'];
        $data['weight_charges'] = $this->shipment['weight_charges'];
        $data['cash_handling_charges'] = $this->shipment['cash_handling_charges'];
        $data['insurance_charges'] = $this->shipment['insurance_charges'];
        $data['fuel_surcharges'] = $this->shipment['fuel_surcharges'];
        $data['gst'] = $this->shipment['gst'];
        $data['total_charges'] = $this->shipment['total_charges'];
        $data['net_payable'] = $this->shipment['net_payable'];
        $user_id = $this->shipment['user_id'];
        $url = $this->shipment['url'];
        InitialChargesWebhookController::webhook_dispatch($url, $user_id, $data);
    }
}
