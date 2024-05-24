<?php

namespace App\Jobs;

use App\Http\Models\Admin\GlobalSettings;
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
        $globalSettings = GlobalSettings::where('setting_value',1)
        ->whereIn('type',[
            'rv_disable_shippers_excluded_shippers',
            'rv_disable_shippers_only_shippers',
            'rv_disable_shippers_all_shippers'
        ])
        ->get(['setting_value','type','text']);

        $isShipperDisabled = 0;
        $excludedShippers = [];
        $onlyShippers = [];

        foreach ($globalSettings as $globalSetting) {
            switch ($globalSetting->type) {
                case 'rv_disable_shippers_all_shippers':
                    $isShipperDisabled = 1;
                    break;
                case 'rv_disable_shippers_excluded_shippers':
                    $excludedShippers = array_merge($excludedShippers, explode(',', $globalSetting->text));
                    break;
                case 'rv_disable_shippers_only_shippers':
                    $onlyShippers = array_merge($onlyShippers, explode(',', $globalSetting->text));
                    break;
            }
        }

        if (in_array($this->shipment['shipment_user_id'], $excludedShippers)) {//Mark Shipper Not Disabled if It's user id found in Excluded Shippers
            $isShipperDisabled = 0;
        }
        
        if (in_array($this->shipment['shipment_user_id'], $onlyShippers)) {//Mark Shipper Disabled if It's user id found in Only Shippers
            $isShipperDisabled = 1;
        }

        RvShipmentTicket::withTrashed()->updateOrCreate(
            ['shipment_id' => $this->shipment['shipment_id']],
            [
                'shipment_shipper_status_id' => $this->shipment['shipper_status_id'],
                'shipment_status_reason_id' => $this->shipment['status_reason_id'],
                'shipment_user_id' => $this->shipment['shipment_user_id'],
                'call_count' => $this->shipment['call_count'],
                'in_progress' => 0,
                'is_completed' => 0,
                'deleted_at' => null,
                'disabled_shipper' => $isShipperDisabled,
                'delete_reason' => null
            ]);
    }
}
