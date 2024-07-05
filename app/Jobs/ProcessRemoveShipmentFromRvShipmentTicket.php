<?php

namespace App\Jobs;

use App\RvShipmentTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessRemoveShipmentFromRvShipmentTicket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $shipmentId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shipmentId)
    {
        $this->queue = 'remove_shipment_from_rv_shipment_ticket';
        $this->shipmentId = $shipmentId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        RvShipmentTicket::where('shipment_id', $this->shipmentId)->delete();
    }
}
