<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use ShiftOneLabs\LaravelSqsFifoQueue\Bus\SqsFifoQueueable;

use App\Http\Models\ShipmentsAirWaybillJourney;

class ProcessShipmentsAirWaybillJourney implements ShouldQueue
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
        $this->messageGroupId = 'shipments_air_waybill_journey';
        $this->entry = $entry;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $shipment_air_waybill_journey = new ShipmentsAirWaybillJourney();

        $shipment_air_waybill_journey->shipment_id = $this->entry['shipment_id'];
        $shipment_air_waybill_journey->user_type = $this->entry['user_type'];
        $shipment_air_waybill_journey->user_id = $this->entry['user_id'];

        $shipment_air_waybill_journey->save();
    }
}
