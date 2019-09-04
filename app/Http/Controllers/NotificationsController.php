<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\CRFTermsConditions;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestTagging;
use App\Http\Models\Rider;
use App\Http\Models\ShipperNotificationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admins\AdminFinanceController;

use App\Http\Models\Notification;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\PickupNote;
use App\Http\Models\CargoConsignment;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Dispute;
use App\Http\Models\DonePayment;
use App\Http\Models\City;
use App\Http\Models\Invoice;
use App\Http\Models\SMS;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

use Carbon\Carbon;

use App\Mail\Notifications;

use App\Jobs\ProcessSMS;

class NotificationsController extends Controller
{
    static private function sms($body, $to) {
      $sms = new SMS();

      $sms->to = str_replace('-', '', $to);
      $sms->body = $body;

      $sms->save();

      dispatch(new ProcessSMS($sms));
    }

    static private function email($subject, $body, $to, $cc = NULL, $bcc = NULL) {
      $mail = Mail::to($to);

      if ($cc) {
        $mail->cc($cc);
      }

      if ($bcc) {
        $mail->bcc($bcc);
      }

      $mail->send(new Notifications($subject, $body));
    }

    static public function send($id, $reference_1_id, $reference_2_id = NULL) {
      $notification = Notification::find($id);

      if ($notification) {
        if ($notification->status) {
          if ($notification->type_id == 1) {
            $subject = $notification->subject;
          }

          $body = $notification->body;

          if ($id == 1) {
            $fields = ['account_id' => 'id', 'company_name' => 'name', 'email' => 'email', 'person_of_contact' => 'poc', 'phone_no_1' => 'phone', 'phone_no_2' => 'phone2', 'address' => 'address', 'cnic' => 'cnic', 'ntn_no' => 'ntn_no', 'api_token' => 'api_token'];

            $shipper = User::find($reference_1_id);

//            $to = $shipper->email;
              if(ShipperNotificationEmail::where('user_id',$shipper->id)->exists()){
                  $to = ShipperNotificationEmail::where('user_id',$shipper->id)->pluck('email')->toArray();
              }else{
                  $to = $shipper->email;
              }

            foreach ($fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                if ($key == 'account_id') {
                  $subject = str_replace('[' . $key . ']', str_pad($shipper[$field], 6, '0', STR_PAD_LEFT), $subject);
                }
                else {
                  $subject = str_replace('[' . $key . ']', $shipper[$field], $subject);
                }
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                if ($key == 'account_id') {
                  $body = str_replace('[' . $key . ']', str_pad($shipper[$field], 6, '0', STR_PAD_LEFT), $body);
                }
                else {
                  $body = str_replace('[' . $key . ']', $shipper[$field], $body);
                }
              }
            }

            if (strpos($subject, '[city]') !== FALSE) {
              $subject = str_replace('[city]', $shipper->city->name, $subject);
            }

            if (strpos($body, '[city]') !== FALSE) {
              $body = str_replace('[city]', $shipper->city->name, $body);
            }

            self::email($subject, $body, $to);
          }
          else if ($id == 2) {
            $fields = ['order_id' => 'order_id', 'pickup_date' => 'pickup_date', 'amount' => 'amount', 'tracking_number' => 'tracking_number'];

            $shipment = Shipment::find($reference_1_id);

            $shipper = $shipment->user;

            $to = $shipper->phone;

            foreach ($fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
            }

            if (strpos($body, '[account_id]') !== FALSE) {
              $body = str_replace('[account_id]', str_pad($shipper->id, 6, '0', STR_PAD_LEFT), $body);
            }

            if (strpos($body, '[company_name]') !== FALSE) {
              $body = str_replace('[company_name]', $shipper->name, $body);
            }

            if (strpos($body, '[service_type]') !== FALSE) {
              $body = str_replace('[service_type]', $shipment->booking_type->booking_type, $body);
            }

            if (strpos($body, '[pickup_address]') !== FALSE) {
              $body = str_replace('[pickup_address]', $shipment->pickup_address->address, $body);
            }

            if (strpos($body, '[pickup_city]') !== FALSE) {
              $body = str_replace('[pickup_city]', $shipment->pickup_address->city->name, $body);
            }

            if (strpos($body, '[shipping_mode]') !== FALSE) {
              $body = str_replace('[shipping_mode]', $shipment->shipping_mode->mode, $body);
            }

            if (strpos($body, '[payment_mode]') !== FALSE) {
              $body = str_replace('[payment_mode]', $shipment->payment_mode->mode, $body);
            }

            self::sms($body, $to);
          }
          else if ($id == 3) {
            $fields = ['consignee_name' => 'consignee_name', 'consignee_address' => 'consignee_address', 'order_id' => 'order_id', 'amount' => 'amount', 'tracking_number' => 'tracking_number'];

            $shipment = Shipment::find($reference_1_id);

            $shipper = $shipment->user;

            $to = $shipment->consignee_phone_number_1;

            foreach ($fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
            }

            if (strpos($body, '[company_name]') !== FALSE) {
              $body = str_replace('[company_name]', $shipper->name, $body);
            }

            if (strpos($body, '[service_type]') !== FALSE) {
              $body = str_replace('[service_type]', $shipment->booking_type->booking_type, $body);
            }

            if (strpos($body, '[service_type]') !== FALSE) {
              $body = str_replace('[service_type]', $shipment->booking_type->booking_type, $body);
            }

            if (strpos($body, '[pickup_address]') !== FALSE) {
              $body = str_replace('[pickup_address]', $shipment->pickup_address->address, $body);
            }

            if (strpos($body, '[pickup_city]') !== FALSE) {
              $body = str_replace('[pickup_city]', $shipment->pickup_address->city->name, $body);
            }

            if (strpos($body, '[consignee_city]') !== FALSE) {
              $body = str_replace('[consignee_city]', $shipment->consignee_city->name, $body);
            }

            if (strpos($body, '[shipping_mode]') !== FALSE) {
              $body = str_replace('[shipping_mode]', $shipment->shipping_mode->mode, $body);
            }

            if (strpos($body, '[payment_mode]') !== FALSE) {
              $body = str_replace('[payment_mode]', $shipment->payment_mode->mode, $body);
            }

            self::sms($body, $to);
          }
          else if ($id == 4) {
            $possible_fields = ['pickup_city', 'consignee_name', 'consignee_city', 'order_id', 'weight', 'tracking_number', 'item_product_type', 'item_description', 'item_quantity'];

            $field_names = ['pickup_city' => 'Pickup City', 'consignee_name' => 'Consignee Name', 'consignee_city' => 'Consignee City', 'order_id' => 'Order ID', 'weight' => 'Weight', 'tracking_number' => 'Tracking Number', 'item_product_type' => 'Item Product Type', 'item_description' => 'Item Description', 'item_quantity' => 'Item Quantity'];

            $present_fields = array();

            $first_field = NULL;

            $position = NULL;

            foreach ($possible_fields as $field) {
              $new_position = strpos($body, '[' . $field . ']');

              if ($new_position !== FALSE) {
                if ($position == NULL) {
                  $present_fields[] = $field;

                  $first_field = $field;
                }
                else if ($new_position > $position) {
                  $present_fields[] = $field;
                }
                else {
                  array_unshift($present_fields, $field);
                }

                $position = $new_position;
              }
            }

            $pickup_note = PickupNote::find($reference_1_id);

            $user_wise_shipments = array();

            foreach ($reference_2_id as $shipment_id) {
              $shipment = Shipment::find($shipment_id);

              $details = array();

              $details['pickup_city'] = $shipment->pickup_address->city->name;
              $details['consignee_name'] = $shipment->consignee_name;
              $details['consignee_city'] = $shipment->consignee_city->name;
              $details['order_id'] = $shipment->order_id;
              $details['weight'] = $shipment->actual_weight;
              $details['tracking_number'] = $shipment->tracking_number;

              if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 2) {
                foreach ($shipment->items as $item) {
                  if ($item->type == 0) {
                    $details['item_product_type'] = $item->product->product_name;
                    $details['item_description'] = $item->description;
                    $details['item_quantity'] = $item->quantity;
                  }
                }
              }
              else {
                $details['item_product_type'] = '';
                $details['item_description'] = '';
                $details['item_quantity'] = '';
              }

              $user_wise_shipments[$shipment->user_id][] = $details;
            }

            $original_subject = $subject;
            $original_body = $body;

            foreach ($user_wise_shipments as $user_id => $shipments) {
              $shipper = User::find($user_id);

              if (strpos($subject, '[company_name]') !== FALSE) {
                $subject = str_replace('[company_name]', $shipper->name, $subject);
              }

              if (strpos($body, '[company_name]') !== FALSE) {
                $body = str_replace('[company_name]', $shipper->name, $body);
              }

              if (strpos($subject, '[arrival_at]') !== FALSE) {
                $subject = str_replace('[arrival_at]', $pickup_note->created_at, $subject);
              }

              if (strpos($body, '[arrival_at]') !== FALSE) {
                $body = str_replace('[arrival_at]', $pickup_note->created_at, $body);
              }

//              $to = $shipper->email;
                if(ShipperNotificationEmail::where('user_id',$shipper->id)->exists()){
                    $to = ShipperNotificationEmail::where('user_id',$shipper->id)->pluck('email')->toArray();
                }else{
                    $to = $shipper->email;
                }
              $shipment_details = '<table style="padding:5px; border: 1px solid black; border-collapse: collapse;"><tbody><tr>';

              $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">S. No.</td>';

              foreach ($present_fields as $field) {
                $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">' . $field_names[$field] . '</td>';
              }

              $shipment_details .= '</tr>';

              $serial_number = 1;

              foreach ($shipments as $shipment) {
                $shipment_details .= '<tr>';

                $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial_number . '</td>';

                foreach ($present_fields as $field) {
                  if (!empty($shipment[$field])) {
                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment[$field] . '</td>';
                  }
                  else {
                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                  }
                }

                $shipment_details .= '</tr>';

                $serial_number++;
              }

              $shipment_details .= '</tbody></table>';

              foreach ($present_fields as $field) {
                if ($field != $first_field) {
                  $body = str_replace('[' . $field . ']', '', $body);
                }
              }

              $body = str_replace('[' . $first_field . ']', $shipment_details, $body);

              $bcc = array();

              $general_admins = Admin::whereIn('role_id', [4, 3, 6])->where('status', 1);

              if ($general_admins->exists()) {
                $bcc = array_merge($bcc, $general_admins->pluck('email')->toArray());
              }

              $origin_hub_id = $pickup_note->city->hub_id;

              $related_admins = Admin::whereIn('role_id', [10])->where('status', 1)->whereHas('hubs', function ($query) use ($origin_hub_id) {
                $query->where('hub_id', $origin_hub_id);
              });

              if ($related_admins->exists()) {
                $bcc = array_merge($bcc, $related_admins->pluck('email')->toArray());
              }

              if (empty($bcc)) {
                $bcc = NULL;
              }

              self::email($subject, $body, $to, NULL, $bcc);

              $subject = $original_subject;
              $body = $original_body;
            }
          }
          else if ($id == 5) {
            $cargo_fields = ['cargo_number' => 'id', 'departure_at' => 'created_at'];

            $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $cargo_consignment = CargoConsignment::find($reference_1_id);

            $shipment = Shipment::find($reference_2_id);

            $shipper = $shipment->user;

//            $to = $shipper->email;
              if(ShipperNotificationEmail::where('user_id',$shipper->id)->exists()){
                  $to = ShipperNotificationEmail::where('user_id',$shipper->id)->pluck('email')->toArray();
              }else{
                  $to = $shipper->email;
              }
            foreach ($cargo_fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                if ($key == 'cargo_number') {
                  $subject = str_replace('[' . $key . ']', str_pad($cargo_consignment[$field], 6, '0', STR_PAD_LEFT), $subject);
                }
                else {
                  $subject = str_replace('[' . $key . ']', $cargo_consignment[$field], $subject);
                }
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                if ($key == 'cargo_number') {
                  $body = str_replace('[' . $key . ']', str_pad($cargo_consignment[$field], 6, '0', STR_PAD_LEFT), $body);
                }
                else {
                  $body = str_replace('[' . $key . ']', $cargo_consignment[$field], $body);
                }
              }
            }

            foreach ($shipment_fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
            }

            if (strpos($body, '[company_name]') !== FALSE) {
              $body = str_replace('[company_name]', $shipper->name, $body);
            }

            self::email($subject, $body, $to);
          }
          else if ($id == 6) {
            $cargo_fields = ['cargo_number' => 'id', 'departure_at' => 'created_at'];

            $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $cargo_consignment = CargoConsignment::find($reference_1_id);

            $shipment = Shipment::find($reference_2_id);

            $shipper = $shipment->user;

            $to = $shipper->phone;

            foreach ($cargo_fields as $key => $field) {
              if ($key == 'cargo_number') {
                $body = str_replace('[' . $key . ']', str_pad($cargo_consignment[$field], 6, '0', STR_PAD_LEFT), $body);
              }
              else {
                $body = str_replace('[' . $key . ']', $cargo_consignment[$field], $body);
              }
            }

            foreach ($shipment_fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
            }

            if (strpos($body, '[company_name]') !== FALSE) {
              $body = str_replace('[company_name]', $shipper->name, $body);
            }

            self::sms($body, $to);
          }
          else if ($id == 7) {
            $cargo_fields = ['cargo_number' => 'id', 'arrival_at' => 'updated_at'];

            $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $cargo_consignment = CargoConsignment::find($reference_1_id);

            $shipment = Shipment::find($reference_2_id);

            $shipper = $shipment->user;

//            $to = $shipper->email;
              if(ShipperNotificationEmail::where('user_id',$shipper->id)->exists()){
                  $to = ShipperNotificationEmail::where('user_id',$shipper->id)->pluck('email')->toArray();
              }else{
                  $to = $shipper->email;
              }
            foreach ($cargo_fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                if ($key == 'cargo_number') {
                  $subject = str_replace('[' . $key . ']', str_pad($cargo_consignment[$field], 6, '0', STR_PAD_LEFT), $subject);
                }
                else {
                  $subject = str_replace('[' . $key . ']', $cargo_consignment[$field], $subject);
                }
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                if ($key == 'cargo_number') {
                  $body = str_replace('[' . $key . ']', str_pad($cargo_consignment[$field], 6, '0', STR_PAD_LEFT), $body);
                }
                else {
                  $body = str_replace('[' . $key . ']', $cargo_consignment[$field], $body);
                }
              }
            }

            foreach ($shipment_fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
            }

            if (strpos($subject, '[company_name]') !== FALSE) {
              $subject = str_replace('[company_name]', $shipper->name, $subject);
            }

            if (strpos($body, '[company_name]') !== FALSE) {
              $body = str_replace('[company_name]', $shipper->name, $body);
            }

            self::email($subject, $body, $to);
          }
          else if ($id == 8) {
            $cargo_fields = ['cargo_number' => 'id', 'arrival_at' => 'updated_at'];

            $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $cargo_consignment = CargoConsignment::find($reference_1_id);

            $shipment = Shipment::find($reference_2_id);

            $shipper = $shipment->user;

            $to = $shipper->phone;

            foreach ($cargo_fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                if ($key == 'cargo_number') {
                  $body = str_replace('[' . $key . ']', str_pad($cargo_consignment[$field], 6, '0', STR_PAD_LEFT), $body);
                }
                else {
                  $body = str_replace('[' . $key . ']', $cargo_consignment[$field], $body);
                }
              }
            }

            foreach ($shipment_fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
            }

            if (strpos($body, '[company_name]') !== FALSE) {
              $body = str_replace('[company_name]', $shipper->name, $body);
            }

            self::sms($body, $to);
          }
          else if ($id == 9) {
            $fields = ['cargo_number' => 'id', 'departure_at' => 'created_at', 'seal_number' => 'seal_number', 'builty_number' => 'builty_number', 'expected_arrival_date', 'expected_arrival_date'];

            $cargo_consignment = CargoConsignment::find($reference_1_id);

            foreach ($fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                if ($key == 'cargo_number') {
                  $subject = str_replace('[' . $key . ']', str_pad($cargo_consignment[$field], 6, '0', STR_PAD_LEFT), $subject);
                }
                else {
                  $subject = str_replace('[' . $key . ']', $cargo_consignment[$field], $subject);
                }
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                if ($key == 'cargo_number') {
                  $body = str_replace('[' . $key . ']', str_pad($cargo_consignment[$field], 6, '0', STR_PAD_LEFT), $body);
                }
                else {
                  $body = str_replace('[' . $key . ']', $cargo_consignment[$field], $body);
                }
              }
            }

            if (strpos($subject, '[shipping_mode]') !== FALSE) {
              $subject = str_replace('[shipping_mode]', $cargo_consignment->shipping_mode->mode, $subject);
            }

            if (strpos($body, '[shipping_mode]') !== FALSE) {
              $body = str_replace('[shipping_mode]', $cargo_consignment->shipping_mode->mode, $body);
            }

            if (strpos($subject, '[transport_mode]') !== FALSE) {
              $subject = str_replace('[transport_mode]', $cargo_consignment->transport_mode->name, $subject);
            }

            if (strpos($body, '[transport_mode]') !== FALSE) {
              $body = str_replace('[transport_mode]', $cargo_consignment->transport_mode->name, $body);
            }

            if (strpos($subject, '[vendor]') !== FALSE) {
              $subject = str_replace('[vendor]', $cargo_consignment->transport_mode_vendor->name, $subject);
            }

            if (strpos($body, '[vendor]') !== FALSE) {
              $body = str_replace('[vendor]', $cargo_consignment->transport_mode_vendor->name, $body);
            }

            if (strpos($subject, '[sender]') !== FALSE) {
              $subject = str_replace('[sender]', $cargo_consignment->sender->name, $subject);
            }

            if (strpos($body, '[sender]') !== FALSE) {
              $body = str_replace('[sender]', $cargo_consignment->sender->name, $body);
            }

            if (strpos($body, '[tracking_number]') !== FALSE) {
              $tracking_numbers = '';

              foreach ($cargo_consignment->cargo_consignment_shipments as $cargo_consignment_shipment) {
                $shipment = $cargo_consignment_shipment->shipment;

                $tracking_numbers .= $shipment->tracking_number . PHP_EOL;
              }

              $body = str_replace('[tracking_number]', $tracking_numbers, $body);
            }

            $to = array();

            $general_admins = Admin::whereIn('role_id', [3, 4, 6])->where('status', 1);

            if ($general_admins->exists()) {
              $to = array_merge($to, $general_admins->pluck('email')->toArray());
            }

            $origin_hub_id = $cargo_consignment->origin_hub_id;
            $destination_hub_id = $cargo_consignment->destination_hub_id;

            $related_admins = Admin::whereIn('role_id', [8, 9, 10])->where('status', 1)->whereHas('hubs', function ($query) use ($origin_hub_id, $destination_hub_id) {
              $query->where('hub_id', $origin_hub_id)
              ->orWhere('hub_id', $destination_hub_id);
            });

            if ($related_admins->exists()) {
              $to = array_merge($to, $related_admins->pluck('email')->toArray());
            }

            if (!empty($to)) {
              self::email($subject, $body, $to);
            }
          }
          else if ($id == 10) {
            $delivery_note_fields = ['delivery_note_number' => 'id', 'departure_at' => 'created_at'];

            $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $delivery_note = DeliveryNote::find($reference_1_id);

            $shipment = Shipment::find($reference_2_id);

            $shipper = $shipment->user;

//            $to = $shipper->email;
              if(ShipperNotificationEmail::where('user_id',$shipper->id)->exists()){
                  $to = ShipperNotificationEmail::where('user_id',$shipper->id)->pluck('email')->toArray();
              }else{
                  $to = $shipper->email;
              }
            foreach ($delivery_note_fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                if ($key == 'delivery_note_number') {
                  $subject = str_replace('[' . $key . ']', str_pad($delivery_note[$field], 6, '0', STR_PAD_LEFT), $subject);
                }
                else {
                  $subject = str_replace('[' . $key . ']', $delivery_note[$field], $subject);
                }
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                if ($key == 'delivery_note_number') {
                  $body = str_replace('[' . $key . ']', str_pad($delivery_note[$field], 6, '0', STR_PAD_LEFT), $body);
                }
                else {
                  $body = str_replace('[' . $key . ']', $delivery_note[$field], $body);
                }
              }
            }

            foreach ($shipment_fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
            }

            if (strpos($subject, '[rider]') !== FALSE) {
              $subject = str_replace('[rider]', $delivery_note->rider->name . ' (' . $delivery_note->rider->phone . ')', $subject);
            }

            if (strpos($body, '[rider]') !== FALSE) {
              $body = str_replace('[rider]', $delivery_note->rider->name . ' (' . $delivery_note->rider->phone . ')', $body);
            }

            if (strpos($subject, '[company_name]') !== FALSE) {
              $subject = str_replace('[company_name]', $shipper->name, $subject);
            }

            if (strpos($body, '[company_name]') !== FALSE) {
              $body = str_replace('[company_name]', $shipper->name, $body);
            }

            self::email($subject, $body, $to);
          }
          else if ($id == 11) {
            $delivery_note_fields = ['delivery_note_number' => 'id', 'departure_at' => 'created_at'];

            $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $delivery_note = DeliveryNote::find($reference_1_id);

            $shipment = Shipment::find($reference_2_id);

            $shipper = $shipment->user;

            $to = $shipper->phone;

            foreach ($delivery_note_fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                if ($key == 'delivery_note_number') {
                  $body = str_replace('[' . $key . ']', str_pad($delivery_note[$field], 6, '0', STR_PAD_LEFT), $body);
                }
                else {
                  $body = str_replace('[' . $key . ']', $delivery_note[$field], $body);
                }
              }
            }

            foreach ($shipment_fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
            }

            if (strpos($body, '[rider]') !== FALSE) {
              $body = str_replace('[rider]', $delivery_note->rider->name . ' (' . $delivery_note->rider->phone . ')', $body);
            }

            if (strpos($body, '[company_name]') !== FALSE) {
              $body = str_replace('[company_name]', $shipper->name, $body);
            }

            self::sms($body, $to);
          }
          else if ($id == 12) {
            $delivery_note_fields = ['delivery_note_number' => 'id', 'departure_at' => 'created_at'];

            $shipment_fields = ['consignee_name' => 'consignee_name', 'consignee_address' => 'consignee_address', 'order_id' => 'order_id', 'amount' => 'amount', 'tracking_number' => 'tracking_number'];

            $delivery_note = DeliveryNote::find($reference_1_id);

            $delivery_note_shipment = DeliveryNoteShipment::where('delivery_note_id', $reference_1_id)->where('shipment_id', $reference_2_id)->first();

            $shipment = Shipment::find($reference_2_id);

            $shipper = $shipment->user;

            $to = $shipment->consignee_phone_number_1;

            foreach ($delivery_note_fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                if ($key == 'delivery_note_number') {
                  $body = str_replace('[' . $key . ']', str_pad($delivery_note[$field], 6, '0', STR_PAD_LEFT), $body);
                }
                else {
                  $body = str_replace('[' . $key . ']', $delivery_note[$field], $body);
                }
              }
            }

            foreach ($shipment_fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
            }

            if (strpos($body, '[rider]') !== FALSE) {
                if ($delivery_note_shipment->rider_information) {
                    $body = str_replace('[rider]', $delivery_note->rider->name . ' (' . $delivery_note->rider->phone . ')', $body);
                }
                else {
                    $body = str_replace('[rider]', '', $body);
                }
            }

            if (strpos($body, '[company_name]') !== FALSE) {
              $body = str_replace('[company_name]', $shipper->name, $body);
            }

            if (strpos($body, '[payment_mode]') !== FALSE) {
              $body = str_replace('[payment_mode]', $shipment->payment_mode->mode, $body);
            }

            self::sms($body, $to);
          }
          else if ($id == 13) {
            $delivery_note_fields = ['delivery_note_number' => 'id', 'departure_at' => 'created_at'];

            $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $delivery_note = DeliveryNote::find($reference_1_id);

            foreach ($delivery_note_fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                if ($key == 'delivery_note_number') {
                  $subject = str_replace('[' . $key . ']', str_pad($delivery_note[$field], 6, '0', STR_PAD_LEFT), $subject);
                }
                else {
                  $subject = str_replace('[' . $key . ']', $delivery_note[$field], $subject);
                }
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                if ($key == 'delivery_note_number') {
                  $body = str_replace('[' . $key . ']', str_pad($delivery_note[$field], 6, '0', STR_PAD_LEFT), $body);
                }
                else {
                  $body = str_replace('[' . $key . ']', $delivery_note[$field], $body);
                }
              }
            }

            if (strpos($subject, '[rider]') !== FALSE) {
              $subject = str_replace('[rider]', $delivery_note->rider->name . ' (' . $delivery_note->rider->phone . ')', $subject);
            }

            if (strpos($body, '[rider]') !== FALSE) {
              $body = str_replace('[rider]', $delivery_note->rider->name . ' (' . $delivery_note->rider->phone . ')', $body);
            }

            $original_subject = $subject;
            $original_body = $body;

            foreach ($delivery_note->delivery_note_shipments as $delivery_note_shipment) {
              $shipment = $delivery_note_shipment->shipment;

              if ($shipment->shipper_status_id != 12) {
                $shipper = $shipment->user;

//                $to = $shipper->email;
                  if(ShipperNotificationEmail::where('user_id',$shipper->id)->exists()){
                      $to = ShipperNotificationEmail::where('user_id',$shipper->id)->pluck('email')->toArray();
                  }else{
                      $to = $shipper->email;
                  }
                foreach ($shipment_fields as $key => $field) {
                  if (strpos($subject, '[' . $key . ']') !== FALSE) {
                    $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
                  }

                  if (strpos($body, '[' . $key . ']') !== FALSE) {
                    $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                  }
                }

                if (strpos($subject, '[company_name]') !== FALSE) {
                  $subject = str_replace('[company_name]', $shipper->name, $subject);
                }

                if (strpos($body, '[company_name]') !== FALSE) {
                  $body = str_replace('[company_name]', $shipper->name, $body);
                }

                if (strpos($subject, '[status]') !== FALSE) {
                  $subject = str_replace('[status]', $shipment->status_shipper->name, $subject);
                }

                if (strpos($body, '[status]') !== FALSE) {
                  $body = str_replace('[status]', $shipment->status_shipper->name, $body);
                }

                self::email($subject, $body, $to);

                $subject = $original_subject;
                $body = $original_body;
              }
            }
          }
          else if ($id == 14) {
            $delivery_note_fields = ['delivery_note_number' => 'id', 'departure_at' => 'created_at'];

            $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $delivery_note = DeliveryNote::find($reference_1_id);

            foreach ($delivery_note_fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                if ($key == 'delivery_note_number') {
                  $body = str_replace('[' . $key . ']', str_pad($delivery_note[$field], 6, '0', STR_PAD_LEFT), $body);
                }
                else {
                  $body = str_replace('[' . $key . ']', $delivery_note[$field], $body);
                }
              }
            }

            if (strpos($body, '[rider]') !== FALSE) {
              $body = str_replace('[rider]', $delivery_note->rider->name . ' (' . $delivery_note->rider->phone . ')', $body);
            }

            $original_body = $body;

            foreach ($delivery_note->delivery_note_shipments as $delivery_note_shipment) {
              $shipment = $delivery_note_shipment->shipment;

              if ($shipment->shipper_status_id != 12) {
                $shipper = $shipment->user;

                $to = $shipper->phone;

                foreach ($shipment_fields as $key => $field) {
                  if (strpos($body, '[' . $key . ']') !== FALSE) {
                    $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                  }
                }

                if (strpos($body, '[company_name]') !== FALSE) {
                  $body = str_replace('[company_name]', $shipper->name, $body);
                }

                if (strpos($body, '[status]') !== FALSE) {
                  $body = str_replace('[status]', $shipment->status_shipper->name, $body);
                }

                self::sms($body, $to);

                $body = $original_body;
              }
            }
          }
          else if ($id == 15) {
            if ($reference_1_id != 0) {
              $return_note_fields = ['return_note_number' => 'id', 'departure_at' => 'created_at'];

              $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

              $return_note = ReturnNote::find($reference_1_id);

              foreach ($return_note_fields as $key => $field) {
                if (strpos($subject, '[' . $key . ']') !== FALSE) {
                  if ($key == 'return_note_number') {
                    $subject = str_replace('[' . $key . ']', str_pad($return_note[$field], 6, '0', STR_PAD_LEFT), $subject);
                  }
                  else {
                    $subject = str_replace('[' . $key . ']', $return_note[$field], $subject);
                  }
                }

                if (strpos($body, '[' . $key . ']') !== FALSE) {
                  if ($key == 'return_note_number') {
                    $body = str_replace('[' . $key . ']', str_pad($return_note[$field], 6, '0', STR_PAD_LEFT), $body);
                  }
                  else {
                    $body = str_replace('[' . $key . ']', $return_note[$field], $body);
                  }
                }
              }

              if (strpos($subject, '[rider]') !== FALSE) {
                $subject = str_replace('[rider]', $return_note->rider->name . ' (' . $return_note->rider->phone . ')', $subject);
              }

              if (strpos($body, '[rider]') !== FALSE) {
                $body = str_replace('[rider]', $return_note->rider->name . ' (' . $return_note->rider->phone . ')', $body);
              }

              $original_subject = $subject;
              $original_body = $body;

              foreach ($return_note->return_note_shipments as $return_note_shipment) {
                $shipment = $return_note_shipment->shipment;

                $shipper = $shipment->user;

//                $to = $shipper->email;
                  if(ShipperNotificationEmail::where('user_id',$shipper->id)->exists()){
                      $to = ShipperNotificationEmail::where('user_id',$shipper->id)->pluck('email')->toArray();
                  }else{
                      $to = $shipper->email;
                  }
                foreach ($shipment_fields as $key => $field) {
                  if (strpos($subject, '[' . $key . ']') !== FALSE) {
                    $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
                  }

                  if (strpos($body, '[' . $key . ']') !== FALSE) {
                    $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                  }
                }

                if (strpos($subject, '[company_name]') !== FALSE) {
                  $subject = str_replace('[company_name]', $shipper->name, $subject);
                }

                if (strpos($body, '[company_name]') !== FALSE) {
                  $body = str_replace('[company_name]', $shipper->name, $body);
                }

                if (strpos($subject, '[status]') !== FALSE) {
                  $subject = str_replace('[status]', $shipment->status_shipper->name, $subject);
                }

                if (strpos($body, '[status]') !== FALSE) {
                  $body = str_replace('[status]', $shipment->status_shipper->name, $body);
                }

                self::email($subject, $body, $to);

                $subject = $original_subject;
                $body = $original_body;
              }
            }
            else {
              $remove_fields = ['return_note_number', 'departure_at', 'rider'];

              $fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

              $shipment = Shipment::find($reference_2_id);

              $shipper = $shipment->user;

//              $to = $shipper->email;
                if(ShipperNotificationEmail::where('user_id',$shipper->id)->exists()){
                    $to = ShipperNotificationEmail::where('user_id',$shipper->id)->pluck('email')->toArray();
                }else{
                    $to = $shipper->email;
                }
              foreach ($remove_fields as $field) {
                if (strpos($subject, '[' . $field . ']') !== FALSE) {
                  $subject = str_replace('[' . $field . ']', '-', $subject);
                }

                if (strpos($body, '[' . $field . ']') !== FALSE) {
                  $body = str_replace('[' . $field . ']', '-', $body);
                }
              }

              foreach ($fields as $key => $field) {
                if (strpos($subject, '[' . $key . ']') !== FALSE) {
                  $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
                }

                if (strpos($body, '[' . $key . ']') !== FALSE) {
                  $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                }
              }

              if (strpos($subject, '[company_name]') !== FALSE) {
                $subject = str_replace('[company_name]', $shipper->name, $subject);
              }

              if (strpos($body, '[company_name]') !== FALSE) {
                $body = str_replace('[company_name]', $shipper->name, $body);
              }

              if (strpos($subject, '[status]') !== FALSE) {
                $subject = str_replace('[status]', $shipment->status_shipper->name, $subject);
              }

              if (strpos($body, '[status]') !== FALSE) {
                $body = str_replace('[status]', $shipment->status_shipper->name, $body);
              }

              self::email($subject, $body, $to);
            }
          }
          else if ($id == 16) {
            if ($reference_1_id != 0) {
              $return_note_fields = ['return_note_number' => 'id', 'departure_at' => 'created_at'];

              $shipment_fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

              $return_note = ReturnNote::find($reference_1_id);

              foreach ($return_note_fields as $key => $field) {
                if (strpos($body, '[' . $key . ']') !== FALSE) {
                  if ($key == 'return_note_number') {
                    $body = str_replace('[' . $key . ']', str_pad($return_note[$field], 6, '0', STR_PAD_LEFT), $body);
                  }
                  else {
                    $body = str_replace('[' . $key . ']', $return_note[$field], $body);
                  }
                }
              }

              if (strpos($body, '[rider]') !== FALSE) {
                $body = str_replace('[rider]', $return_note->rider->name . ' (' . $return_note->rider->phone . ')', $body);
              }

              $original_body = $body;

              foreach ($return_note->return_note_shipments as $return_note_shipment) {
                $shipment = $return_note_shipment->shipment;

                $shipper = $shipment->user;

                $to = $shipper->phone;

                foreach ($shipment_fields as $key => $field) {
                  if (strpos($body, '[' . $key . ']') !== FALSE) {
                    $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                  }
                }

                if (strpos($body, '[company_name]') !== FALSE) {
                  $body = str_replace('[company_name]', $shipper->name, $body);
                }

                if (strpos($body, '[status]') !== FALSE) {
                  $body = str_replace('[status]', $shipment->status_shipper->name, $body);
                }

                self::sms($body, $to);

                $body = $original_body;
              }
            }
            else {
              $remove_fields = ['return_note_number', 'departure_at', 'rider'];

              $fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

              $shipment = Shipment::find($reference_2_id);

              $shipper = $shipment->user;

              $to = $shipper->phone;

              foreach ($remove_fields as $field) {
                if (strpos($body, '[' . $field . ']') !== FALSE) {
                  $body = str_replace('[' . $field . ']', '-', $body);
                }
              }

              foreach ($fields as $key => $field) {
                if (strpos($body, '[' . $key . ']') !== FALSE) {
                  $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                }
              }

              if (strpos($body, '[company_name]') !== FALSE) {
                $body = str_replace('[company_name]', $shipper->name, $body);
              }

              if (strpos($body, '[status]') !== FALSE) {
                $body = str_replace('[status]', $shipment->status_shipper->name, $body);
              }

              self::sms($body, $to);
            }
          }
          else if ($id == 17) {
            $fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $shipment = Shipment::find($reference_1_id);

            $new_shipment = Shipment::find($reference_2_id);

            $shipper = $shipment->user;

//            $to = $shipper->email;
              if(ShipperNotificationEmail::where('user_id',$shipper->id)->exists()){
                  $to = ShipperNotificationEmail::where('user_id',$shipper->id)->pluck('email')->toArray();
              }else{
                  $to = $shipper->email;
              }
            foreach ($fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
            }

            if (strpos($subject, '[account_id]') !== FALSE) {
              $subject = str_replace('[account_id]', str_pad($shipper->id, 6, '0', STR_PAD_LEFT), $subject);
            }

            if (strpos($body, '[account_id]') !== FALSE) {
              $body = str_replace('[account_id]', str_pad($shipper->id, 6, '0', STR_PAD_LEFT), $body);
            }

            if (strpos($subject, '[company_name]') !== FALSE) {
              $subject = str_replace('[company_name]', $shipper->name, $subject);
            }

            if (strpos($body, '[company_name]') !== FALSE) {
              $body = str_replace('[company_name]', $shipper->name, $body);
            }

            if (strpos($subject, '[service_type]') !== FALSE) {
              $subject = str_replace('[service_type]', $shipment->booking_type->booking_type, $subject);
            }

            if (strpos($body, '[service_type]') !== FALSE) {
              $body = str_replace('[service_type]', $shipment->booking_type->booking_type, $body);
            }

            if (strpos($subject, '[new_tracking_number]') !== FALSE) {
              $subject = str_replace('[new_tracking_number]', $new_shipment->tracking_number, $subject);
            }

            if (strpos($body, '[new_tracking_number]') !== FALSE) {
              $body = str_replace('[new_tracking_number]', $new_shipment->tracking_number, $body);
            }

            self::email($subject, $body, $to);
          }
          else if ($id == 18) {
            $fields = ['order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $shipment = Shipment::find($reference_1_id);

            $new_shipment = Shipment::find($reference_2_id);

            $shipper = $shipment->user;

            $to = $shipper->phone;

            foreach ($fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
            }

            if (strpos($body, '[account_id]') !== FALSE) {
              $body = str_replace('[account_id]', $shipper->id, $body);
            }

            if (strpos($body, '[company_name]') !== FALSE) {
              $body = str_replace('[company_name]', $shipper->name, $body);
            }

            if (strpos($body, '[service_type]') !== FALSE) {
              $body = str_replace('[service_type]', $shipment->booking_type->booking_type, $body);
            }

            if (strpos($body, '[new_tracking_number]') !== FALSE) {
              $body = str_replace('[new_tracking_number]', $new_shipment->tracking_number, $body);
            }

            self::sms($body, $to);
          }
          else if ($id == 19) {
            $fields = ['dispute_number' => 'id', 'dispute_description' => 'description'];

            $dispute = Dispute::find($reference_1_id);

            if ($dispute->raised_by_status == 0) {
              $launched_by = $dispute->admins;
            }
            else {
              $launched_by = $dispute->shipper;
            }

            foreach ($fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                if ($key == 'dispute_number') {
                  $subject = str_replace('[' . $key . ']', str_pad($dispute[$field], 6, '0', STR_PAD_LEFT), $subject);
                }
                else {
                  $subject = str_replace('[' . $key . ']', $dispute[$field], $subject);
                }
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                if ($key == 'dispute_number') {
                  $body = str_replace('[' . $key . ']', str_pad($dispute[$field], 6, '0', STR_PAD_LEFT), $body);
                }
                else {
                  $body = str_replace('[' . $key . ']', $dispute[$field], $body);
                }
              }
            }

            if (strpos($subject, '[dispute_type]') !== FALSE) {
              $subject = str_replace('[dispute_type]', $dispute->dispute_types->type, $subject);
            }

            if (strpos($body, '[dispute_type]') !== FALSE) {
              $body = str_replace('[dispute_type]', $dispute->dispute_types->type, $body);
            }

            if (strpos($subject, '[launched_by]') !== FALSE) {
              $subject = str_replace('[launched_by]', $launched_by->name, $subject);
            }

            if (strpos($body, '[launched_by]') !== FALSE) {
              $body = str_replace('[launched_by]', $launched_by->name, $body);
            }

            if (strpos($subject, '[city]') !== FALSE) {
              $subject = str_replace('[city]', $dispute->city->name, $subject);
            }

            if (strpos($body, '[city]') !== FALSE) {
              $body = str_replace('[city]', $dispute->city->name, $body);
            }

            if (strpos($subject, '[tracking_number]') !== FALSE) {
              $tracking_numbers = '';

              foreach ($dispute->dispute_shipments as $dispute_shipment) {
                  $shipment = $dispute_shipment->shipment;

                  $tracking_numbers .= $shipment->tracking_number . ', ';
                }

              $tracking_numbers .= substr($tracking_numbers, 0, -2) . PHP_EOL;

              $subject = str_replace('[tracking_number]', $tracking_numbers, $subject);
            }

            if (strpos($body, '[tracking_number]') !== FALSE) {
              $tracking_numbers = '';

              foreach ($dispute->dispute_shipments as $dispute_shipment) {
                  $shipment = $dispute_shipment->shipment;

                  $tracking_numbers .= $shipment->tracking_number . ', ';
                }

              $tracking_numbers .= substr($tracking_numbers, 0, -2) . PHP_EOL;

              $body = str_replace('[tracking_number]', $tracking_numbers, $body);
            }

            $to = [$launched_by->email];

            $general_admins = Admin::whereIn('role_id', [4, 3, 2, 5, 6])->where('status', 1);

            if ($general_admins->exists()) {
              $to = array_merge($to, $general_admins->pluck('email')->toArray());
            }

            $hub_id = $dispute->city->hub_id;

            $related_admins = Admin::whereIn('role_id', [8, 11])->where('status', 1)->whereHas('hubs', function ($query) use ($hub_id) {
              $query->where('hub_id', $hub_id);
            });

            if ($related_admins->exists()) {
              $to = array_merge($to, $related_admins->pluck('email')->toArray());
            }

            self::email($subject, $body, $to);
          }
          else if ($id == 20) {
            $possible_fields = ['consignee_name', 'consignee_city', 'order_id', 'estimated_weight', 'actual_weight', 'chargeable_weight', 'tracking_number', 'amount', 'weight_charges', 'cash_handling_charges', 'charges', 'gst', 'payable'];

            $field_names = ['consignee_name' => 'Consignee Name', 'consignee_city' => 'Consignee City', 'order_id' => 'Order ID', 'estimated_weight' => 'Estimated Weight', 'actual_weight' => 'Actual Weight', 'chargeable_weight' => 'Chargeable Weight', 'tracking_number' => 'Tracking Number', 'amount' => 'Collection Amount (PKR)', 'weight_charges' => 'Weight Charges (PKR)', 'cash_handling_charges' => 'Cash Handling Charges (PKR)', 'charges' => 'Total Charges (PKR)', 'gst' => 'GST (PKR)', 'payable' => 'Payable (PKR)'];

            $present_fields = array();

            $first_field = NULL;

            $position = NULL;

            foreach ($possible_fields as $field) {
              $new_position = strpos($body, '[' . $field . ']');

              if ($new_position !== FALSE) {
                if ($position == NULL) {
                  $present_fields[] = $field;

                  $first_field = $field;
                }
                else if ($new_position > $position) {
                  $present_fields[] = $field;
                }
                else {
                  array_unshift($present_fields, $field);
                }

                $position = $new_position;
              }
            }

            $done_payment = DonePayment::find($reference_1_id);

            $shipper = User::find($done_payment->user_id);

            if (strpos($subject, '[company_name]') !== FALSE) {
              $subject = str_replace('[company_name]', $shipper->name, $subject);
            }

            if (strpos($body, '[company_name]') !== FALSE) {
              $body = str_replace('[company_name]', $shipper->name, $body);
            }

            if (strpos($subject, '[city]') !== FALSE) {
              $subject = str_replace('[city]', $shipper->city->name, $subject);
            }

            if (strpos($body, '[city]') !== FALSE) {
              $body = str_replace('[city]', $shipper->city->name, $body);
            }

            if (strpos($subject, '[bank]') !== FALSE) {
              $subject = str_replace('[bank]', $shipper->bank->bank_name, $subject);
            }

            if (strpos($body, '[bank]') !== FALSE) {
              $body = str_replace('[bank]', $shipper->bank->bank_name, $body);
            }

            if (strpos($subject, '[bank_branch]') !== FALSE) {
              $subject = str_replace('[bank_branch]', $shipper->bank->bank_branch, $subject);
            }

            if (strpos($body, '[bank_branch]') !== FALSE) {
              $body = str_replace('[bank_branch]', $shipper->bank->bank_branch, $body);
            }

            if (strpos($subject, '[account_number]') !== FALSE) {
              $subject = str_replace('[account_number]', $shipper->bank->account_no, $subject);
            }

            if (strpos($body, '[account_number]') !== FALSE) {
              $body = str_replace('[account_number]', $shipper->bank->account_no, $body);
            }

            if (strpos($subject, '[account_title]') !== FALSE) {
              $subject = str_replace('[account_title]', $shipper->bank->account_title, $subject);
            }

            if (strpos($body, '[account_title]') !== FALSE) {
              $body = str_replace('[account_title]', $shipper->bank->account_title, $body);
            }

            if (strpos($subject, '[iban]') !== FALSE) {
              $subject = str_replace('[iban]', $shipper->bank->iban, $subject);
            }

            if (strpos($body, '[iban]') !== FALSE) {
              $body = str_replace('[iban]', $shipper->bank->iban, $body);
            }

            if (strpos($subject, '[account_city]') !== FALSE) {
              $subject = str_replace('[account_city]', $shipper->bank->city->name, $subject);
            }

            if (strpos($body, '[account_city]') !== FALSE) {
              $body = str_replace('[account_city]', $shipper->bank->city->name, $body);
            }

            if (strpos($subject, '[payment_cycle]') !== FALSE) {
              $subject = str_replace('[payment_cycle]', $shipper->bank->payment_cycle, $subject);
            }

            if (strpos($body, '[account_number]') !== FALSE) {
              $body = str_replace('[payment_cycle]', $shipper->bank->payment_cycle, $body);
            }

            if (strpos($subject, '[payment_done_id]') !== FALSE) {
              $subject = str_replace('[payment_done_id]', str_pad($done_payment->id, 6, '0', STR_PAD_LEFT), $subject);
            }

            if (strpos($body, '[payment_done_id]') !== FALSE) {
              $body = str_replace('[payment_done_id]', str_pad($done_payment->id, 6, '0', STR_PAD_LEFT), $body);
            }

            if (strpos($subject, '[payment_done_at]') !== FALSE) {
              $subject = str_replace('[payment_done_at]', $done_payment->created_at, $subject);
            }

            if (strpos($body, '[payment_done_at]') !== FALSE) {
              $body = str_replace('[payment_done_at]', $done_payment->created_at, $body);
            }

            if (strpos($subject, '[total_shipments]') !== FALSE) {
              $subject = str_replace('[total_shipments]', $done_payment->total_shipments, $subject);
            }

            if (strpos($body, '[total_shipments]') !== FALSE) {
              $body = str_replace('[total_shipments]', $done_payment->total_shipments, $body);
            }

            if (strpos($subject, '[delivered_shipments]') !== FALSE) {
              $subject = str_replace('[delivered_shipments]', $done_payment->delivered_shipments, $subject);
            }

            if (strpos($body, '[delivered_shipments]') !== FALSE) {
              $body = str_replace('[delivered_shipments]', $done_payment->delivered_shipments, $body);
            }

            if (strpos($subject, '[returned_shipments]') !== FALSE) {
              $subject = str_replace('[returned_shipments]', $done_payment->returned_shipments, $subject);
            }

            if (strpos($body, '[returned_shipments]') !== FALSE) {
              $body = str_replace('[returned_shipments]', $done_payment->returned_shipments, $body);
            }

            if (strpos($subject, '[adjusted_shipments]') !== FALSE) {
              $subject = str_replace('[adjusted_shipments]', $done_payment->adjusted_shipments, $subject);
            }

            if (strpos($body, '[adjusted_shipments]') !== FALSE) {
              $body = str_replace('[adjusted_shipments]', $done_payment->adjusted_shipments, $body);
            }

//            $to = $shipper->email;
              if(ShipperNotificationEmail::where('user_id',$shipper->id)->exists()){
                  $to = ShipperNotificationEmail::where('user_id',$shipper->id)->pluck('email')->toArray();
              }else{
                  $to = $shipper->email;
              }
            $shipment_details = '<table style="padding:5px; border: 1px solid black; border-collapse: collapse;"><tbody><tr>';

            $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">S. No.</td>';

            foreach ($present_fields as $field) {
              $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">' . $field_names[$field] . '</td>';
            }

            $shipment_details .= '</tr>';

            $serial_number = 1;

            $total_amount = 0;
            $total_weight_charges = 0;
            $total_cash_handling_charges = 0;
            $total_insurance_charges = 0;
            $total_replacement_charges = 0;
            // $total_try_and_buy_charges = 0;
            $total_return_charges = 0;
            $total_packaging_material_charges = 0;
            $total_fuel_surcharge = 0;
            $total_gst = 0;
            $total_charges = 0;
            $total_payable = 0;

            foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
              $shipment_details .= '<tr>';

              $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial_number . '</td>';

              $shipment = $done_payment_shipment->shipment;

              foreach ($present_fields as $field) {
                if (in_array($field, ['amount', 'charges', 'gst', 'payable'])) {
                  $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $done_payment_shipment[$field] . '</td>';
                }
                else if ($field == 'consignee_city') {
                  $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment->consignee_city->name . '</td>';
                }
                else if (!empty($shipment[$field])) {
                  $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment[$field] . '</td>';
                }
                else {
                  $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                }
              }

              $shipment_details .= '</tr>';

              $serial_number++;

              if ($done_payment_shipment->type == 0) {
                  $total_amount += $done_payment_shipment->amount;
                  $total_cash_handling_charges += $shipment->cash_handling_charges;
                  $total_replacement_charges += $shipment->replacement_charges;
                  // $total_try_and_buy_charges += $shipment->try_and_buy_charges;
              }
              else {
                  $total_return_charges += $shipment->return_charges;
              }

              $total_weight_charges += $shipment->weight_charges;

              if ($shipment->packaging_material_request) {
                  $total_packaging_material_charges += $shipment->packaging_material_charges;
              }

              $total_insurance_charges += $shipment->insurance_charges;
              $total_fuel_surcharge += $shipment->fuel_surcharge;

              $total_gst += $done_payment_shipment->gst;
              $total_charges += $done_payment_shipment->charges + $done_payment_shipment->gst;
              $total_payable += $done_payment_shipment->payable;
            }

            $shipment_details .= '</tbody></table>';

            foreach ($present_fields as $field) {
              if ($field != $first_field) {
                $body = str_replace('[' . $field . ']', '', $body);
              }
            }

            $body = str_replace('[' . $first_field . ']', $shipment_details, $body);

            if (strpos($subject, '[total_amount]') !== FALSE) {
              $subject = str_replace('[total_amount]', $total_amount, $subject);
            }

            if (strpos($body, '[total_amount]') !== FALSE) {
              $body = str_replace('[total_amount]', $total_amount, $body);
            }

            if (strpos($subject, '[total_weight_charges]') !== FALSE) {
              $subject = str_replace('[total_weight_charges]', $total_weight_charges, $subject);
            }

            if (strpos($body, '[total_weight_charges]') !== FALSE) {
              $body = str_replace('[total_weight_charges]', $total_weight_charges, $body);
            }

            if (strpos($subject, '[total_cash_handling_charges]') !== FALSE) {
              $subject = str_replace('[total_cash_handling_charges]', $total_cash_handling_charges, $subject);
            }

            if (strpos($body, '[total_cash_handling_charges]') !== FALSE) {
              $body = str_replace('[total_cash_handling_charges]', $total_cash_handling_charges, $body);
            }

            if (strpos($subject, '[total_insurance_charges]') !== FALSE) {
              $subject = str_replace('[total_insurance_charges]', $total_insurance_charges, $subject);
            }

            if (strpos($body, '[total_insurance_charges]') !== FALSE) {
              $body = str_replace('[total_insurance_charges]', $total_insurance_charges, $body);
            }

            if (strpos($subject, '[total_replacement_charges]') !== FALSE) {
              $subject = str_replace('[total_replacement_charges]', $total_replacement_charges, $subject);
            }

            if (strpos($body, '[total_replacement_charges]') !== FALSE) {
              $body = str_replace('[total_replacement_charges]', $total_replacement_charges, $body);
            }

            // if (strpos($subject, '[total_try_and_buy_charges]') !== FALSE) {
            //   $subject = str_replace('[total_try_and_buy_charges]', $total_try_and_buy_charges, $subject);
            // }

            // if (strpos($body, '[total_try_and_buy_charges]') !== FALSE) {
            //   $body = str_replace('[total_try_and_buy_charges]', $total_try_and_buy_charges, $body);
            // }

            if (strpos($subject, '[total_return_charges]') !== FALSE) {
              $subject = str_replace('[total_return_charges]', $total_return_charges, $subject);
            }

            if (strpos($body, '[total_return_charges]') !== FALSE) {
              $body = str_replace('[total_return_charges]', $total_return_charges, $body);
            }

            if (strpos($subject, '[total_packaging_material_charges]') !== FALSE) {
              $subject = str_replace('[total_packaging_material_charges]', $total_packaging_material_charges, $subject);
            }

            if (strpos($body, '[total_packaging_material_charges]') !== FALSE) {
              $body = str_replace('[total_packaging_material_charges]', $total_packaging_material_charges, $body);
            }

            if (strpos($subject, '[total_fuel_surcharge]') !== FALSE) {
              $subject = str_replace('[total_fuel_surcharge]', $total_fuel_surcharge, $subject);
            }

            if (strpos($body, '[total_fuel_surcharge]') !== FALSE) {
              $body = str_replace('[total_fuel_surcharge]', $total_fuel_surcharge, $body);
            }

            if (strpos($subject, '[total_gst]') !== FALSE) {
              $subject = str_replace('[total_gst]', $total_gst, $subject);
            }

            if (strpos($body, '[total_gst]') !== FALSE) {
              $body = str_replace('[total_gst]', $total_gst, $body);
            }

            if (strpos($subject, '[total_charges]') !== FALSE) {
              $subject = str_replace('[total_charges]', $total_charges, $subject);
            }

            if (strpos($body, '[total_charges]') !== FALSE) {
              $body = str_replace('[total_charges]', $total_charges, $body);
            }

            if (strpos($subject, '[total_payable]') !== FALSE) {
              $subject = str_replace('[total_payable]', $total_payable, $subject);
            }

            if (strpos($body, '[total_payable]') !== FALSE) {
              $body = str_replace('[total_payable]', $total_payable, $body);
            }

            $bcc = array();

            $general_admins = Admin::whereIn('role_id', [4, 3, 6])->where('status', 1);

            if ($general_admins->exists()) {
              $bcc = array_merge($bcc, $general_admins->pluck('email')->toArray());
            }

            $hub_id = $shipper->city->hub_id;

            $related_admins = Admin::whereIn('role_id', [10])->where('status', 1)->whereHas('hubs', function ($query) use ($hub_id) {
              $query->where('hub_id', $hub_id);
            });

            if ($related_admins->exists()) {
              $bcc = array_merge($bcc, $related_admins->pluck('email')->toArray());
            }

            if (empty($bcc)) {
              $bcc = NULL;
            }

            self::email($subject, $body, $to, NULL, $bcc);
          }
          else if ($id == 21) {
            $fields = ['consignee_name' => 'consignee_name', 'consignee_address' => 'consignee_address', 'order_id' => 'order_id', 'amount' => 'amount', 'tracking_number' => 'tracking_number'];

            $shipment = Shipment::find($reference_1_id);

            $shipper = $shipment->user;

            foreach ($fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
            }

            if (strpos($subject, '[company_name]') !== FALSE) {
              $subject = str_replace('[company_name]', $shipper->name, $subject);
            }

            if (strpos($body, '[company_name]') !== FALSE) {
              $body = str_replace('[company_name]', $shipper->name, $body);
            }

            if (strpos($subject, '[service_type]') !== FALSE) {
              $subject = str_replace('[service_type]', $shipment->booking_type->booking_type, $subject);
            }

            if (strpos($body, '[service_type]') !== FALSE) {
              $body = str_replace('[service_type]', $shipment->booking_type->booking_type, $body);
            }

            if (strpos($subject, '[pickup_address]') !== FALSE) {
              $subject = str_replace('[pickup_address]', $shipment->pickup_address->address, $subject);
            }

            if (strpos($body, '[pickup_address]') !== FALSE) {
              $body = str_replace('[pickup_address]', $shipment->pickup_address->address, $body);
            }

            if (strpos($subject, '[pickup_city]') !== FALSE) {
              $subject = str_replace('[pickup_city]', $shipment->pickup_address->city->name, $subject);
            }

            if (strpos($body, '[pickup_city]') !== FALSE) {
              $body = str_replace('[pickup_city]', $shipment->pickup_address->city->name, $body);
            }

            if (strpos($subject, '[consignee_city]') !== FALSE) {
              $subject = str_replace('[consignee_city]', $shipment->consignee_city->name, $subject);
            }

            if (strpos($body, '[consignee_city]') !== FALSE) {
              $body = str_replace('[consignee_city]', $shipment->consignee_city->name, $body);
            }

            if (strpos($subject, '[payment_mode]') !== FALSE) {
              $subject = str_replace('[payment_mode]', $shipment->payment_mode->mode, $subject);
            }

            if (strpos($body, '[payment_mode]') !== FALSE) {
              $body = str_replace('[payment_mode]', $shipment->payment_mode->mode, $body);
            }

//            $to = [$shipper->email];
              if(ShipperNotificationEmail::where('user_id',$shipper->id)->exists()){
                  $to = ShipperNotificationEmail::where('user_id',$shipper->id)->pluck('email')->toArray();
              }else{
                  $to = [$shipper->email];
              }
            $general_admins = Admin::whereIn('role_id', [4, 6])->where('status', 1);

            if ($general_admins->exists()) {
              $to = array_merge($to, $general_admins->pluck('email')->toArray());
            }

            $to[] = Admin::find($reference_2_id)->email;

            self::email($subject, $body, $to);
          }
          else if ($id == 22) {
            $possible_fields = ['company_name', 'person_of_contact', 'phone_number', 'address', 'city'];

            $present_fields = array();

            $first_field = NULL;

            $position = NULL;

            foreach ($possible_fields as $field) {
              $new_position = strpos($body, '[' . $field . ']');

              if ($new_position !== FALSE) {
                if ($position == NULL) {
                  $present_fields[] = $field;

                  $first_field = $field;
                }
                else if ($new_position > $position) {
                  $present_fields[] = $field;
                }
                else {
                  array_unshift($present_fields, $field);
                }

                $position = $new_position;
              }
            }

            $pickup_note = PickupNote::find($reference_1_id);

            $to = $pickup_note->rider->phone;

            $pickup_details = '';

            foreach ($pickup_note->pickup_note_requests as $pickup_note_request) {
              $pickup_request = $pickup_note_request->pickup_request;
              $pickup_address = $pickup_request->pickup_address;

              foreach ($present_fields as $field) {
                if ($field == 'company_name') {
                  $pickup_details .= $pickup_request->shipper->name . ', ';
                }
                else if ($field == 'person_of_contact') {
                  $pickup_details .= $pickup_address->poc . ', ';
                }
                else if ($field == 'phone_number') {
                  $pickup_details .= $pickup_address->phone . ', ';
                }
                else if ($field == 'address') {
                  $pickup_details .= $pickup_address->pickup_address . ', ';
                }
                else if ($field == 'city') {
                  $pickup_details .= $pickup_address->city->name . ', ';
                }
              }

              $pickup_details = substr($pickup_details, 0, -2) . PHP_EOL;
            }

            foreach ($present_fields as $field) {
              if ($field != $first_field) {
                $body = str_replace('[' . $field . ']', '', $body);
              }
            }

            $body = str_replace('[' . $first_field . ']', $pickup_details, $body);

            self::sms($body, $to);
          }
          else if ($id == 23) {
            $shipments = Shipment::where('shipper_status_id', 12);

            if ($shipments->exists()) {
              $subject = $notification->subject;
              $body = $notification->body;

              $possible_fields = ['service_type', 'pickup_address', 'pickup_city', 'consignee_name', 'consignee_phone_number_1', 'consignee_phone_number_2', 'consignee_email', 'consignee_address', 'consignee_city', 'order_id', 'shipping_mode', 'amount', 'payment_mode', 'status', 'status_reason', 'status_date', 'arrival_date', 'tracking_number'];

              $field_names = ['service_type' => 'Service Type', 'pickup_address' => 'Pickup Address', 'pickup_city' => 'Pickup City', 'consignee_name' => 'Consignee Name', 'consignee_phone_number_1' => 'Consignee Phone Number 1', 'consignee_phone_number_2' => 'Consignee Phone Number 2', 'consignee_email' => 'Consignee Email', 'consignee_address' => 'Consignee Address', 'consignee_city' => 'Consignee City', 'order_id' => 'Order ID', 'shipping_mode' => 'Shipping Mode', 'amount' => 'Amount', 'payment_mode' => 'Payment Mode', 'status' => 'Status', 'status_reason' => 'Status Reason', 'status_date' => 'Status Date', 'arrival_date' => 'Arrival Date', 'tracking_number' => 'Tracking Number'];

              $present_fields = array();

              $first_field = NULL;

              $position = NULL;

              foreach ($possible_fields as $field) {
                $new_position = strpos($body, '[' . $field . ']');

                if ($new_position !== FALSE) {
                  if ($position == NULL) {
                    $present_fields[] = $field;

                    $first_field = $field;
                  }
                  else if ($new_position > $position) {
                    $present_fields[] = $field;
                  }
                  else {
                    array_unshift($present_fields, $field);
                  }

                  $position = $new_position;
                }
              }

              $shipments = $shipments->get();

              $user_wise_shipments = array();

              foreach ($shipments as $shipment) {
                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 12)->latest()->first();

                if ($shipment_journey && $shipment_journey->verification) {
                  $details = array();

                  $details['service_type'] = $shipment->booking_type->booking_type;
                  $details['pickup_address'] = $shipment->pickup_address->address;
                  $details['pickup_city'] = $shipment->pickup_address->city->name;
                  $details['consignee_name'] = $shipment->consignee_name;
                  $details['consignee_phone_number_1'] = $shipment->consignee_phone_number_1;
                  $details['consignee_phone_number_2'] = $shipment->consignee_phone_number_2;
                  $details['consignee_email'] = $shipment->consignee_email;
                  $details['consignee_address'] = $shipment->consignee_address;
                  $details['consignee_city'] = $shipment->consignee_city->name;
                  $details['order_id'] = $shipment->order_id;
                  $details['shipping_mode'] = $shipment->shipping_mode->mode;
                  $details['amount'] = $shipment->amount;
                  $details['payment_mode'] = $shipment->payment_mode->mode;
                  $details['status'] = $shipment_journey->shipment_status_shipper->name;

                  if ($shipment_journey->status_reason_id) {
                    $details['status_reason'] = $shipment_journey->shipment_status_reason->name;
                  }

                  $details['status_date'] = $shipment_journey->created_at;

                  $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2)->first();

                  if ($shipment_journey) {
                    $details['arrival_date'] = $shipment_journey->created_at;
                  }
                  else {
                    $details['arrival_date'] = $shipment->created_at;
                  }

                  $details['tracking_number'] = $shipment->tracking_number;

                  $user_wise_shipments[$shipment->user_id][] = $details;
                }
              }

              if (!empty($user_wise_shipments)) {
                $original_subject = $subject;
                $original_body = $body;

                foreach ($user_wise_shipments as $user_id => $shipments) {
                  $shipper = User::find($user_id);

                  if (strpos($subject, '[company_name]') !== FALSE) {
                    $subject = str_replace('[company_name]', $shipper->name, $subject);
                  }

                  if (strpos($body, '[company_name]') !== FALSE) {
                    $body = str_replace('[company_name]', $shipper->name, $body);
                  }

//                  $to = $shipper->email;
                    if(ShipperNotificationEmail::where('user_id',$shipper->id)->exists()){
                        $to = ShipperNotificationEmail::where('user_id',$shipper->id)->pluck('email')->toArray();
                    }else{
                        $to = $shipper->email;
                    }
                  $shipment_details = '<table style="padding:5px; border: 1px solid black; border-collapse: collapse;"><tbody><tr>';

                  $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">S. No.</td>';

                  foreach ($present_fields as $field) {
                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">' . $field_names[$field] . '</td>';
                  }

                  $shipment_details .= '</tr>';

                  $serial_number = 1;

                  foreach ($shipments as $shipment) {
                    $shipment_details .= '<tr>';

                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial_number . '</td>';

                    foreach ($present_fields as $field) {
                      if (!empty($shipment[$field])) {
                        $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment[$field] . '</td>';
                      }
                      else {
                        $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                      }
                    }

                    $shipment_details .= '</tr>';

                    $serial_number++;
                  }

                  $shipment_details .= '</tbody></table>';

                  foreach ($present_fields as $field) {
                    if ($field != $first_field) {
                      $body = str_replace('[' . $field . ']', '', $body);
                    }
                  }

                  $body = str_replace('[' . $first_field . ']', $shipment_details, $body);

                  $cc = array();

                  $general_admins = Admin::whereIn('role_id', [6, 15, 21])->where('status', 1);

                  if ($general_admins->exists()) {
                    $cc = array_merge($cc, $general_admins->pluck('email')->toArray());
                  }

                  self::email($subject, $body, $to, $cc);

                  $subject = $original_subject;
                  $body = $original_body;
                }
              }
            }
          }
          else if ($id == 24) {
            $shipments = Shipment::where('shipper_status_id', 20);

            if ($shipments->exists()) {
              $subject = $notification->subject;
              $body = $notification->body;

              $possible_fields = ['service_type', 'pickup_address', 'pickup_city', 'consignee_name', 'consignee_phone_number_1', 'consignee_phone_number_2', 'consignee_email', 'consignee_address', 'consignee_city', 'order_id', 'shipping_mode', 'status', 'status_reason', 'status_date', 'tracking_number'];

              $field_names = ['service_type' => 'Service Type', 'pickup_address' => 'Pickup Address', 'pickup_city' => 'Pickup City', 'consignee_name' => 'Consignee Name', 'consignee_phone_number_1' => 'Consignee Phone Number 1', 'consignee_phone_number_2' => 'Consignee Phone Number 2', 'consignee_email' => 'Consignee Email', 'consignee_address' => 'Consignee Address', 'consignee_city' => 'Consignee City', 'order_id' => 'Order ID', 'shipping_mode' => 'Shipping Mode', 'status' => 'Status', 'status_reason' => 'Status Reason', 'status_date' => 'Status Date', 'tracking_number' => 'Tracking Number'];

              $present_fields = array();

              $first_field = NULL;

              $position = NULL;

              foreach ($possible_fields as $field) {
                $new_position = strpos($body, '[' . $field . ']');

                if ($new_position !== FALSE) {
                  if ($position == NULL) {
                    $present_fields[] = $field;

                    $first_field = $field;
                  }
                  else if ($new_position > $position) {
                    $present_fields[] = $field;
                  }
                  else {
                    array_unshift($present_fields, $field);
                  }

                  $position = $new_position;
                }
              }

              $shipments = $shipments->get();

              $hub_wise_shipments = array();

              foreach ($shipments as $shipment) {
                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 20)->latest()->first();

                if ($shipment_journey && $shipment_journey->verification) {
                  $details = array();

                  $details['service_type'] = $shipment->booking_type->booking_type;
                  $details['pickup_address'] = $shipment->pickup_address->address;
                  $details['pickup_city'] = $shipment->pickup_address->city->name;
                  $details['consignee_name'] = $shipment->consignee_name;
                  $details['consignee_phone_number_1'] = $shipment->consignee_phone_number_1;
                  $details['consignee_phone_number_2'] = $shipment->consignee_phone_number_2;
                  $details['consignee_email'] = $shipment->consignee_email;
                  $details['consignee_address'] = $shipment->consignee_address;
                  $details['consignee_city'] = $shipment->consignee_city->name;
                  $details['order_id'] = $shipment->order_id;
                  $details['shipping_mode'] = $shipment->shipping_mode->mode;
                  $details['status'] = $shipment_journey->shipment_status_shipper->name;

                  if ($shipment_journey->status_reason_id) {
                    $details['status_reason'] = $shipment_journey->shipment_status_reason->name;
                  }

                  $details['status_date'] = $shipment_journey->created_at;

                  $details['tracking_number'] = $shipment->tracking_number;

                  $hub_wise_shipments[$shipment->consignee_city->hub_id][] = $details;
                }
              }

              if (!empty($hub_wise_shipments)) {
                $original_subject = $subject;
                $original_body = $body;

                foreach ($hub_wise_shipments as $hub_id => $shipments) {
                  $hub = City::find($hub_id);

                  if (strpos($subject, '[hub]') !== FALSE) {
                    $subject = str_replace('[hub]', $hub->name, $subject);
                  }

                  if (strpos($body, '[hub]') !== FALSE) {
                    $body = str_replace('[hub]', $hub->name, $body);
                  }

                  $shipment_details = '<table style="padding:5px; border: 1px solid black; border-collapse: collapse;"><tbody><tr>';

                  $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">S. No.</td>';

                  foreach ($present_fields as $field) {
                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">' . $field_names[$field] . '</td>';
                  }

                  $shipment_details .= '</tr>';

                  $serial_number = 1;

                  foreach ($shipments as $shipment) {
                    $shipment_details .= '<tr>';

                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial_number . '</td>';

                    foreach ($present_fields as $field) {
                      if (!empty($shipment[$field])) {
                        $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment[$field] . '</td>';
                      }
                      else {
                        $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                      }
                    }

                    $shipment_details .= '</tr>';

                    $serial_number++;
                  }

                  $shipment_details .= '</tbody></table>';

                  foreach ($present_fields as $field) {
                    if ($field != $first_field) {
                      $body = str_replace('[' . $field . ']', '', $body);
                    }
                  }

                  $body = str_replace('[' . $first_field . ']', $shipment_details, $body);

                  $to = array();

                  $general_admins = Admin::whereIn('role_id', [6, 3, 15])->where('status', 1);

                  if ($general_admins->exists()) {
                    $to = array_merge($to, $general_admins->pluck('email')->toArray());
                  }

                  $related_admins = Admin::whereIn('role_id', [8, 9, 10])->where('status', 1)->whereHas('hubs', function ($query) use ($hub_id) {
                    $query->where('hub_id', $hub_id);
                  });

                  if ($related_admins->exists()) {
                    $to = array_merge($to, $related_admins->pluck('email')->toArray());
                  }

                  self::email($subject, $body, $to);

                  $subject = $original_subject;
                  $body = $original_body;
                }
              }
            }
          }
          else if ($id == 25) {
            $shipments = Shipment::where('shipper_status_id', 13);

            if ($shipments->exists()) {
              $subject = $notification->subject;
              $body = $notification->body;

              $possible_fields = ['service_type', 'pickup_address', 'pickup_city', 'consignee_name', 'consignee_phone_number_1', 'consignee_phone_number_2', 'consignee_email', 'consignee_address', 'consignee_city', 'order_id', 'shipping_mode', 'status', 'status_reason', 'status_date', 'tracking_number'];

              $field_names = ['service_type' => 'Service Type', 'pickup_address' => 'Pickup Address', 'pickup_city' => 'Pickup City', 'consignee_name' => 'Consignee Name', 'consignee_phone_number_1' => 'Consignee Phone Number 1', 'consignee_phone_number_2' => 'Consignee Phone Number 2', 'consignee_email' => 'Consignee Email', 'consignee_address' => 'Consignee Address', 'consignee_city' => 'Consignee City', 'order_id' => 'Order ID', 'shipping_mode' => 'Shipping Mode', 'status' => 'Status', 'status_reason' => 'Status Reason', 'status_date' => 'Status Date', 'tracking_number' => 'Tracking Number'];

              $present_fields = array();

              $first_field = NULL;

              $position = NULL;

              foreach ($possible_fields as $field) {
                $new_position = strpos($body, '[' . $field . ']');

                if ($new_position !== FALSE) {
                  if ($position == NULL) {
                    $present_fields[] = $field;

                    $first_field = $field;
                  }
                  else if ($new_position > $position) {
                    $present_fields[] = $field;
                  }
                  else {
                    array_unshift($present_fields, $field);
                  }

                  $position = $new_position;
                }
              }

              $shipments = $shipments->get();

              $hub_wise_shipments = array();

              foreach ($shipments as $shipment) {
                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 13)->latest()->first();

                if ($shipment_journey && $shipment_journey->verification) {
                  $details = array();

                  $details['service_type'] = $shipment->booking_type->booking_type;
                  $details['pickup_address'] = $shipment->pickup_address->address;
                  $details['pickup_city'] = $shipment->pickup_address->city->name;
                  $details['consignee_name'] = $shipment->consignee_name;
                  $details['consignee_phone_number_1'] = $shipment->consignee_phone_number_1;
                  $details['consignee_phone_number_2'] = $shipment->consignee_phone_number_2;
                  $details['consignee_email'] = $shipment->consignee_email;
                  $details['consignee_address'] = $shipment->consignee_address;
                  $details['consignee_city'] = $shipment->consignee_city->name;
                  $details['order_id'] = $shipment->order_id;
                  $details['shipping_mode'] = $shipment->shipping_mode->mode;
                  $details['status'] = $shipment_journey->shipment_status_shipper->name;

                  if ($shipment_journey->status_reason_id) {
                    $details['status_reason'] = $shipment_journey->shipment_status_reason->name;
                  }

                  $details['status_date'] = $shipment_journey->created_at;

                  $details['tracking_number'] = $shipment->tracking_number;

                  $hub_wise_shipments[$shipment->consignee_city->hub_id][] = $details;
                }
              }

              if (!empty($hub_wise_shipments)) {
                $original_subject = $subject;
                $original_body = $body;

                foreach ($hub_wise_shipments as $hub_id => $shipments) {
                  $hub = City::find($hub_id);

                  if (strpos($subject, '[hub]') !== FALSE) {
                    $subject = str_replace('[hub]', $hub->name, $subject);
                  }

                  if (strpos($body, '[hub]') !== FALSE) {
                    $body = str_replace('[hub]', $hub->name, $body);
                  }

                  $shipment_details = '<table style="padding:5px; border: 1px solid black; border-collapse: collapse;"><tbody><tr>';

                  $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">S. No.</td>';

                  foreach ($present_fields as $field) {
                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">' . $field_names[$field] . '</td>';
                  }

                  $shipment_details .= '</tr>';

                  $serial_number = 1;

                  foreach ($shipments as $shipment) {
                    $shipment_details .= '<tr>';

                    $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial_number . '</td>';

                    foreach ($present_fields as $field) {
                      if (!empty($shipment[$field])) {
                        $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipment[$field] . '</td>';
                      }
                      else {
                        $shipment_details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"></td>';
                      }
                    }

                    $shipment_details .= '</tr>';

                    $serial_number++;
                  }

                  $shipment_details .= '</tbody></table>';

                  foreach ($present_fields as $field) {
                    if ($field != $first_field) {
                      $body = str_replace('[' . $field . ']', '', $body);
                    }
                  }

                  $body = str_replace('[' . $first_field . ']', $shipment_details, $body);

                  $to = array();

                  $general_admins = Admin::whereIn('role_id', [6, 3, 15])->where('status', 1);

                  if ($general_admins->exists()) {
                    $to = array_merge($to, $general_admins->pluck('email')->toArray());
                  }

                  $related_admins = Admin::whereIn('role_id', [8, 9, 10])->where('status', 1)->whereHas('hubs', function ($query) use ($hub_id) {
                    $query->where('hub_id', $hub_id);
                  });

                  if ($related_admins->exists()) {
                    $to = array_merge($to, $related_admins->pluck('email')->toArray());
                  }

                  self::email($subject, $body, $to);

                  $subject = $original_subject;
                  $body = $original_body;
                }
              }
            }
          }
          else if ($id == 26) {
              if (strpos($subject, '[date]') !== FALSE) {
                  $subject = str_replace('[date]', $reference_1_id, $subject);
              }

              if (strpos($body, '[date]') !== FALSE) {
                  $body = str_replace('[date]', $reference_1_id, $body);
              }

              $link = '<a href="' . $reference_2_id . '" target="_blank">Report</a>';

              if (strpos($subject, '[link]') !== FALSE) {
                  $subject = str_replace('[link]', $link, $subject);
              }

              if (strpos($body, '[link]') !== FALSE) {
                  $body = str_replace('[link]', $link, $body);
              }

              $to = array();

              $admins = Admin::whereIn('role_id', [2, 3, 4, 6, 20])->where('status', 1);

              if ($admins->exists()) {
                  $to = array_merge($to, $admins->pluck('email')->toArray());
              }

              $admins = Admin::join('admin_roles', 'admins.role_id', '=', 'admin_roles.id')->where('admin_roles.department_id', 7);

              if ($admins->exists()) {
                  $to = array_merge($to, $admins->pluck('admins.email')->toArray());
              }

              $ceo = Admin::find(8);

              if ($ceo) {
                  $to[] = $ceo->email;
              }

              self::email($subject, $body, $to);
          }
          else if ($id == 27) {
            $shipper_fields = ['account_id' => 'id', 'company_name' => 'name'];

            $invoice_fields = ['invoice_number' => 'invoice_number', 'billing_period_from_date' => 'billing_period_from_date', 'billing_period_to_date' => 'billing_period_to_date', 'due_date' => 'due_date'];

            $invoice = Invoice::find($reference_1_id);

            $to = array();

            $shipper = $invoice->shipper;

            $to[] = $shipper->email;

            $to[] = $shipper->bank->billing_person_email;

            foreach ($shipper_fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                if ($key == 'account_id') {
                  $subject = str_replace('[' . $key . ']', str_pad($shipper[$field], 6, '0', STR_PAD_LEFT), $subject);
                }
                else {
                  $subject = str_replace('[' . $key . ']', $shipper[$field], $subject);
                }
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                if ($key == 'account_id') {
                  $body = str_replace('[' . $key . ']', str_pad($shipper[$field], 6, '0', STR_PAD_LEFT), $body);
                }
                else {
                  $body = str_replace('[' . $key . ']', $shipper[$field], $body);
                }
              }
            }

            foreach ($invoice_fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                if ($key == 'billing_period_from_date' || $key == 'billing_period_to_date' || $key == 'due_date') {
                  $subject = str_replace('[' . $key . ']', Carbon::parse($invoice[$field])->format('d/m/Y'), $subject);
                }
                else {
                  $subject = str_replace('[' . $key . ']', $invoice[$field], $subject);
                }
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                if ($key == 'billing_period_from_date' || $key == 'billing_period_to_date' || $key == 'due_date') {
                  $body = str_replace('[' . $key . ']', Carbon::parse($invoice[$field])->format('d/m/Y'), $body);
                }
                else {
                  $body = str_replace('[' . $key . ']', $invoice[$field], $body);
                }
              }
            }

            if (strpos($subject, '[invoice]') !== FALSE) {
              $subject = str_replace('[invoice]', '', $subject);
            }

            if (strpos($body, '[invoice]') !== FALSE) {
              $invoice = AdminFinanceController::generate_invoice_print($reference_1_id, TRUE);

              $body = str_replace('[invoice]', preg_replace('/\r|\n/', '', $invoice), $body);
            }

            $cc = array();

            $general_admins = Admin::where('role_id', 2)->where('status', 1);

            if ($general_admins->exists()) {
              $cc = array_merge($cc, $general_admins->pluck('email')->toArray());
            }

            self::email($subject, $body, $to, $cc);
          }
          else if ($id == 28) {
            $shipper_fields = ['account_id' => 'id', 'company_name' => 'name'];

            $invoice_fields = ['invoice_number' => 'invoice_number', 'billing_period_from_date' => 'billing_period_from_date', 'billing_period_to_date' => 'billing_period_to_date', 'due_date' => 'due_date'];

            $invoice = Invoice::find($reference_1_id);

            $to = array();

            $shipper = $invoice->shipper;

            $to[] = $shipper->email;

            $to[] = $shipper->bank->billing_person_email;

            foreach ($shipper_fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                if ($key == 'account_id') {
                  $subject = str_replace('[' . $key . ']', str_pad($shipper[$field], 6, '0', STR_PAD_LEFT), $subject);
                }
                else {
                  $subject = str_replace('[' . $key . ']', $shipper[$field], $subject);
                }
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                if ($key == 'account_id') {
                  $body = str_replace('[' . $key . ']', str_pad($shipper[$field], 6, '0', STR_PAD_LEFT), $body);
                }
                else {
                  $body = str_replace('[' . $key . ']', $shipper[$field], $body);
                }
              }
            }

            foreach ($invoice_fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                if ($key == 'billing_period_from_date' || $key == 'billing_period_to_date' || $key == 'due_date') {
                  $subject = str_replace('[' . $key . ']', Carbon::parse($invoice[$field])->format('d/m/Y'), $subject);
                }
                else {
                  $subject = str_replace('[' . $key . ']', $invoice[$field], $subject);
                }
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                if ($key == 'billing_period_from_date' || $key == 'billing_period_to_date' || $key == 'due_date') {
                  $body = str_replace('[' . $key . ']', Carbon::parse($invoice[$field])->format('d/m/Y'), $body);
                }
                else {
                  $body = str_replace('[' . $key . ']', $invoice[$field], $body);
                }
              }
            }

            if (strpos($subject, '[invoice]') !== FALSE) {
              $subject = str_replace('[invoice]', '', $subject);
            }

            if (strpos($body, '[invoice]') !== FALSE) {
              $invoice = AdminFinanceController::generate_invoice_print($reference_1_id, TRUE);

              $body = str_replace('[invoice]', preg_replace('/\r|\n/', '', $invoice), $body);
            }

            $cc = array();

            $general_admins = Admin::where('role_id', 2)->where('status', 1);

            if ($general_admins->exists()) {
              $cc = array_merge($cc, $general_admins->pluck('email')->toArray());
            }

            self::email($subject, $body, $to, $cc);
          }
			else if($id == 31){
	              $possible_fields = ['tracking_number', 'shipper_name', 'email', 'phone', 'destination', 'channel', 'case_nature', 'case_nature_type', 'details'];

	            $crm_request = CrmRequest::find($reference_1_id);
	            if($crm_request) {
	                $tagging = CrmRequestTagging::where('crm_request_id', $crm_request->id)->first();
	                if ($crm_request->status_id != 1) {
	                    if ($tagging) {

	                        if ($tagging->crm_request_tagging_type_id == 1) {
	                            $roles = AdminRole::where('department_id', $tagging->tagged_id)->pluck('id');
	                            $admin_department = Admin::whereIn('role_id', $roles)->where('status', 1);
	                            if ($admin_department->exists()) {
	                                $to = $admin_department->pluck('email');
	                            }
	                        } else if ($tagging->crm_request_tagging_type_id == 2) {
	                            $admin_department = Admin::find($tagging->tagged_id)->email;
	                            $to = $admin_department;
	                        }
	                    }
	                }
	                else{
	                    $to = 'complaints@trax.pk';
	                }

	                    $table_details = '';

	                    if($crm_request->shipment_id){
	                        $shipment = Shipment::find($crm_request->shipment_id);
	                        if($shipment){
	                            if (strpos($subject, '[tracking_number]') !== FALSE) {
	                                $subject = str_replace('[tracking_number]', $shipment->tracking_number, $subject);
	                            }
	                            if (strpos($subject, '[shipper_name]') !== FALSE) {
	                                $subject = str_replace('[shipper_name]', $shipment->user->name, $subject);
	                            }
	                            if (strpos($subject, '[email]') !== FALSE) {
	                                $subject = str_replace('[email]', $shipment->user->email, $subject);
	                            }
	                            if (strpos($subject, '[phone]') !== FALSE) {
	                                $subject = str_replace('[phone]', $shipment->user->phone, $subject);
	                            }
	                            if (strpos($subject, '[destination]') !== FALSE) {
	                                $subject = str_replace('[destination]', $shipment->consignee_city->name, $subject);
	                            }


	                            if (strpos($body, '[tracking_number]') !== FALSE) {
	                                $table_details .= '<tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">Tracking Number</th><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">'. $shipment->tracking_number .'</td></tr>';
	                            }
	                            if (strpos($body, '[shipper_name]') !== FALSE) {
	                                $table_details .= '<tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">Shipper Name</th><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">'. $shipment->user->name .'</td></tr>';
	                            }
	                            if (strpos($body, '[email]') !== FALSE) {
	                                $table_details .= '<tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">Shipper Email</th><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">'. $shipment->user->email .'</td></tr>';
	                            }
	                            if (strpos($body, '[phone]') !== FALSE) {
	                                $table_details .= '<tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">Shipper Phone</th><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">'. $shipment->user->phone .'</td></tr>';
	                            }
	                            if (strpos($body, '[destination]') !== FALSE) {
	                                $table_details .= '<tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">Destination</th><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">'. $shipment->consignee_city->name .'</td></tr>';
	                            }

	                        }else{
	                            if (strpos($body, '[tracking_number]') !== FALSE) {
	                                $body = str_replace('[tracking_number]', '', $body);
	                            }
	                            if (strpos($body, '[shipper_name]') !== FALSE) {
	                                $body = str_replace('[shipper_name]', '', $body);
	                            }
	                            if (strpos($body, '[email]') !== FALSE) {
	                                $body = str_replace('[email]', '', $body);
	                            }
	                            if (strpos($body, '[phone]') !== FALSE) {
	                                $body = str_replace('[phone]', '', $body);
	                            }
	                            if (strpos($body, '[destination]') !== FALSE) {
	                                $body = str_replace('[destination]', '', $body);
	                            }
	                        }
	                    }

	                    if (strpos($subject, '[request_id]') !== FALSE) {
	                        $subject = str_replace('[request_id]', $crm_request->id, $subject);
	                    }
	                    if (strpos($subject, '[channel]') !== FALSE) {
	                        $subject = str_replace('[channel]', $crm_request->channel->channel, $subject);
	                    }
	                    if (strpos($subject, '[case_nature]') !== FALSE) {
	                        $subject = str_replace('[case_nature]', $crm_request->nature->name, $subject);
	                    }
	                    if (strpos($subject, '[case_nature_type]') !== FALSE) {
	                        $subject = str_replace('[case_nature_type]', $crm_request->nature->type, $subject);
	                    }
	                    if (strpos($subject, '[details]') !== FALSE) {
	                        $subject = str_replace('[details]', $crm_request->description, $subject);
	                    }

	                    if (strpos($body, '[channel]') !== FALSE) {
	                        $table_details .= '<tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">Channel</th><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">'. $crm_request->channel->channel .'</td></tr>';
	                    }
	                    if (strpos($body, '[case_nature]') !== FALSE) {
	                        $table_details .= '<tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">Case Nature</th><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">'. $crm_request->nature->name .'</td></tr>';
	                    }
	                    if($crm_request->case_nature_id != 3){
	                        if (strpos($body, '[case_nature_type]') !== FALSE) {
	                            $table_details .= '<tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">Case Nature Type</th><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">'. $crm_request->nature_type->type .'</td></tr>';
	                        }
	                    }
	                    if (strpos($body, '[details]') !== FALSE) {
	                        $table_details .= '<tr><th style="padding:5px; border: 1px solid black; border-collapse: collapse; font-weight: bold;">Description</th><td style="padding:5px; border: 1px solid black; border-collapse: collapse;">'. $crm_request->description .'</td></tr>';
	                    }

	                    $table_details = '<table style="padding:5px; border: 1px solid black; border-collapse: collapse;"><tbody>' . $table_details . '</tbody></table>';

	                    $first = TRUE;

	                    foreach ($possible_fields as $possible_field) {
	                        if (strpos($body, '[' . $possible_field . ']') !== FALSE) {
	                            if ($first) {
	                                $body = str_replace('[' . $possible_field . ']', $table_details, $body);

	                                $first = FALSE;
	                            }
	                            else {
	                                $body = str_replace('[' . $possible_field . ']', '', $body);
	                            }
	                        }
	                    }

	                    if($crm_request->status_id != 1) {
	                        $cc = 'complaints@trax.pk';
	                        self::email($subject, $body, $to, $cc);
	                    }
	                    else{
	                        self::email($subject, $body, $to);
	                    }
	            }
	          }
            else if ($id == 32) {
                  $nsa_shipment = Shipment::find($reference_1_id);

                  if (strpos($subject, '[nsa]') !== FALSE) {
                      $subject = str_replace('[nsa]', $reference_2_id, $subject);
                  }

                  if (strpos($body, '[nsa]') !== FALSE) {
                      $body = str_replace('[nsa]', $reference_2_id, $body);
                  }

                  if (strpos($subject, '[tracking_number]') !== FALSE) {
                      $subject = str_replace('[tracking_number]', $nsa_shipment->tracking_number, $subject);
                  }

                  if (strpos($body, '[tracking_number]') !== FALSE) {
                      $body = str_replace('[tracking_number]', $nsa_shipment->tracking_number, $body);
                  }

                  if(ShipperNotificationEmail::where('user_id',$nsa_shipment->user_id)->exists()){
                      $to = ShipperNotificationEmail::where('user_id',$nsa_shipment->user_id)->pluck('email')->toArray();
                  }else{
                      $to = $nsa_shipment->user->email;
                  }

                  self::email($subject, $body, $to);
            }else if($id == 33){
                  $nsa_shipment = Shipment::find($reference_1_id);
                 if (strpos($subject, '[tracking_number]') !== FALSE) {
                    $subject = str_replace('[tracking_number]', $nsa_shipment->tracking_number, $subject);
                 }
                 if (strpos($body, '[tracking_number]') !== FALSE) {
                    $body = str_replace('[tracking_number]', $nsa_shipment->tracking_number, $body);
                 }
                if (strpos($subject, '[destination]') !== FALSE) {
                    $subject = str_replace('[destination]', $nsa_shipment->consignee_city->name, $subject);
                }
                if (strpos($body, '[destination]') !== FALSE) {
                    $body = str_replace('[destination]', $nsa_shipment->consignee_city->name, $body);
                }

                if (strpos($subject, '[nsa_osa_estimated_charges]') !== FALSE) {
                    $subject = str_replace('[nsa_osa_estimated_charges]', $nsa_shipment->nsa_osa_estimated_charges, $subject);
                }
                if (strpos($body, '[nsa_osa_estimated_charges]') !== FALSE) {
                    $body = str_replace('[nsa_osa_estimated_charges]', $nsa_shipment->nsa_osa_estimated_charges, $body);
                }

                $journey = ShipmentsJourney::where('shipment_id', $reference_1_id)->where('shipper_status_id', 12)->whereIn('status_reason_id',[12, 34])->latest('id')->first();
                if (strpos($subject, '[remarks]') !== FALSE) {
                    $subject = str_replace('[remarks]', $journey->remarks, $subject);
                }
                if (strpos($body, '[remarks]') !== FALSE) {
                    $body = str_replace('[remarks]', $journey->remarks, $body);
                }

                $hub_id = $nsa_shipment->consignee_city->hub_id;

                if(ShipperNotificationEmail::where('user_id',$nsa_shipment->user_id)->exists()){
                    $to = ShipperNotificationEmail::where('user_id',$nsa_shipment->user_id)->pluck('email')->toArray();
                }else{
                    $to = $nsa_shipment->user->email;
                }

                $cc = array();

                $general_admins = Admin::whereIn('role_id', [15, 3, 7, 14])->where('status', 1);

                if ($general_admins->exists()) {
                  $cc = array_merge($cc, $general_admins->pluck('email')->toArray());
                }

                $related_admins = Admin::whereIn('role_id', [8, 9])->where('status', 1)->whereHas('hubs', function ($query) use ($hub_id) {
                  $query->where('hub_id', $hub_id);
                });

                if ($related_admins->exists()) {
                  $cc = array_merge($cc, $related_admins->pluck('email')->toArray());
                }

                self::email($subject, $body, $to);
            }
            else if($id == 34){
                $user_id = str_pad($reference_1_id, 6, '0', STR_PAD_LEFT);
                $updated_at = Carbon::now();
                $sale_person = Admin::where('id', $reference_2_id)->first();

                 if (strpos($subject, '[user_id]') !== FALSE) {
                    $subject = str_replace('[user_id]', $user_id, $subject);
                 }
                 if (strpos($body, '[user_id]') !== FALSE) {
                    $body = str_replace('[user_id]', $user_id, $body);
                 }
                if (strpos($subject, '[updated_at]') !== FALSE) {
                    $subject = str_replace('[updated_at]', $updated_at, $subject);
                }
                if (strpos($body, '[updated_at]') !== FALSE) {
                    $body = str_replace('[updated_at]', $updated_at, $body);
                }

                if (strpos($body, '[tagged_sales_person]') !== FALSE) {
                    $body = str_replace('[tagged_sales_person]', $sale_person->name, $body);
                }
                $to = array();

                $admins = Admin::whereIn('role_id', [2, 4])->where('status', 1);

                if ($admins->exists()) {
                    $to = array_merge($to, $admins->pluck('email')->toArray());
                }

                self::email($subject, $body, $to);
            }
            else if($id == 35){
                $shipment = Shipment::find($reference_1_id);
                $shipment_journey = ShipmentsJourney::where('shipment_id', $reference_1_id)->where('verification', 1)->latest('id')->first();
                if (strpos($body, '[tracking_number]') !== FALSE) {
                    $body = str_replace('[tracking_number]', $shipment->tracking_number, $body);
                }
                if (strpos($body, '[consignee_name]') !== FALSE) {
                    $body = str_replace('[consignee_name]', $shipment->consignee_name, $body);
                }
                if (strpos($body, '[shipper_name]') !== FALSE) {
                    $body = str_replace('[shipper_name]', $shipment->user->name, $body);
                }
                if (strpos($body, '[receiver_name]') !== FALSE) {
                    $body = str_replace('[receiver_name]', $shipment_journey->received_or_refused_by , $body);
                }

                $to = $shipment->consignee_phone_number_1;
                self::sms($body, $to);
            }
            else if($id == 36 || $id == 37){
                $account_a = User::where('id', $reference_1_id)->first();
                $account_b = User::where('id', $reference_2_id)->first();
//                $account_id_a = str_pad($account_a->id, 6, '0', STR_PAD_LEFT);
                $account_id_b = str_pad($account_b->id, 6, '0', STR_PAD_LEFT);
                $logo = '<img class="brand-logo trax" alt="Trax" src="' . asset('img/trax_logo.png') . '" width="100" height="50">';
                if (strpos($subject, '[account_id]') !== FALSE) {
                    $subject = str_replace('[account_id]', $account_id_b, $subject);
                }
                if (strpos($body, '[account_id]') !== FALSE) {
                    $body = str_replace('[account_id]', $account_id_b, $body);
                }
                if (strpos($subject, '[company_name_b]') !== FALSE) {
                    $subject = str_replace('[company_name_b]', $account_b->name, $subject);
                }
                if (strpos($body, '[company_name_b]') !== FALSE) {
                    $body = str_replace('[company_name_b]', $account_b->name, $body);
                }

                if (strpos($body, '[company_name_a]') !== FALSE) {
                    $body = str_replace('[company_name_a]', $account_a->name, $body);
                }
                if (strpos($body, '[trax_logo]') !== FALSE) {
                    $body = str_replace('[trax_logo]', $logo, $body);
                }
                $to = $account_a->email;

                self::email($subject, $body, $to);
            }
			else if($id == 38){
                $shipper = User::find($reference_1_id);
                if($shipper){
                    $terms = CRFTermsConditions::where('user_id', $shipper->id)->first();
                    if($terms){
                        $logo = '<img class="brand-logo trax" alt="Trax" src="' . asset('img/trax_logo.png') . '" width="100" height="50">';
                        $button = '<div class="row"><button onclick="window.open(' . route('cod.terms.accept', ['token' => $terms->token, 'id' => $shipper->id]) . ')" type="button" style="width: 100px; height: 40px; background-color: transparent; border: 2px solid black; border-radius: 5px; font-size: 25px; font-weight: bold;">Yes</button>';
                        $link = '<div class="row"><button onclick="window.open(' . route('cod.terms.download', ['token' => $terms->token, 'id' => $shipper->id]) . ')" type="button" style="height: 40px; background-color: transparent; border: 2px solid black; border-radius: 5px; font-size: 18px; font-weight: bold;">CRF Download</button>';
                        if (strpos($subject, '[shipper_name]') !== FALSE) {
                            $subject = str_replace('[shipper_name]', $shipper->name, $subject);
                        }
                        if (strpos($body, '[shipper_name]') !== FALSE) {
                            $body = str_replace('[shipper_name]', $shipper->name, $body);
                        }
                        if (strpos($body, '[trax_logo]') !== FALSE) {
                            $body = str_replace('[trax_logo]', $logo, $body);
                        }

                        if (strpos($body, '[button]') !== FALSE) {
                            $body = str_replace('[button]', $button, $body);
                        }

                        if (strpos($body, '[link]') !== FALSE) {
                            $body = str_replace('[link]', $link, $body);
                        }

                        $to = $shipper->email;
                        self::email($subject, $body, $to);
                    }
                }
            }
			else if($id == 39){
                $journey = ShipmentsJourney::where('shipment_id', $reference_1_id)->where('shipper_status_id', 25)->latest('id')->first();
                $shipment = Shipment::find($reference_1_id);
                if (strpos($subject, '[tracking_number]') !== FALSE) {
                    $subject = str_replace('[tracking_number]', $shipment->tracking_number, $subject);
                }
                if (strpos($body, '[tracking_number]') !== FALSE) {
                    $body = str_replace('[tracking_number]', $shipment->tracking_number, $body);
                }
                if (strpos($body, '[status_updated_at]') !== FALSE) {
                    $body = str_replace('[status_updated_at]', $journey->created_at, $body);
                }

                if (strpos($body, '[receiver_name]') !== FALSE) {
                    $body = str_replace('[receiver_name]', $journey->received_or_refused_by, $body);
                }
                $to = $shipment->user->email;

                self::email($subject, $body, $to);
            }
			else if($id == 40){
                $delivery_note = DeliveryNote::find($reference_1_id);
                if($delivery_note){
                    $rider = Rider::find($delivery_note->rider_id);
                    $delivery_note_id = str_pad($delivery_note->id, 6, '0', STR_PAD_LEFT);

                    if (strpos($body, '[rider_name]') !== FALSE) {
                        $body = str_replace('[rider_name]', $rider->name, $body);
                    }

                    if (strpos($body, '[delivery_note_id]') !== FALSE) {
                        $body = str_replace('[delivery_note_id]', $delivery_note_id, $body);
                    }

                    if (strpos($body, '[password]') !== FALSE) {
                        $body = str_replace('[password]', $delivery_note->password, $body);
                    }
                    $to = $rider->phone;
                    self::sms($body, $to);
                }
            }        }
      }
    }

    static public function custom($type, $subject, $body, $to) {
      if ($type == 1) {
        self::email($subject, $body, $to);
      }
    }
}