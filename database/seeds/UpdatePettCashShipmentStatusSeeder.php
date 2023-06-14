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
        $settings = GlobalSettings::where('type','foc_account_tag');

     
        if ($settings->exists()) {
            $settings = $settings->first();
            $user_ids = array_map('intval', explode(',', $settings->text));
          
            $shipments = Shipment::whereIn('user_id',$user_ids)->whereIn('shipper_status_id',[1,2])->get();
           
            foreach($shipments as $shipment){
                              
                Shipment::where('id', $shipment->id)->update(['shipper_status_id' => 17, 'consignee_status_id' => 17]);
                ShipmentsJourneyController::add($shipment->id, 17, 17, NULL, NULL, NULL, 346, NULL, NULL, 1, NULL, NULL);
            }
            
        }
        
    }
}
