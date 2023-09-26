<?php
namespace App\Http\Traits;

use App\DeliveryNoteErrorLog;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Rider;
use App\RiderWiseDeliveryNote;
use App\RiderWiseDeliveryNoteShipment;
use App\RiderWiseDeliveryNoteSummary;
use Carbon\Carbon;
use phpDocumentor\Reflection\Types\Null_;

trait LastMileAppReportTrait
{

    public function createDeliveryNoteErrorLog($delivery_note_id, $shipment_id, $message) {

        DeliveryNoteErrorLog::create([
            'delivery_note_id' => $delivery_note_id,
            'shipment_id' => $shipment_id,
            'message' => $message,
        ]);
        
    }
     public function rider_wise_delivery_note($shipment_id, $delivery_note_id, $rider_id, $shipper_status_id,
                                                    $added_at, $rider_delivery, $via)
     {
         //via : 1=admin, 2=rider
         if ($via == 1) {
             $delivery_note = DeliveryNote::where('id', $delivery_note_id)->select('rider_id')->first();
             $rider_id = $delivery_note->rider_id;
             $rider = Rider::select('name as rider_name', 'trax_id')
                 ->where('id', $rider_id);

             $added_at1 = Carbon::now();
             $rider_delivery_date = $added_at1->toDateTimeString();
         }
         else
         {
             $rider_delivery_date = $rider_delivery->added_at;

             $rider = Rider::select('name as rider_name', 'trax_id')
                 ->where('id', $rider_id);
         }

         if($rider->exists()){
             $rider = $rider->first();
             $added_at_from = Carbon::parse($rider_delivery_date);
             $added_at_from = $added_at_from->startOfDay();
             $added_at_to = Carbon::parse($rider_delivery_date);
             $added_at_to = $added_at_to->endOfDay();
             $added_at_from = $added_at_from->toDateTimeString();
             $added_at_to = $added_at_to->toDateTimeString();

             $datetime = Carbon::parse($rider_delivery_date);
             $time = $datetime->format('H:i:s');
             $flag = false;

             $delivery_note_data = DeliveryNote::join('cities as c', 'c.id', 'delivery_notes.hub_id')
                 ->join('zones as z', 'c.zone_id', 'z.id')
                 ->where('delivery_notes.id', $delivery_note_id)
                 ->select('delivery_notes.created_at as created_at',
                     'delivery_notes.hub_id as hub_id', 'delivery_notes.shipments_count as total_shipments',
                     'c.name as hub_name', 'z.id as zone_id', 'z.name as zone_name','delivery_notes.created_at as delivery_note_creation_date')
                 ->first();

             $delivery_note_creation_date = Carbon::parse($delivery_note_data->delivery_note_creation_date);

             $delivery_note_creation_date = $delivery_note_creation_date->toDateString();
             $datetime = $datetime->toDateString(); // date at which rider mark status

             $check_summary = RiderWiseDeliveryNoteSummary::where('rider_id',$rider_id)
                 ->whereBetween('delivery_date',[$added_at_from,$added_at_to]);

             if ($datetime == $delivery_note_creation_date)
             {
                 if($check_summary->exists())
                 {
                     $check_summary = $check_summary->first();
                     $rwdnsum_id = $check_summary->id;

                     $existing_shipment = RiderWiseDeliveryNoteShipment::where('shipment_id',$shipment_id);
                     if($existing_shipment->exists())
                     {
                         $existing_shipment = $existing_shipment->orderBy('id','desc')->first();
                         $existing_time = $existing_shipment->updated_time;

                         if ($existing_time < '10:59:59') {
                             $check_summary->before_11_count = $check_summary->before_11_count + 1;
                         } elseif ($existing_time > '10:59:59' && $existing_time < '11:59:59') {
                             $check_summary->at_11_count = $check_summary->at_11_count - 1;
                         } elseif ($existing_time > '11:59:59' && $existing_time < '12:59:59') {
                             $check_summary->at_12_count = $check_summary->at_12_count - 1;
                         } elseif ($existing_time > '12:59:59' && $existing_time < '13:59:59') {
                             $check_summary->at_13_count = $check_summary->at_13_count - 1;
                         } elseif ($existing_time > '13:59:59' && $existing_time < '14:59:59') {
                             $check_summary->at_14_count = $check_summary->at_14_count - 1;
                         } elseif ($existing_time > '14:59:59' && $existing_time < '15:59:59') {
                             $check_summary->at_15_count = $check_summary->at_15_count - 1;
                         } elseif ($existing_time > '15:59:59' && $existing_time < '16:59:59') {
                             $check_summary->at_16_count = $check_summary->at_16_count - 1;
                         } elseif ($existing_time > '16:59:59' && $existing_time < '17:59:59') {
                             $check_summary->at_17_count = $check_summary->at_17_count - 1;
                         } elseif ($existing_time > '17:59:59' && $existing_time < '18:59:59') {
                             $check_summary->at_18_count = $check_summary->at_18_count - 1;
                         } elseif ($existing_time > '18:59:59' && $existing_time < '19:59:59') {
                             $check_summary->at_19_count = $check_summary->at_19_count - 1;
                         } elseif ($existing_time > '19:59:59' && $existing_time < '20:59:59') {
                             $check_summary->at_20_count = $check_summary->at_20_count - 1;
                         } elseif ($existing_time > '20:59:59' && $existing_time < '21:59:59') {
                             $check_summary->at_21_count = $check_summary->at_21_count - 1;
                         } elseif ($existing_time > '21:59:59' && $time < '22:59:59') {
                             $check_summary->at_22_count = $check_summary->at_22_count - 1;
                         } elseif ($existing_time > '22:59:59' && $existing_time < '23:59:59') {
                             $check_summary->after_23_count = $check_summary->after_23_count - 1;
                         }

                         $flag = true;
                     }

                     if (!$flag)
                     {
                         $check_summary->shipment_update_count = $check_summary->shipment_update_count + 1;
                         $check_summary->via_rider_count = $check_summary->via_rider_count + 1;
                     }

                     if ($time < '10:59:59') {
                         $check_summary->before_11_count = $check_summary->before_11_count + 1;
                     } elseif ($time > '10:59:59' && $time < '11:59:59') {
                         $check_summary->at_11_count = $check_summary->at_11_count + 1;
                     } elseif ($time > '11:59:59' && $time < '12:59:59') {
                         $check_summary->at_12_count = $check_summary->at_12_count + 1;
                     } elseif ($time > '12:59:59' && $time < '13:59:59') {
                         $check_summary->at_13_count = $check_summary->at_13_count + 1;
                     } elseif ($time > '13:59:59' && $time < '14:59:59') {
                         $check_summary->at_14_count = $check_summary->at_14_count + 1;
                     } elseif ($time > '14:59:59' && $time < '15:59:59') {
                         $check_summary->at_15_count = $check_summary->at_15_count + 1;
                     } elseif ($time > '15:59:59' && $time < '16:59:59') {
                         $check_summary->at_16_count = $check_summary->at_16_count + 1;
                     } elseif ($time > '16:59:59' && $time < '17:59:59') {
                         $check_summary->at_17_count = $check_summary->at_17_count + 1;
                     } elseif ($time > '17:59:59' && $time < '18:59:59') {
                         $check_summary->at_18_count = $check_summary->at_18_count + 1;
                     } elseif ($time > '18:59:59' && $time < '19:59:59') {
                         $check_summary->at_19_count = $check_summary->at_19_count + 1;
                     } elseif ($time > '19:59:59' && $time < '20:59:59') {
                         $check_summary->at_20_count = $check_summary->at_20_count + 1;
                     } elseif ($time > '20:59:59' && $time < '21:59:59') {
                         $check_summary->at_21_count = $check_summary->at_21_count + 1;
                     } elseif ($time > '21:59:59' && $time < '22:59:59') {
                         $check_summary->at_22_count = $check_summary->at_22_count + 1;
                     } elseif ($time > '22:59:59' && $time < '23:59:59') {
                         $check_summary->after_23_count = $check_summary->after_23_count + 1;
                     }


                     $check_existing_note = RiderWiseDeliveryNote::where('delivery_note_id',$delivery_note_id);
                     if (!$check_existing_note->exists())
                     {
                         $check_summary->delivery_note_count = $check_summary->delivery_note_count + 1;
                         $check_summary->delivery_note_shipments_count = $check_summary->delivery_note_shipments_count + $delivery_note_data->total_shipments;

                         $new_delivery_note = new RiderWiseDeliveryNote();
                         $new_delivery_note->rwdnsum_id = $rwdnsum_id;
                         $new_delivery_note->delivery_note_id = $delivery_note_id;
                         $new_delivery_note->delivery_note_created_at = $delivery_note_data->created_at;
                         $new_delivery_note->shipment_update_count = 1;
                         $new_delivery_note->hub_id = $delivery_note_data->hub_id;
                         $new_delivery_note->hub_name = $delivery_note_data->hub_name;
                         $new_delivery_note->zone_id = $delivery_note_data->zone_id;
                         $new_delivery_note->zone_name = $delivery_note_data->zone_name;
                         $new_delivery_note->save();

                         $new_delivery_note_shipment = new RiderWiseDeliveryNoteShipment();
                         $new_delivery_note_shipment->rwdnsum_id = $rwdnsum_id;
                         $new_delivery_note_shipment->rwdn_id = $new_delivery_note->id;
                         $new_delivery_note_shipment->shipment_id = $shipment_id;
                         $new_delivery_note_shipment->shipper_status_id = $shipper_status_id;
                         $new_delivery_note_shipment->updated_time = $time;
                         $new_delivery_note_shipment->updated_via = $via;
                         $new_delivery_note_shipment->save();
                     }
                     else
                     {
                         $check_existing_note = $check_existing_note->first();

                         if (!$flag)
                         {
                             $check_existing_note->shipment_update_count = $check_existing_note->shipment_update_count + 1;
                             $check_existing_note->save();
                         }

                         $new_delivery_note_shipment = new RiderWiseDeliveryNoteShipment();
                         $new_delivery_note_shipment->rwdnsum_id = $rwdnsum_id;
                         $new_delivery_note_shipment->rwdn_id = $check_existing_note->id;
                         $new_delivery_note_shipment->shipment_id = $shipment_id;
                         $new_delivery_note_shipment->shipper_status_id = $shipper_status_id;
                         $new_delivery_note_shipment->updated_time = $time;
                         $new_delivery_note_shipment->updated_via = $via;
                         $new_delivery_note_shipment->save();
                     }

                     $check_summary->save();
                 }
                 else
                 {
                     $new_summary = new RiderWiseDeliveryNoteSummary();
                     $new_summary->delivery_date = $rider_delivery_date ?? '00:00:00 00:00:00';
                     $new_summary->rider_id = $rider_id;
                     $new_summary->trax_id = $rider->trax_id ?? '';
                     $new_summary->rider_name = $rider->rider_name ?? '';
                     $new_summary->shipment_update_count = 1;
                     $new_summary->delivery_note_count = 1;
                     $new_summary->delivery_note_shipments_count = $delivery_note_data->total_shipments ?? 0;
                     $new_summary->via_rider_count = 1;

                     if ($time < '10:59:59') {
                         $new_summary->before_11_count = 1;
                     } elseif ($time > '10:59:59' && $time < '11:59:59') {
                         $new_summary->at_11_count = 1;
                     } elseif ($time > '11:59:59' && $time < '12:59:59') {
                         $new_summary->at_12_count = 1;
                     } elseif ($time > '12:59:59' && $time < '13:59:59') {
                         $new_summary->at_13_count = 1;
                     } elseif ($time > '13:59:59' && $time < '14:59:59') {
                         $new_summary->at_14_count = 1;
                     } elseif ($time > '14:59:59' && $time < '15:59:59') {
                         $new_summary->at_15_count = 1;
                     } elseif ($time > '15:59:59' && $time < '16:59:59') {
                         $new_summary->at_16_count = 1;
                     } elseif ($time > '16:59:59' && $time < '17:59:59') {
                         $new_summary->at_17_count = 1;
                     } elseif ($time > '17:59:59' && $time < '18:59:59') {
                         $new_summary->at_18_count = 1;
                     } elseif ($time > '18:59:59' && $time < '19:59:59') {
                         $new_summary->at_19_count = 1;
                     } elseif ($time > '19:59:59' && $time < '20:59:59') {
                         $new_summary->at_20_count = 1;
                     } elseif ($time > '20:59:59' && $time < '21:59:59') {
                         $new_summary->at_21_count = 1;
                     } elseif ($time > '21:59:59' && $time < '22:59:59') {
                         $new_summary->at_22_count = 1;
                     } elseif ($time > '22:59:59' && $time < '23:59:59') {
                         $new_summary->after_23_count = 1;
                     }
                     $new_summary->save();

                     $new_delivery_note = new RiderWiseDeliveryNote();
                     $new_delivery_note->rwdnsum_id = $new_summary->id;
                     $new_delivery_note->delivery_note_id = $delivery_note_id;
                     $new_delivery_note->delivery_note_created_at = $delivery_note_data->created_at;
                     $new_delivery_note->shipment_update_count = 1;
                     $new_delivery_note->hub_id = $delivery_note_data->hub_id;
                     $new_delivery_note->hub_name = $delivery_note_data->hub_name;
                     $new_delivery_note->zone_id = $delivery_note_data->zone_id;
                     $new_delivery_note->zone_name = $delivery_note_data->zone_name;
                     $new_delivery_note->save();

                     $new_delivery_note_shipment = new RiderWiseDeliveryNoteShipment();
                     $new_delivery_note_shipment->rwdnsum_id = $new_summary->id;
                     $new_delivery_note_shipment->rwdn_id = $new_delivery_note->id;
                     $new_delivery_note_shipment->shipment_id = $shipment_id;
                     $new_delivery_note_shipment->shipper_status_id = $shipper_status_id;
                     $new_delivery_note_shipment->updated_time = $time;
                     $new_delivery_note_shipment->updated_via = $via;
                     $new_delivery_note_shipment->save();

                 }
             }
         }
    }
}