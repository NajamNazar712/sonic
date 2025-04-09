<?php

namespace App\Jobs;

use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use App\Http\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Models\InterceptReBookRequest;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\RcpAssignedAgent;
use App\Http\Models\Admin\RcpAssignedShipment;
use App\Http\Models\Admin\RcpAssignedShipmentLog;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\RvTrait;

class HandleShipmentIntercept implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RvTrait;

    protected $shipmentJourneyData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $shipmentJourneyData)
    {
        $this->shipmentJourneyData = $shipmentJourneyData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ShipmentsJourneyController::add(
            $this->shipmentJourneyData[0], // shipment_id
            $this->shipmentJourneyData[1], // param2 (54 or 55)
            $this->shipmentJourneyData[2], // param3 (54 or 55)
            $this->shipmentJourneyData[3], // param4 (NULL)
            $this->shipmentJourneyData[4], // param5 (NULL)
            $this->shipmentJourneyData[5], // user_id
            $this->shipmentJourneyData[6]  // param7 (NULL)
        );
    }
}
