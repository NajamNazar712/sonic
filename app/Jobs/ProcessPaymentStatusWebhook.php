<?php

namespace App\Jobs;

use App\Http\Controllers\Webhook\PaymentStatusWebhookController;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessPaymentStatusWebhook implements ShouldQueue
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
        $this->queue = 'payment_status_webhook';
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
        $data['url'] = $this->shipment['url'];
        $data['tracking_number'] = $this->shipment['tracking_number'];
        $data['status'] = $this->shipment['status'];
        $user_id = $this->shipment['user_id'];
        $tracking_number = $this->shipment['tracking_number'];
        $status = $this->shipment['status'];
        $url = $this->shipment['url'];
        $date = $this->shipment['date_time'];
        $payment_id = $this->shipment['payment_id'];
        PaymentStatusWebhookController::webhook_dispatch($url, $user_id, $tracking_number, $status, $date, $payment_id);
    }
}
