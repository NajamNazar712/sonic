<?php

namespace App\Jobs;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Shipment;
use App\ReturnConfirmationPendingSmsAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RCPSmsToConsignee implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $shipment_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shipment_id)
    {

        $this->queue = 'rcp_sms_to_consignee';
        $this->shipment_id = $shipment_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

       NotificationsController::send(169,$this->shipment_id);
       ReturnConfirmationPendingSmsAttempt::create(['shipment_id'=>$this->shipment_id,'status' => 0, 'count' => 1]);

    }
}
