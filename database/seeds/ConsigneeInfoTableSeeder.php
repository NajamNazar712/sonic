<?php

use App\Http\Models\ConsigneeInfo;
use App\Http\Models\Shipment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsigneeInfoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shipments = Shipment::select('user_id','consignee_city_id','consignee_name','consignee_address','consignee_phone_number_1','consignee_phone_number_2','consignee_email')->get();
        foreach ($shipments as $shipment){
            if(!ConsigneeInfo::where('shipper_id', $shipment->user_id)->where('phone_number_1', $shipment->consignee_phone_number_1)->exists()){
                DB::table('consignee_infos')->insert(array(
                    array('shipper_id' => $shipment->user_id, 'city_id' => $shipment->consignee_city_id, 'name' => $shipment->consignee_name, 'address' => $shipment->consignee_address, 'phone_number_1' => $shipment->consignee_phone_number_1, 'phone_number_2' => $shipment->consignee_phone_number_2, 'email' => $shipment->consignee_email),
                ));
            }

        }
    }
}
