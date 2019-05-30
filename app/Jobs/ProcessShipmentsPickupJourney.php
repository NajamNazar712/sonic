<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use ShiftOneLabs\LaravelSqsFifoQueue\Bus\SqsFifoQueueable;

use App\Http\Models\ShipmentsPickupJourney;

class ProcessShipmentsPickupJourney implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SqsFifoQueueable, SerializesModels;

    protected $entry;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->connection = 'sqs-fifo';
        $this->messageGroupId = 'shipments_pickup_journey';
        $this->entry = $entry;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $shipment_pickup_journey = new ShipmentsPickupJourney();

        $shipment_pickup_journey->shipment_id = $this->entry['shipment_id'];
        $shipment_pickup_journey->status_id = $this->entry['status_id'];
        $shipment_pickup_journey->admin_id = $this->entry['admin_id'];
        $shipment_pickup_journey->reference_1_id = $this->entry['reference_1_id'];
        $shipment_pickup_journey->reference_2_id = $this->entry['reference_2_id'];

        $shipment_pickup_journey->save();
    }
}
