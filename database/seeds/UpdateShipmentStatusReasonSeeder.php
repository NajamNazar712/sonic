<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateShipmentStatusReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataToUpdate = [
            ['id' => 17, 'name' => "Hold on consignee's request"],
            ['id' => 18, 'name' => "Hold on shipper's request"]
        ];
        
        foreach ($dataToUpdate as $data) {
            DB::table('shipment_status_reason')
                ->updateOrInsert(
                    ['id' => $data['id']],
                    ['name' => $data['name']]
                );
        }
        
        
    }
}
