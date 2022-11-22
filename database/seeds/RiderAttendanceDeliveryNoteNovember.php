<?php

use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Admin\Attendance\EmployeeAttendanceActionLog;
use App\Http\Models\Rider;
use App\Http\Models\ShipmentOtp;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RiderAttendanceDeliveryNoteNovember extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $from = Carbon::createFromFormat('Y-m-d H:i:s', '2022-11-22 00:00:00');
        $to = Carbon::createFromFormat('Y-m-d H:i:s', '2022-11-22 23:23:59');

        $delivery_notes = \App\Http\Models\Admin\DeliveryNote::whereBetween('created_at', [$from,$to]);
        if($delivery_notes->exists()){
            $delivery_notes = $delivery_notes->pluck('id')->toArray();

            $delivery_note_shipments = \App\Http\Models\Admin\DeliveryNoteShipment::whereIn('delivery_note_id', $delivery_notes);

            if($delivery_note_shipments->exists()){
                $delivery_note_shipments = $delivery_note_shipments->pluck('shipment_id')->toArray();

                $shipments = \App\Http\Models\Shipment::whereIn('id', $delivery_note_shipments)->where('amount', 0);

                if($shipments->exists()){
                    $shipments = $shipments->pluck('id')->toArray();
                    dd(count($shipments));
//                    foreach ($shipments as $shipment){
//                        $shipment_otp = ShipmentOtp::where('shipment_id', $shipment);
//                        $otp = mt_rand(100000, 999999);
//                        $dbf_otp = mt_rand(100000, 999999);
//                        if ($shipment_otp->exists()) {
//                            $shipment_otp = $shipment_otp->first();
//                        } else {
//                            $shipment_otp = new ShipmentOtp();
//                            $shipment_otp->shipment_id = $shipment;
//                        }
//                        $shipment_otp->otp = $otp;
//                        $shipment_otp->dbf_otp = $dbf_otp;
//                        $shipment_otp->rider_id = null;
//                        $shipment_otp->latitude = null;
//                        $shipment_otp->longitude = null;
//                        $shipment_otp->save();
//                    }
                }
            }
        }
    }
}
