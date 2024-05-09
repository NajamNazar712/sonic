<?php

namespace App\Http\Controllers\Admins\Logistic;

use App\Http\Models\Shipment;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\ShipmentsJourney;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogisticToShipmentSyncController extends Controller
{



    //discuss column atif sir
    /**
     * booking_type_id
     * $self_collection
     * $business_category_id
     * $parcel_value
     * $return_address_id
     * $information_display
     * $package_type
     * $shipping_mode_id
     * $payment_mode_id
     * $charges_mode_id
     * $try_and_buy_charges
     * $amount
     * $same_day_timing_id
     * $open_shipment/is_open
     * static public function book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id,
     * $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2,
     * $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight,
     * $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id, $charges_mode_id,
     * $try_and_buy_charges, $pieces, $self_collection, $business_category_id, $open_shipment,
     * $return_address_id,$parcel_value = null)
     *
     * 'shipper_id' => $booking['shipper_id'],
     * 'cn_number' => $booking['cn_number'],
     * 'booking_date' => $booking['booking_date'],
     * 'product_id' => $booking['product_id'],
     * 'service_id' => $booking['service_id'],
     * 'destination_id' => $booking['destination_id'], //discuss sir atif
     * 'shipper_reference' => $booking['shipper_reference'],
     * 'consignee_name' => $booking['consignee_name'],
     * 'total_pieces' => $booking['total_pieces'],
     * 'consignee_phone_1' => $booking['consignee_phone_1'],
     * 'total_dense_weight' => $booking['total_dense_weight'],
     * 'total_volumetric_weight' => $booking['total_volumetric_weight'],
     * 'user_type' => 2,
     * 'created_by' => $rider_id,
     * 'created_at' => $currentTimestamp,
     * 'updated_at' => $currentTimestamp,
     **/
    public static function  shipments_book (
        $user_id,
        $tracking_number,
        $pickup_address_id,
        $booking_type_id,
        $information_display,
        $consignee_city_id,
        $consignee_name,
        $consignee_address,
        $consignee_phone_number_1,
        $pickup_date,
        $estimated_weight,
        $order_id,
        $package_type,
        $shipping_mode_id,
        $amount,
        $payment_mode_id,
        $shipper_status_id,
        $consignee_status_id,
        $charges_mode_id,
        $pieces,
        $business_category_id,
        $parcel_value,
        $return_address_id,
        $booked_by
    )
    {
        try {
            $shipment = new Shipment();
            $shipment->user_id = $user_id;
            $shipment->tracking_number = $tracking_number;
            $shipment->pickup_address_id = $pickup_address_id;
            $shipment->booking_type_id =$booking_type_id;
            $shipment->information_display=$information_display;
            $shipment->consignee_city_id = $consignee_city_id;
            $shipment->consignee_name = $consignee_name;
            $shipment->consignee_address = $consignee_address;
            $shipment->consignee_phone_number_1 = $consignee_phone_number_1;
            $shipment->pickup_date = $pickup_date;
            $shipment->estimated_weight = $estimated_weight;
            $shipment->order_id = $order_id;
            $shipment->package_type = $package_type;
            $shipment->shipping_mode_id = $shipping_mode_id;
            $shipment->amount = $amount;
            $shipment->payment_mode_id = $payment_mode_id;
            $shipment->shipper_status_id = $shipper_status_id;
            $shipment->consignee_status_id = $consignee_status_id;
            $shipment->charges_mode_id = $charges_mode_id;
            $shipment->pieces = $pieces;
            $shipment->business_category_id = $business_category_id;
            $shipment->parcel_value = $parcel_value;
            $shipment->return_address_id = $return_address_id;
            $shipment->booked_by = $booked_by;
            $shipment->save();

            $shipment_journey = new ShipmentsJourney();
            $shipment_journey->shipment_id = $shipment->id;
            $shipment_journey->verification = 1;
            $shipment_journey->user_id = $shipment->user_id;
            $shipment_journey->city_id = $shipment->consignee_city_id;
            $shipment_journey->shipper_status_id = 1;
            $shipment_journey->consignee_status_id = 1;
            $shipment_journey->ip_address = '127.0.0.1';
            $shipment_journey->save();

            return $shipment->id;

        } catch (\Exception $ex) {
            return false;
        }
    }

    public static function  shipment_pieces($shipment_id,$cn_no,$order)
    {
//        $i=1;
//        for ($cn_no=$form_piece;$cn_no<=$to_piece;$cn_no++)
//        {
            $shipment_piece = ShipmentPiece::where('tracking_number',$cn_no)->where('shipment_id',$shipment_id);
            if(!$shipment_piece->exists())
            {
                $shipment_piece = new ShipmentPiece();
                $shipment_piece->shipment_id = $shipment_id;
                $shipment_piece->tracking_number= $cn_no;
                $shipment_piece->numbering = $order;
                $shipment_piece->save();
            }

//        }
    }

//
//$shipments = [];
//if(isset($shipment_data))
//{
//foreach ($shipment_data as $shipment)
//{
//$shipments[] = [
//'user_id'=>$shipment->shipper_id,
//'tracking_number' =>$shipment['cn_number'],
//'booking_type_id' =>1, //rush or others //discuss
//'pickup_address_id'=>$shipment['pickup_address_id'],
//'consignee_city_id'=>$shipment['destination_id'],
//'consignee_name'=>$shipment['consignee_name'],
//'consignee_address'=>'Test Address', // discuss
//'consignee_phone_number_1'=>$shipment['consignee_phone_1'],
//'pickup_date'=>$shipment['booking_date'],
//'estimated_weight'=>500, //discuss,
//'order_id'=>5323 , //discuss
//'package_type'=>0, //discuss
//'shipping_mode_id'=>1,//discuss
//'amount'=>1500,//discuss
//'payment_mode_id'=>1 ,//discuss
//'shipper_status_id'=>1,
//'consignee_status_id'=>1,
//'charges_mode_id'=>4,
//'pieces'=>$shipment['total_pieces'],
//'business_category_id'=>1, //discuss,
//'parcel_value' =>null, //discuss
//'return_address_id'=>null, //discuss
//'created_at'=>$shipment['created_at'],
//'updated_at'=>$shipment['updated_at'],
//'booked_by'=>$shipment['user_type']
//
//];
//
//
//}
//}
}
