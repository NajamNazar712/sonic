<?php

namespace App\Jobs;

use App\RvShipmentTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessRvShipmentTicket implements ShouldQueue
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
        $this->queue = 'rv_shipment_ticket';
        $this->shipment = $shipment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        RvShipmentTicket::updateOrCreate(
            [
                'shipment_id' => $this->shipment['shipment_id']
            ],
            [
                'shipment_shipper_status_id' => $this->shipment['shipper_status_id'],
                'shipment_status_reason_id' => $this->shipment['status_reason_id'],
                'shipment_user_id' => $this->shipment['shipment_user_id'],
                'call_count' => $this->shipment['call_count'],
                'in_progress' => 0,
                'in_completed' => 0,
                'deleted_at' => null,
                'delete_reason' => null
            ]);
    }
}
