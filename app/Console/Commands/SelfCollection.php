<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\OrderManagementController;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\SelfCollectionShipment;
use App\Http\Models\Shipment;
use Illuminate\Console\Command;

class SelfCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipments:self_collection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Self Collection Shipments';

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
        $self_collection_shipments = Shipment::join('self_collection_shipments as scs', 'scs.shipment_id', '=', 'shipments.id')
            ->where('shipments.shipper_status_id' , 15)
            ->where('scs.status' , 0);
        if($self_collection_shipments->exists()){
            $self_collection_shipments = $self_collection_shipments->get();
            foreach ($self_collection_shipments as $self_collection_shipment){
                if($self_collection_shipment->consignee_city->hub_city->address != NULL){
                    $address = $self_collection_shipment->consignee_city->hub_city->address;
                    NotificationsController::send(75, $self_collection_shipment->shipment_id, $address);
                    $self_collection = SelfCollectionShipment::where('shipment_id', $self_collection_shipment->shipment_id)->first();
                    $self_collection->status = 1;
                    $self_collection->save();
                }
            }
        }
    }
}
