<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use ShiftOneLabs\LaravelSqsFifoQueue\Bus\SqsFifoQueueable;

use App\Http\Models\ShipmentsPaymentJourney;

class ProcessShipmentsPaymentJourney implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SqsFifoQueueable, SerializesModels;

    protected $entry;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $entry)
    {
        $this->connection = 'sqs-fifo';
        $this->messageGroupId = 'shipments_payment_journey';
        $this->entry = $entry;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $shipment_payment_journey = new ShipmentsPaymentJourney();

        $shipment_payment_journey->shipment_id = $this->entry['shipment_id'];
        $shipment_payment_journey->status_id = $this->entry['status_id'];
        $shipment_payment_journey->admin_id = $this->entry['admin_id'];
        $shipment_payment_journey->payable_remarks = $this->entry['payable_remarks'];
        $shipment_payment_journey->payment_id = $this->entry['payment_id'];

        $shipment_payment_journey->save();
    }
}
