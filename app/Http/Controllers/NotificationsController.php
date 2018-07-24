<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;

use App\Http\Models\Notification;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipment;
use App\Http\Models\PickupNote;
use App\Http\Models\CargoConsignment;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Dispute;

use GuzzleHttp\Client;

class NotificationsController extends Controller
{
    static private function sms($body, $to) {
      $client = new Client(['base_uri' => 'http://sms.its.com.pk/api/', 'http_errors' => FALSE]);

      $response = $client->get('', [
        'query' => [
          'username' => 'trax',
          'password' => '123456',
          'receiver' => str_replace('-', '', $to),
          'msgdata' => $body
        ]
      ]);
    }

    static private function email($subject, $body, $to) {
      Mail::send('notifications.email', ['body' => nl2br($body)], function ($message) use ($subject, $to) {
          $message->subject($subject);

          $message->to($to);
      });
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

            $to = $shipper->email;

            foreach ($fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                $subject = str_replace('[' . $key . ']', $shipper[$field], $subject);
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipper[$field], $body);
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
            $fields = ['account_id' => 'id', 'company_name' => 'name', 'order_id' => 'order_id', 'pickup_date' => 'pickup_date', 'amount' => 'amount', 'tracking_number' => 'tracking_number'];

            $shipment = Shipment::find($reference_1_id);

            $to = $shipment->user->phone;

            foreach ($fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
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
            $fields = ['company_name' => 'name', 'consignee_name' => 'consignee_name', 'consignee_address' => 'consignee_address', 'order_id' => 'order_id', 'amount' => 'amount', 'tracking_number' => 'tracking_number'];

            $shipment = Shipment::find($reference_1_id);

            $to = $shipment->user->phone;

            foreach ($fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
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
            $possible_fields = ['pickup_city', 'consignee_name', 'consignee_city', 'order_id', 'weight', 'tracking_number'];

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

              $user_wise_shipments[$shipment->user_id][] = $details;
            }

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

              $to = $shipper->email;

              $shipment_details = '';

              foreach ($shipments as $shipment) {
                foreach ($present_fields as $field) {
                  $shipment_details .= $shipment[$field] . ', ';
                }

                $shipment_details .= substr($shipment_details, 0, -2) . PHP_EOL;
              }

              foreach ($present_fields as $field) {
                if ($field != $first_field) {
                  $body = str_replace('[' . $field . ']', '', $body);
                }
              }

              $body = str_replace('[' . $first_field . ']', $shipment_details, $body);

              self::email($subject, $body, $to);
            }
          }
          else if ($id == 5) {
            $cargo_fields = ['cargo_number' => 'id', 'departure_at' => 'created_at'];

            $shipment_fields = ['company_name' => 'name', 'order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $cargo_consignment = CargoConsignment::find($reference_1_id);

            $shipment = Shipment::find($reference_2_id);

            $to = $shipment->user->email;

            foreach ($cargo_fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                $subject = str_replace('[' . $key . ']', $cargo_consignment[$field], $subject);
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $cargo_consignment[$field], $body);
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

            self::email($subject, $body, $to);
          }
          else if ($id == 6) {
            $cargo_fields = ['cargo_number' => 'id', 'departure_at' => 'created_at'];

            $shipment_fields = ['company_name' => 'name', 'order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $cargo_consignment = CargoConsignment::find($reference_1_id);

            $shipment = Shipment::find($reference_2_id);

            $to = $shipment->user->phone;

            foreach ($cargo_fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $cargo_consignment[$field], $body);
              }
            }

            foreach ($shipment_fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
            }

            self::sms($body, $to);
          }
          else if ($id == 7) {
            $cargo_fields = ['cargo_number' => 'id', 'arrival_at' => 'updated_at'];

            $shipment_fields = ['company_name' => 'name', 'order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $cargo_consignment = CargoConsignment::find($reference_1_id);

            $shipment = Shipment::find($reference_2_id);

            $to = $shipment->user->email;

            foreach ($cargo_fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                $subject = str_replace('[' . $key . ']', $cargo_consignment[$field], $subject);
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $cargo_consignment[$field], $body);
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

            self::email($subject, $body, $to);
          }
          else if ($id == 8) {
            $cargo_fields = ['cargo_number' => 'id', 'arrival_at' => 'updated_at'];

            $shipment_fields = ['company_name' => 'name', 'order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $cargo_consignment = CargoConsignment::find($reference_1_id);

            $shipment = Shipment::find($reference_2_id);

            $to = $shipment->user->phone;

            foreach ($cargo_fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $cargo_consignment[$field], $body);
              }
            }

            foreach ($shipment_fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
            }

            self::sms($body, $to);
          }
          else if ($id == 9) {
            // $fields = ['cargo_number' => 'id', 'departure_at' => 'created_at', 'seal_number' => 'seal_number', 'builty_number' => 'builty_number', 'expected_arrival_date', 'expected_arrival_date'];

            // 'shipping_mode', 'transport_mode', 'vendor', 'sender', 'tracking_number'

            // $cargo_consignment = CargoConsignment::find($reference_1_id);

            // //ROLES TO BE ADDED
            // $to = Admin::whereIn('role', [])->get()->pluck('email');

            // foreach ($fields as $key => $field) {
            //   if (strpos($subject, '[' . $key . ']') !== FALSE) {
            //     $subject = str_replace('[' . $key . ']', $cargo_consignment[$field], $subject);
            //   }

            //   if (strpos($body, '[' . $key . ']') !== FALSE) {
            //     $body = str_replace('[' . $key . ']', $cargo_consignment[$field], $body);
            //   }
            // }

            // if (strpos($subject, '[shipping_mode]') !== FALSE) {
            //   $subject = str_replace('[shipping_mode]', $cargo_consignment->shipping_mode->mode, $subject);
            // }

            // if (strpos($body, '[shipping_mode]') !== FALSE) {
            //   $body = str_replace('[shipping_mode]', $cargo_consignment->shipping_mode->mode, $body);
            // }

            // if (strpos($subject, '[transport_mode]') !== FALSE) {
            //   $subject = str_replace('[transport_mode]', $cargo_consignment->transport_mode->name, $subject);
            // }

            // if (strpos($body, '[transport_mode]') !== FALSE) {
            //   $body = str_replace('[transport_mode]', $cargo_consignment->transport_mode->name, $body);
            // }

            // if (strpos($subject, '[vendor]') !== FALSE) {
            //   $subject = str_replace('[vendor]', $cargo_consignment->transport_mode_vendor->name, $subject);
            // }

            // if (strpos($body, '[vendor]') !== FALSE) {
            //   $body = str_replace('[vendor]', $cargo_consignment->transport_mode_vendor->name, $body);
            // }

            // if (strpos($subject, '[sender]') !== FALSE) {
            //   $subject = str_replace('[sender]', $cargo_consignment->sender->name, $subject);
            // }

            // if (strpos($body, '[sender]') !== FALSE) {
            //   $body = str_replace('[sender]', $cargo_consignment->sender->name, $body);
            // }

            // if (strpos($body, '[tracking_number]') !== FALSE) {
            //   $tracking_numbers = '';

            //   foreach ($cargo_consignment->cargo_consignment_shipments => $cargo_consignment_shipment) {
            //     $shipment = $cargo_consignment_shipment->shipment;

            //     $tracking_numbers .= $shipment->tracking_number . PHP_EOL;
            //   }

            //   $body = str_replace('[tracking_number]', $tracking_numbers, $body);
            // }

            // self::email($subject, $body, $to);
          }
          else if ($id == 10) {
            $delivery_note_fields = ['delivery_note_number' => 'id', 'departure_at' => 'created_at'];

            $shipment_fields = ['company_name' => 'name', 'order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $delivery_note = DeliveryNote::find($reference_1_id);

            $shipment = Shipment::find($reference_2_id);

            $to = $shipment->user->email;

            foreach ($delivery_note_fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                $subject = str_replace('[' . $key . ']', $delivery_note[$field], $subject);
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $delivery_note[$field], $body);
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
              $subject = str_replace('[rider]', $delivery_note->rider->name, $subject);
            }

            if (strpos($body, '[rider]') !== FALSE) {
              $body = str_replace('[rider]', $delivery_note->rider->name, $body);
            }

            self::email($subject, $body, $to);
          }
          else if ($id == 11) {
            $delivery_note_fields = ['delivery_note_number' => 'id', 'departure_at' => 'created_at'];

            $shipment_fields = ['company_name' => 'name', 'order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $delivery_note = DeliveryNote::find($reference_1_id);

            $shipment = Shipment::find($reference_2_id);

            $to = $shipment->user->phone;

            foreach ($delivery_note_fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $delivery_note[$field], $body);
              }
            }

            foreach ($shipment_fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
            }

            if (strpos($body, '[rider]') !== FALSE) {
              $body = str_replace('[rider]', $delivery_note->rider->name, $body);
            }

            self::sms($body, $to);
          }
          else if ($id == 12) {
            $delivery_note_fields = ['delivery_note_number' => 'id', 'departure_at' => 'created_at'];

            $shipment_fields = ['company_name' => 'name', 'consignee_name' => 'consignee_name', 'consignee_address' => 'consignee_address', 'order_id' => 'order_id', 'amount' => 'amount', 'tracking_number' => 'tracking_number'];

            $delivery_note = DeliveryNote::find($reference_1_id);

            $shipment = Shipment::find($reference_2_id);

            $to = $shipment->consignee_phone_number_1;

            foreach ($delivery_note_fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $delivery_note[$field], $body);
              }
            }

            foreach ($shipment_fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
            }

            if (strpos($body, '[rider]') !== FALSE) {
              $body = str_replace('[rider]', $delivery_note->rider->name, $body);
            }

            if (strpos($body, '[payment_mode]') !== FALSE) {
              $body = str_replace('[payment_mode]', $shipment->payment_mode->mode, $body);
            }

            self::sms($body, $to);
          }
          else if ($id == 13) {
            $delivery_note_fields = ['delivery_note_number' => 'id', 'departure_at' => 'created_at'];

            $shipment_fields = ['company_name' => 'name', 'order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $delivery_note = DeliveryNote::find($reference_1_id);

            foreach ($delivery_note_fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                $subject = str_replace('[' . $key . ']', $delivery_note[$field], $subject);
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $delivery_note[$field], $body);
              }
            }

            if (strpos($subject, '[rider]') !== FALSE) {
              $subject = str_replace('[rider]', $delivery_note->rider->name, $subject);
            }

            if (strpos($body, '[rider]') !== FALSE) {
              $body = str_replace('[rider]', $delivery_note->rider->name, $body);
            }

            $original_subject = $subject;
            $original_body = $body;

            foreach ($delivery_note->delivery_note_shipments as $delivery_note_shipment) {
              $shipment = $delivery_note_shipment->shipment;

              $to = $shipment->user->email;

              foreach ($shipment_fields as $key => $field) {
                if (strpos($subject, '[' . $key . ']') !== FALSE) {
                  $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
                }

                if (strpos($body, '[' . $key . ']') !== FALSE) {
                  $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                }
              }

              if (strpos($subject, '[status]') !== FALSE) {
                $status_parts = explode(' - ', $shipment->status_shipper->name);

                $subject = str_replace('[status]', $status_parts[1], $subject);
              }

              if (strpos($body, '[status]') !== FALSE) {
                $status_parts = explode(' - ', $shipment->status_shipper->name);

                $body = str_replace('[status]', $status_parts[1], $body);
              }

              self::email($subject, $body, $to);

              $subject = $original_subject;
              $body = $original_body;
            }
          }
          else if ($id == 14) {
            $delivery_note_fields = ['delivery_note_number' => 'id', 'departure_at' => 'created_at'];

            $shipment_fields = ['company_name' => 'name', 'order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $delivery_note = DeliveryNote::find($reference_1_id);

            foreach ($delivery_note_fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $delivery_note[$field], $body);
              }
            }

            if (strpos($body, '[rider]') !== FALSE) {
              $body = str_replace('[rider]', $delivery_note->rider->name, $body);
            }

            $original_body = $body;

            foreach ($delivery_note->delivery_note_shipments as $delivery_note_shipment) {
              $shipment = $delivery_note_shipment->shipment;

              $to = $shipment->user->phone;

              foreach ($shipment_fields as $key => $field) {
                if (strpos($body, '[' . $key . ']') !== FALSE) {
                  $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                }
              }

              if (strpos($body, '[status]') !== FALSE) {
                $status_parts = explode(' - ', $shipment->status_shipper->name);

                $body = str_replace('[status]', $status_parts[1], $body);
              }

              self::sms($body, $to);

              $body = $original_body;
            }
          }
          else if ($id == 15) {
            if ($reference_1_id != 0) {
              $return_note_fields = ['return_note_number' => 'id', 'departure_at' => 'created_at'];

              $shipment_fields = ['company_name' => 'name', 'order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

              $return_note = ReturnNote::find($reference_1_id);

              foreach ($return_note_fields as $key => $field) {
                if (strpos($subject, '[' . $key . ']') !== FALSE) {
                  $subject = str_replace('[' . $key . ']', $return_note[$field], $subject);
                }

                if (strpos($body, '[' . $key . ']') !== FALSE) {
                  $body = str_replace('[' . $key . ']', $return_note[$field], $body);
                }
              }

              if (strpos($subject, '[rider]') !== FALSE) {
                $subject = str_replace('[rider]', $return_note->rider->name, $subject);
              }

              if (strpos($body, '[rider]') !== FALSE) {
                $body = str_replace('[rider]', $return_note->rider->name, $body);
              }

              $original_subject = $subject;
              $original_body = $body;

              foreach ($return_note->return_note_shipments as $return_note_shipment) {
                $shipment = $return_note_shipment->shipment;

                $to = $shipment->user->email;

                foreach ($shipment_fields as $key => $field) {
                  if (strpos($subject, '[' . $key . ']') !== FALSE) {
                    $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
                  }

                  if (strpos($body, '[' . $key . ']') !== FALSE) {
                    $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                  }
                }

                if (strpos($subject, '[status]') !== FALSE) {
                  $status_parts = explode(' - ', $shipment->status_shipper->name);

                  $subject = str_replace('[status]', $status_parts[1], $subject);
                }

                if (strpos($body, '[status]') !== FALSE) {
                  $status_parts = explode(' - ', $shipment->status_shipper->name);

                  $body = str_replace('[status]', $status_parts[1], $body);
                }

                self::email($subject, $body, $to);

                $subject = $original_subject;
                $body = $original_body;
              }
            }
            else {
              $remove_fields = ['return_note_number', 'departure_at', 'rider'];

              $fields = ['company_name' => 'name', 'order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

              $shipment = Shipment::find($reference_2_id);

              $to = $shipment->user->email;

              foreach ($remove_fields => $field) {
                if (strpos($subject, '[' . $field . ']') !== FALSE) {
                  $subject = str_replace('[' . $field . ']', '-', $subject);
                }

                if (strpos($body, '[' . $key . ']') !== FALSE) {
                  $body = str_replace('[' . $key . ']', '-', $body);
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

              if (strpos($subject, '[status]') !== FALSE) {
                $status_parts = explode(' - ', $shipment->status_shipper->name);

                $subject = str_replace('[status]', $status_parts[1], $subject);
              }

              if (strpos($body, '[status]') !== FALSE) {
                $status_parts = explode(' - ', $shipment->status_shipper->name);

                $body = str_replace('[status]', $status_parts[1], $body);
              }

              self::email($subject, $body, $to);
            }
          }
          else if ($id == 16) {
            if ($reference_1_id != 0) {
              $return_note_fields = ['return_note_number' => 'id', 'departure_at' => 'created_at'];

              $shipment_fields = ['company_name' => 'name', 'order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

              $return_note = ReturnNote::find($reference_1_id);

              foreach ($return_note_fields as $key => $field) {
                if (strpos($body, '[' . $key . ']') !== FALSE) {
                  $body = str_replace('[' . $key . ']', $return_note[$field], $body);
                }
              }

              if (strpos($body, '[rider]') !== FALSE) {
                $body = str_replace('[rider]', $return_note->rider->name, $body);
              }

              $original_body = $body;

              foreach ($return_note->return_note_shipments as $return_note_shipment) {
                $shipment = $return_note_shipment->shipment;

                $to = $shipment->user->phone;

                foreach ($shipment_fields as $key => $field) {
                  if (strpos($body, '[' . $key . ']') !== FALSE) {
                    $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                  }
                }

                if (strpos($body, '[status]') !== FALSE) {
                  $status_parts = explode(' - ', $shipment->status_shipper->name);

                  $body = str_replace('[status]', $status_parts[1], $body);
                }

                self::sms($body, $to);

                $body = $original_body;
              }
            }
            else {
              $remove_fields = ['return_note_number', 'departure_at', 'rider'];

              $fields = ['company_name' => 'name', 'order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

              $shipment = Shipment::find($reference_2_id);

              $to = $shipment->user->phone;

              foreach ($remove_fields => $field) {
                if (strpos($body, '[' . $field . ']') !== FALSE) {
                  $body = str_replace('[' . $field . ']', '-', $body);
                }
              }

              foreach ($fields as $key => $field) {
                if (strpos($body, '[' . $key . ']') !== FALSE) {
                  $body = str_replace('[' . $key . ']', $shipment[$field], $body);
                }
              }

              if (strpos($body, '[status]') !== FALSE) {
                $status_parts = explode(' - ', $shipment->status_shipper->name);

                $body = str_replace('[status]', $status_parts[1], $body);
              }

              self::sms($body, $to);
            }
          }
          else if ($id == 17) {
            $fields = ['account_id' => 'id', 'company_name' => 'name', 'order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $shipment = Shipment::find($reference_1_id);

            $new_shipment = Shipment::find($reference_2_id);

            $to = $shipment->user->email;

            foreach ($fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
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
            $fields = ['account_id' => 'id', 'company_name' => 'name', 'order_id' => 'order_id', 'tracking_number' => 'tracking_number'];

            $shipment = Shipment::find($reference_1_id);

            $new_shipment = Shipment::find($reference_2_id);

            $to = $shipment->user->phone;

            foreach ($fields as $key => $field) {
              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
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
              $launched_by = $dispute->user;
            }

            $to = $launched_by->email;

            foreach ($fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                $subject = str_replace('[' . $key . ']', $dispute[$field], $subject);
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $dispute[$field], $body);
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
              $subject = str_replace('[city]', $dispute->city->type, $subject);
            }

            if (strpos($body, '[city]') !== FALSE) {
              $body = str_replace('[city]', $dispute->city->type, $body);
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

            self::email($subject, $body, $to);
          }
          else if ($id == 20) {
            $fields = ['company_name' => 'name', 'consignee_name' => 'consignee_name', 'consignee_address' => 'consignee_address', 'order_id' => 'order_id', 'amount' => 'amount', 'tracking_number' => 'tracking_number'];

            $shipment = Shipment::find($reference_1_id);

            $to = $shipment->user->email;

            foreach ($fields as $key => $field) {
              if (strpos($subject, '[' . $key . ']') !== FALSE) {
                $subject = str_replace('[' . $key . ']', $shipment[$field], $subject);
              }

              if (strpos($body, '[' . $key . ']') !== FALSE) {
                $body = str_replace('[' . $key . ']', $shipment[$field], $body);
              }
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

            self::email($subject, $body, $to);
          }
        }
      }
    }

    static public function custom($type, $subject, $body, $to) {
      if ($type == 1) {
        self::email($subject, $body, $to);
      }
    }
}