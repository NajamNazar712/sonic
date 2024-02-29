<?php

namespace App\Jobs;

use App\DeliveryNoteErrorLog;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\InternationalShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentOrderDate;
use App\Http\Models\ShipmentShipperReference;
use App\Http\Models\Shipper\User;
use App\Http\Models\SubstituteUserShipment;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\City;
use App\Http\Models\Rider;
use Carbon\Carbon;
use App\Http\Models\ShipmentDetail;
use App\RiderWiseDeliveryNote;
use App\RiderWiseDeliveryNoteShipment;
use App\RiderWiseDeliveryNoteSummary;
use Exception;

class LastMileApp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $riderWise;

    protected $shipment_id;
    protected $delivery_note_id;
    protected $rider_id;
    protected $shipper_status_id;
    protected $added_at;
    protected $rider_delivery;
    protected $via;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $shipment_id, int $delivery_note_id, int $rider_id,int $shipper_status_id, string $added_at, object $rider_delivery, int $via)
    {
        $this->queue = 'last_mile_count_app';
        $this->shipment_id = $shipment_id;
        $this->delivery_note_id = $delivery_note_id;
        $this->rider_id = $rider_id;
        $this->shipper_status_id = $shipper_status_id;
        $this->added_at = $added_at;
        $this->rider_delivery = $rider_delivery;
        $this->via = $via;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //via : 1=admin, 2=rider
        try {
            if ($this->via == 1) {
                $delivery_note = DeliveryNote::where('id', $this->delivery_note_id)->select('rider_id')->first();
                $rider_id = $delivery_note->rider_id;
                $rider = Rider::select('name as rider_name', 'trax_id')
                    ->where('id', $rider_id);
    
                $added_at1 = Carbon::now()->toDateTimeString();
                $rider_delivery_date = $added_at1;
            }
            else
            {
                $rider_delivery_date = $this->rider_delivery->added_at;
    
                $rider = Rider::select('name as rider_name', 'trax_id')
                    ->where('id', $this->rider_id);
            }
            
            if($rider->exists()){
                            $rider = $rider->first();
    
                $today = Carbon::today();
    
                $time = Carbon::parse($rider_delivery_date)->format('H:i:s');
                $delivery_note_data = DeliveryNote::join('cities as c', 'c.id', 'delivery_notes.hub_id')
                    ->join('zones as z', 'c.zone_id', 'z.id')
                    ->select('delivery_notes.created_at as created_at', 'delivery_notes.hub_id as hub_id', 'delivery_notes.shipments_count as total_shipments', 'c.name as hub_name', 'z.id as zone_id', 'z.name as zone_name','delivery_notes.created_at as delivery_note_creation_date')
                    ->where('delivery_notes.id', $this->delivery_note_id)
                    ->whereDate('delivery_notes.created_at', $today);
               if($delivery_note_data->exists()){
                    $delivery_note_data = $delivery_note_data->first();
                    
                    $check_note_id_delivery = RiderWiseDeliveryNote::where('delivery_note_id',$this->delivery_note_id)->whereDate('delivery_note_created_at', $today);
                    $check_summary = RiderWiseDeliveryNoteSummary::where('id',$check_note_id_delivery->latest()->pluck('rwdnsum_id'))->where('rider_id',$rider_id)->whereDate('delivery_date', $today);
                    
                    if($check_summary->exists() && $check_note_id_delivery->exists())
                    {
                       $check_summary = $check_summary->first();
                       $check_summary_delivery = $check_note_id_delivery->first();
                       $rwdnsum_id = $check_summary->id; 
                       $finishTime = Carbon::parse($check_summary->delivery_date);                    
                       $totalDuration = $finishTime->diffInHours($time);
                       $riderWiseShipmentNote = RiderWiseDeliveryNoteShipment::where('shipment_id',$shipment_id)->where('rwdnsum_id',$rwdnsum_id)->whereDate('created_at', $today);
                       
                       if(!$riderWiseShipmentNote->exists())
                       {
                           $new_delivery_note_shipment = new RiderWiseDeliveryNoteShipment();
                           $new_delivery_note_shipment->rwdnsum_id = $rwdnsum_id;
                           $new_delivery_note_shipment->rwdn_id = $check_summary_delivery->id;
                           $new_delivery_note_shipment->shipment_id = $this->shipment_id;
                           $new_delivery_note_shipment->shipper_status_id = $this->shipper_status_id;
                           $new_delivery_note_shipment->updated_time = $time;
                           $new_delivery_note_shipment->updated_via = $this->via;
                           $new_delivery_note_shipment->save();
                           self::countAdd($time,$check_summary);
                           $check_summary->via_rider_count = $check_summary->via_rider_count + 1;
                           $check_summary->shipment_update_count = $check_summary->shipment_update_count + 1;
                           $check_summary->save();
                           return true;
                       }
                       
                       
                       if($totalDuration <= 0){
                            return true;   
                       }else{
                           $update_delivery_note_shipment = $riderWiseShipmentNote->first();
                           $minusTime = $update_delivery_note_shipment->updated_time;
                           $update_delivery_note_shipment->id = $update_delivery_note_shipment->id;  
                           $update_delivery_note_shipment->shipper_status_id = $this->shipper_status_id;
                           $update_delivery_note_shipment->updated_time = $time;
                           $update_delivery_note_shipment->updated_via = $this->via;
                           $update_delivery_note_shipment->save();
                           self::countSub($minusTime,$check_summary);
                       }
                       self::countAdd($time,$check_summary);
                       return true;
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
                       
                        
                        $new_summary->save();
                        self::countAdd($time,$new_summary);
                        
                        $new_delivery_note = new RiderWiseDeliveryNote();
                        $new_delivery_note->rwdnsum_id = $new_summary->id;
                        $new_delivery_note->delivery_note_id = $this->delivery_note_id;
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
                        $new_delivery_note_shipment->shipment_id = $this->shipment_id;
                        $new_delivery_note_shipment->shipper_status_id = $this->shipper_status_id;
                        $new_delivery_note_shipment->updated_time = $time;
                        $new_delivery_note_shipment->updated_via = $this->via;
                        $new_delivery_note_shipment->save();
                    }
                }
    
    
            }
        }
        catch(Exception $exception) {
            $body = 'Error Exception.<br/>' . json_encode($exception->getMessage());
            DeliveryNoteErrorLog::create([
                'delivery_note_id' => $this->delivery_note_id,
                'shipment_id' => $this->shipment_id,
                'message' => $body,
            ]);
        }
        

    }

    private function rider_wise_delivery_note($shipment_id, $delivery_note_id, $rider_id, $shipper_status_id,
                                                    $added_at, $rider_delivery, $via)
     {
         
         
    }

    private function countAdd($time,$check_summary)
    {   
		if ($time <= '10:59:59') {
			$check_summary->before_11_count = $check_summary->before_11_count + 1;
             $check_summary->save();
		} elseif ($time > '10:59:59' && $time <= '11:59:59') {
			$check_summary->at_11_count = $check_summary->at_11_count + 1;
             $check_summary->save();
            
		} elseif ($time > '11:59:59' && $time <= '12:59:59') {
			$check_summary->at_12_count = $check_summary->at_12_count + 1;
              $check_summary->save();
            
		} elseif ($time > '12:59:59' && $time <= '13:59:59') {
			$check_summary->at_13_count = $check_summary->at_13_count + 1;
              $check_summary->save();
            
		} elseif ($time > '13:59:59' && $time <= '14:59:59') {
			$check_summary->at_14_count = $check_summary->at_14_count + 1;
              $check_summary->save();
            
		} elseif ($time > '14:59:59' && $time <= '15:59:59') {
			$check_summary->at_15_count = $check_summary->at_15_count + 1;
             $check_summary->save();
            
		} elseif ($time > '15:59:59' && $time <= '16:59:59') {
			$check_summary->at_16_count = $check_summary->at_16_count + 1;
             $check_summary->save();
            
		} elseif ($time > '16:59:59' && $time <= '17:59:59') {
			$check_summary->at_17_count = $check_summary->at_17_count + 1;
              $check_summary->save();
            
		} elseif ($time > '17:59:59' && $time <= '18:59:59') {
			$check_summary->at_18_count = $check_summary->at_18_count + 1;
              $check_summary->save();
            
		} elseif ($time > '18:59:59' && $time <= '19:59:59') {
			$check_summary->at_19_count = $check_summary->at_19_count + 1;
             $check_summary->save();
            
		} elseif ($time > '19:59:59' && $time <= '20:59:59') {
			$check_summary->at_20_count = $check_summary->at_20_count + 1;
             $check_summary->save();
            
		} elseif ($time > '20:59:59' && $time <= '21:59:59') {
			$check_summary->at_21_count = $check_summary->at_21_count + 1;
             $check_summary->save();
            
		} elseif ($time > '21:59:59' && $time <= '22:59:59') {
			$check_summary->at_22_count = $check_summary->at_22_count + 1;
             $check_summary->save();
            
		} elseif ($time > '22:59:59' && $time <= '23:59:59') {
			$check_summary->after_23_count = $check_summary->after_23_count + 1;
             $check_summary->save();
		}
    }

    private function countSub($time,$check_summary)
    {

        if(!empty($check_summary->before_11_count) && $time <= '10:59:59')
        {
            $check_summary->before_11_count = $check_summary->before_11_count - 1;
            $check_summary->save(); 
        }
        elseif (!empty($check_summary->at_11_count) && $time > '10:59:59' && $time <= '11:59:59') {
            $check_summary->at_11_count = $check_summary->at_11_count - 1;
            $check_summary->save();
        } elseif (!empty($check_summary->at_12_count) && $time > '11:59:59' && $time <= '12:59:59') {
            $check_summary->at_12_count = $check_summary->at_12_count - 1;
            $check_summary->save();
        } elseif (!empty($check_summary->at_13_count) && $time > '12:59:59' && $time <= '13:59:59') {
            $check_summary->at_13_count = $check_summary->at_13_count - 1;
            $check_summary->save();
        } elseif (!empty($check_summary->at_14_count) && $time > '13:59:59' && $time <= '14:59:59') {
            $check_summary->at_14_count = $check_summary->at_14_count - 1;
            $check_summary->save();
        } elseif (!empty($check_summary->at_15_count) && $time > '14:59:59' && $time <= '15:59:59') {
            $check_summary->at_15_count = $check_summary->at_15_count - 1;
            $check_summary->save();
        } elseif (!empty($check_summary->at_16_count) && $time > '15:59:59' && $time <= '16:59:59') {
            $check_summary->at_16_count = $check_summary->at_16_count - 1;
            $check_summary->save();
        } elseif (!empty($check_summary->at_17_count) && $time > '16:59:59' && $time <= '17:59:59') {
            $check_summary->at_17_count = $check_summary->at_17_count - 1;
            $check_summary->save();

        } elseif (!empty($check_summary->at_18_count) && $time > '17:59:59' && $time <= '18:59:59') {
            $check_summary->at_18_count = $check_summary->at_18_count - 1;
            $check_summary->save();
        } elseif (!empty($check_summary->at_19_count) && $time > '18:59:59' && $time <= '19:59:59') {   
            $check_summary->at_19_count = $check_summary->at_19_count - 1;
            $check_summary->save();
        } elseif (!empty($check_summary->at_20_count) && $time > '19:59:59' && $time <= '20:59:59') {
            $check_summary->at_20_count = $check_summary->at_20_count - 1;
            $check_summary->save();
        } elseif (!empty($check_summary->at_21_count) && $time > '20:59:59' && $time <= '21:59:59') {
            $check_summary->at_21_count = $check_summary->at_21_count - 1;
            $check_summary->save();
        } elseif (!empty($check_summary->at_22_count) && $time > '21:59:59' && $time <= '22:59:59') {
            $check_summary->at_22_count = $check_summary->at_22_count - 1;
            $check_summary->save();
        } elseif (!empty($check_summary->after_23_count) && $time > '22:59:59' && $time <= '23:59:59') {
            $check_summary->after_23_count = $check_summary->after_23_count - 1;
            $check_summary->save();
        }
    }
}
