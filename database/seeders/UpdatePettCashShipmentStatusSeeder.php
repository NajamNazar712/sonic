<?php

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Shipment;
use Illuminate\Database\Seeder;

class UpdatePettCashShipmentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
            $shipments = Shipment::where('user_id',1690)->whereIn('shipper_status_id',[1,2])->where('special_instructions', 'like', '%Petty Cash Statement%')->get();
        
            if(count($shipments) > 0){
            foreach($shipments as $shipment){
                              
                Shipment::where('id', $shipment->id)->update(['shipper_status_id' => 17, 'consignee_status_id' => 17]);
                ShipmentsJourneyController::add($shipment->id, 17, 17, NULL, NULL, NULL, 346, NULL, NULL, 1, NULL, NULL);
            }
        }
            
    }
        
}
