<?php

namespace App\Console\Commands;

use App\Http\Controllers\CRM\CRMController;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoComplaintHighAging extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:autohighaging';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make CRM request of High Aging for shipment 15-days';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $shipments = Shipment::whereNotIn('shipper_status_id',[36,37,38,14,25,31,51,18])->get();

        foreach ($shipments as $shipment) {

            
            $arrival_status = ShipmentsJourney::where('shipment_id',$shipment->id)->where('shipper_status_id',2)->whereDate('created_at', '>=', Carbon::today()->subDays(15));
            $delivery_status = ShipmentsJourney::where('shipment_id',$shipment->id)->where('shipper_status_id',$shipment->shipper_status_id);
            if($arrival_status->exists() && $delivery_status->exists()){
                $delivery_status = $delivery_status->latest()->first();
                $arrival_status = $arrival_status->latest()->first();
                $start_date = $arrival_status->created_at;
                $end_date = $delivery_status->created_at;
                $difference = $start_date->diffInDays($end_date);
    
                if($difference > 14){
                    //launch request
                    $nature_id = 1;
                    $complaint_id = 34;
                    $description = 'Auto Complaint Locking High Aging';
                    $launched_by = 0;
                    $user_id = 1910; //default user
                    
                    $crm_req = CrmRequest::where('shipment_id',$shipment->id)->where('case_nature_type_id',34);
                    if(!$crm_req->exists()){
                        CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment->id, $shipment->shipper_id, null, $description);
                    }
                      
                }elseif($shipment->shipper_status_id == 2){
                    //launch request
                    $nature_id = 1;
                    $complaint_id = 34;
                    $description = 'Auto Complaint Locking High Aging';
                    $launched_by = 0;
                    $user_id = 1910; //default user

                    $crm_req = CrmRequest::where('shipment_id',$shipment->id)->where('case_nature_type_id',34);
                    if(!$crm_req->exists()){
                        CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment->id, $shipment->shipper_id, null, $description);
                    }
                }
            }



        }
    }
}
