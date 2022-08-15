<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessOneLinkDeliveryNoteShipment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $details)
    {
        $this->queue = 'one_link_shipments';
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $one_link_details = $this->details;
        foreach ($one_link_details as $one_link_detail){
            $shipment_id = $one_link_detail['shipment_id'];
            $delivery_note_id = $one_link_detail['delivery_note_id'];
        }
    }
}
