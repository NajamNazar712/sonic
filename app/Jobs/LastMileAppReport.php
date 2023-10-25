<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Traits\LastMileAppReportTrait;

class LastMileAppReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,LastMileAppReportTrait;
    protected $shipment_id;
    protected $delivery_note_id;
    protected $rider_id;
    protected $shipper_status_id;
    protected $added_at;
    protected $rider_delivery;
    protected $via;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shipment_id,$delivery_note_id,$rider_id,$shipper_status_id,$added_at,$rider_delivery,$via)
    {
        $this->shipment_id = $shipment_id;
        $this->delivery_note_id = $delivery_note_id;
        $this->rider_id = $rider_id;
        $this->shipper_status_id = $shipper_status_id;
        $this->added_at = $added_at;
        $this->rider_delivery = $rider_delivery;
        $this->via = $via;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->rider_wise_delivery_note($this->shipment_id,$this->delivery_note_id,$this->rider_id,$this->shipper_status_id,$this->added_at,$this->rider_delivery,$this->via);
    }
}
