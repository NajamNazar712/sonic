<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin\ChangeShipmentAmountLog;
use App\Http\Models\Admin\ReplacementToRegularLog;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use Illuminate\Http\Request;

class ReplacementToRegualrShipmemtController extends Controller
{
   public static function replaceAutoWithRegularShipment($shipment_id,$shipment_amount,$shipment_reason,$launched_by,$user_id)
   {
       $shipment = Shipment::where('id', $shipment_id)->first();
       if($shipment->warehouse == 1){
           $product_type = ShipmentItem::where(['shipment_id' => $shipment_id, 'type' => 1])->get();
           foreach ($product_type as $product) {
               $insurance = $product['insurance'];
               $type = $product['type'];
               $product_type_id = $product['product_type_id'];
               $item_description = $product['description'];
               $item_quantity = $product['quantity'];
               $item_price = $product['price'];
               if ($item_price == null) {
                   $item_price = 0;
               }
               $replacement_charges = $shipment->replacement_charges;
               ReplacementToRegularLog::create([
                   'shipment_id' => $shipment->id,
                   'updated_by' => $user_id,
                   'replacement_charges' => $replacement_charges,
                   'product_type_id' => $product_type_id,
                   'item_description' => $item_description,
                   'item_quantity' => $item_quantity,
                   'item_price' => $item_price,
                   'insurance' => $insurance,
                   'type' => $type,
               ]);
           }
       } else {
           $product_type = ShipmentItem::where(['shipment_id' => $shipment_id, 'type' => 1])->first();
           $insurance = $product_type['insurance'];
           $type = $product_type['type'];
           $product_type_id = $product_type['product_type_id'];
           $item_description = $product_type['description'];
           $item_quantity = $product_type['quantity'];
           $item_price = $product_type['price'];
           $replacement_charges = $shipment['replacement_charges'];
           ReplacementToRegularLog::create([
               'shipment_id' => $shipment->id,
               'updated_by' => $user_id,
               'replacement_charges' => $replacement_charges,
               'product_type_id' => $product_type_id,
               'item_description' => $item_description,
               'item_quantity' => $item_quantity,
               'item_price' => $item_price,
               'insurance' => $insurance,
               'type' => $type,
           ]);
       }

       //if shipment status collected or not collected then change shipper_status_id udpate 13 re-attemp Anas BA Said
//       if(in_array($shipment->shipper_status_id,[56,30])) {
//           Shipment::where('id', $shipment_id)->update([
//               'booking_type_id' => 1,
//               'shipper_status_id' => 13,
//               'consignee_status_id' => 13,
//               'amount' => $shipment_amount,
//           ]);
//
//
//       }

       if($shipment) {

           $shipment->booking_type_id = 1;
           $shipment->amount = $shipment_amount;
           if(in_array($shipment->shipper_status_id,[56,30])) {
               $shipment->shipper_status_id = 13;
               $shipment->consignee_status_id = 13;
               ShipmentsJourneyController::add(
                   $shipment_id,
                   13,
                   13,
                   $shipment_reason,
                   NULL,
                   $launched_by == 0 ? NULL : $user_id,
                   $launched_by == 0 ? $user_id : NULL
               );
           }
           $shipment->save();
       }

       if ($shipment->amount != $shipment_amount) {
           ChangeShipmentAmountLog::create([
               'shipment_id' => $shipment_id,
               'old_amount' => $shipment->amount,
               'new_amount' => $shipment_amount,
               'admin_id' => $user_id
           ]);
       }

       ShipmentItem::where(['shipment_id' => $shipment->id, 'type' => 1])->delete();
   }
}
