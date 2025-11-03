<?php

namespace App\Console\Commands;

use App\Http\Controllers\CRM\CRMController;
use App\Http\Models\Admin\GlobalSettings;
use App\Models\TrackingActivity;
use Illuminate\Console\Command;

class TicketDraftingCRM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticketdraft:crm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $shipmentList = TrackingActivity::with('shipment')->where('status',0)->get();
        $globalSettings = GlobalSettings::where('type','ticket_draft_shipper_status')->first();
        $ticketDraftShipmentStatus = explode(',', $globalSettings->text);
        foreach($shipmentList as $list){           
            $rangeString = $list->shipment->pickup_city_etd->range ?? '0-0';
            $days = $list->shipment->updated_at->diffInDays(now());
            [$min, $max] = array_map(fn($v) => (int) trim($v), explode('-', $rangeString));
            $rangeArray = range($min, $max);

            $crmRequestCheck =  $list->shipment->crm_request()
                ->latest('created_at') // or 'id' if that's the auto-increment
                ->first();
            if(!in_array($days, $rangeArray)  && in_array($list->shipment->shipper_status_id, $ticketDraftShipmentStatus)){
                if($crmRequestCheck?->status_id != 1){
                    CRMController::add(1, 2, 5, 1, 5100, 4, $list->shipment->id, $list->shipment->user_id, 5100, NULL);
                }

                $list->status = 1;
                $list->save();
            }
        }
    }
}
