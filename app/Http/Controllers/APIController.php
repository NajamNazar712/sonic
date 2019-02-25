<?php

namespace App\Http\Controllers;

use App\Http\Models\CorporateRateStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use App\Http\Controllers\NotificationsController;

use Validator;
use Illuminate\Validation\Rule;

use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\RateStatus;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\City;
use App\Http\Models\CityDelivery;

use Carbon\Carbon;

class APIController extends Controller
{
    private $names = [
      'person_of_contact' => 'Person of Contact',
      'phone_number' => 'Phone Number',
      'email_address' => 'Email Address',
      'address' => 'Address',
      'city_id' => 'City ID',

      'service_type_id' => 'Service Type ID',
      'pickup_address_id' => 'Pickup Address ID',
      'information_display' => 'Information Display',
      'consignee_city_id' => 'Consignee City ID',
      'consignee_name' => 'Consignee Name',
      'consignee_address' => 'Consignee Address',
      'consignee_phone_number_1' => 'Consignee Phone Number 1',
      'consignee_phone_number_2' => 'Consignee Phone Number 2',
      'consignee_email_address' => 'Consignee Email Address',
      'order_id' => 'Order ID',
      'package_type' => 'Package Type',
      'pickup_date' => 'Pickup Date',
      'special_instructions' => 'Special Instructions',
      'estimated_weight' => 'Estimated Weight',
      'shipping_mode_id' => 'Shipping Mode ID',
      'same_day_timing_id' => 'Same Day Timing ID',
      'amount' => 'Collection Amount',
      'payment_mode_id' => 'Payment Mode ID',

      'item_product_type_id' => 'Item Product Type ID',
      'item_description' => 'Item Description',
      'item_quantity' => 'Item Quantity',
      'item_insurance' => 'Item Insurance',
      'product_value' => 'Product Value',

      'replacement_item_product_type_id' => 'Replacement Item Product Type ID',
      'replacement_item_description' => 'Replacement Item Description',
      'replacement_item_quantity' =>'Replacement Item Quantity',

      'items' => 'Item(s)',
      'items.*.item_product_type_id' => 'Item Product Type ID',
      'items.*.item_description' => 'Item Description',
      'items.*.item_quantity' => 'Item Quantity',
      'items.*.item_insurance' => 'Item Insurance',
      'items.*.product_value' => 'Product Value',

      'tracking_number' => 'Tracking Number',
      'type' => 'Type'
    ];

    private $messages = [
      'required' => ':attribute is Required.',
      'required_if' => ':attribute is Required when :other is :value.',
      'filled' => ':attribute is Optional but cannot be Empty if Present.',
      'integer' => ':attribute must be an Integer.',
      'numeric' => ':attribute must be a Number.',
      'boolean' => ':attribute must be 0 or 1.',
      'digits_between' => ':attribute must be between :min and :max Digits.',
      'email' => ':attribute must be a Valid Email Address.',
      'exists' => 'Given :attribute is of Invalid ID.',
      'unique' => ':attribute is already Present.',
      'date_format' => ':attribute must be of valid Format, required Format is: YYYY-MM-DD.',

      'phone_number.regex' => ':attribute format is Invalid, required Format is: 03000000000.',

      'consignee_phone_number_1.regex' => ':attribute format is Invalid, required Format is: 03000000000.',
      'consignee_phone_number_2.regex' => ':attribute format is Invalid, required Format is: 03000000000.'
    ];

    public function pickup_addresses(Request $request) {
      $user_id = $request->user_id;

      $pickup_addresses = User::find($user_id)->shipping;

      if (count($pickup_addresses)) {
        $details = array();

        foreach ($pickup_addresses as $pickup_address) {
          if ($pickup_address->hidden == 0) {
            $detail = array();

            $detail['id'] = $pickup_address->id;
            $detail['person_of_contact'] = $pickup_address->poc;
            $detail['phone_number'] = $pickup_address->phone;
            $detail['email_address'] = $pickup_address->email;
            $detail['address'] = $pickup_address->pickup_address;
            $detail['status'] = $pickup_address->status;
            $detail['default'] = ($pickup_address->default_address) ? TRUE : FALSE;

            $detail['city'] = array();

            $city = $pickup_address->city;

            if ($city->status && $city->zone_id) {
              $detail['city']['id'] = $city->id;
              $detail['city']['name'] = $city->name;

              $details[] = $detail;
            }
          }
        }

        if (!empty($details)) {
          return response()->json(['status' => 0, 'message' => 'Pickup Addresses', 'pickup_addresses' => $details]);
        }
        else {
          return response()->json(['status' => 1, 'message' => 'No Pickup Address']);
        }
      }
      else {
        return response()->json(['status' => 1, 'message' => 'No Pickup Address']);
      }
    }

    public function pickup_address_add(Request $request) {
      $user_id = $request->user_id;

      $rules = [
        'person_of_contact' => ['required', 'between:1,190'],
        'phone_number' => ['required', 'regex:/^[0][0-9]{10}$/'],
        'email_address' => ['required', 'email'],
        'address' => ['required', 'between:1,190'],
        'city_id' => ['required', 'integer', 'digits_between:1,10', 'exists:cities,id']
      ];

      $validate = Validator::make($request->all(), $rules, $this->messages);

      $validate->setAttributeNames($this->names);

      if ($validate->fails()) {
        return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
      }
      else {
        $city = City::find($request->input('city_id'));

        if (!$city->status) {
          return response()->json(['status' => 1, 'message' => 'City ID #' . $request->input('city_id')]) . ' is deactivated';
        }

        if (!$city->zone_id) {
          return response()->json(['status' => 1, 'message' => 'City ID #' . $request->input('city_id')]) . ' is deactivated';
        }

        if (!$city->pickup) {
          return response()->json(['status' => 1, 'message' => 'Pickup is not allowed for City ID #' . $request->input('city_id')]);
        }

        $person_of_contact = $request->input('person_of_contact');
        $phone_number = substr_replace($request->input('phone_number'), '-', 4, 0);
        $email_address = $request->input('email_address');
        $address = $request->input('address');
        $city_id = $request->input('city_id');

        $pickup_address = new UserShippingInfo();

        $pickup_address->user_id = $user_id;
        $pickup_address->poc = $person_of_contact;
        $pickup_address->phone = $phone_number;
        $pickup_address->email = $email_address;
        $pickup_address->pickup_address = $address;
        $pickup_address->city_id = $city_id;

        $pickup_address->save();

        $id = $pickup_address->id;

        return response()->json(['status' => 0, 'message' => 'Pickup Address has been added', 'id' => $id]);
      }
    }

    public function shipment_book(Request $request) {
      $user_id = $request->user_id;

      $rules = [
        'service_type_id' => ['required', 'integer', 'digits_between:1,10', 'exists:booking_types,id'],
        'pickup_address_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function($query) use($user_id) {
          $query->where('user_id', $user_id)->where('hidden', 0);
        })],
        'information_display' => ['required', 'boolean'],
        'consignee_city_id' => ['required', 'integer', 'digits_between:1,10', 'exists:cities,id'],
        'consignee_name' => ['required', 'between:1,100'],
        'consignee_address' => ['required', 'between:1,190'],
        'consignee_phone_number_1' => ['required', 'regex:/^[0][0-9]{10}$/'],
        'consignee_phone_number_2' => ['nullable', 'filled', 'regex:/^[0][0-9]{10}$/'],
        'consignee_email_address' => ['nullable', 'filled', 'email'],
        'order_id' => ['nullable', 'filled', Rule::unique('shipments')->where(function($query) use($user_id) {
          $query->where('user_id', $user_id);
        })],
        'package_type' => ['required_if:service_type_id,3', 'boolean'],
        'pickup_date' => ['required', 'date_format:Y-m-d', 'after:yesterday'],
        'special_instructions' => ['nullable', 'filled', 'between:0,190'],
        'estimated_weight' => ['required', 'numeric', 'between:0.1,10000'],
        'shipping_mode_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id'],
        'same_day_timing_id' => ['required_if:shipping_mode_id,4', 'integer', 'digits_between:1,10', 'exists:shipping_mode_same_day_timings,id'],
        'amount' => ['required', 'integer', 'digits_between:1,20', 'between:0,1000000'],
        'payment_mode_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('payment_modes', 'id')->where(function($query) {
          $query->whereNotIn('id', [2, 3]);
        })],

        'item_product_type_id' => ['required_if:service_type_id,1,2', 'integer', 'digits_between:1,10', 'exists:products,id'],
        'item_description' => ['required_if:service_type_id,1,2', 'between:0,250'],
        'item_quantity' => ['required_if:service_type_id,1,2', 'integer', 'digits_between:1,10', 'between:1,1000'],
        'item_insurance' => ['required_if:service_type_id,1,2', 'boolean'],
        'product_value' => ['required_if:item_insurance,1', 'integer', 'digits_between:1,20', 'between:1,100000'],

        'replacement_item_product_type_id' => ['required_if:service_type_id,2', 'integer', 'digits_between:1,10', 'exists:products,id'],
        'replacement_item_description' => ['required_if:service_type_id,2', 'between:0,250'],
        'replacement_item_quantity' => ['required_if:service_type_id,2', 'integer', 'digits_between:1,10', 'between:1,1000'],

        'items' => ['required_if:service_type_id,3', 'array'],
        'items.*.item_product_type_id' => ['required_if:service_type_id,3', 'integer', 'digits_between:1,10', 'exists:products,id'],
        'items.*.item_description' => ['required_if:service_type_id,3', 'between:0,250'],
        'items.*.item_quantity' => ['required_if:service_type_id,3', 'integer', 'digits_between:1,10', 'between:1,1000'],
        'items.*.item_insurance' => ['required_if:service_type_id,3', 'boolean'],
        'items.*.product_value' => ['required_if:service_type_id,3', 'integer', 'digits_between:1,20', 'between:1,100000']
      ];

      $validate = Validator::make($request->all(), $rules, $this->messages);

      $validate->setAttributeNames($this->names);

      if ($validate->fails()) {
        return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
      }
      else {
        if (!RateStatus::where('user_id', $user_id)->where('shipping_mode_id', $request->input('shipping_mode_id'))->where('status', 1)->exists()) {
          return response()->json(['status' => 1, 'message' => 'Booking is not enabled for Shipping Mode ID #' . $request->input('shipping_mode_id') . ' on your Account']);
        }

        $user_shipping_info = UserShippingInfo::find($request->input('pickup_address_id'));

        if (!$user_shipping_info->status) {
          return response()->json(['status' => 1, 'message' => 'Pickup Address ID #' . $request->input('pickup_address_id') . ' is disabled']);
        }

        if (!$user_shipping_info->city->status) {
          return response()->json(['status' => 1, 'message' => 'Pickup Address\'s City ID #' . $user_shipping_info->city_id]) . ' is deactivated';
        }

        if (!$user_shipping_info->city->zone_id) {
          return response()->json(['status' => 1, 'message' => 'Pickup Address\'s City ID #' . $user_shipping_info->city_id]) . ' is deactivated';
        }

        if (!$user_shipping_info->city->pickup) {
          return response()->json(['status' => 1, 'message' => 'Pickup is not allowed for City ID #' . $user_shipping_info->city_id]);
        }

        $consignee_city = City::find($request->input('consignee_city_id'));

        if (!$consignee_city->status) {
          return response()->json(['status' => 1, 'message' => 'Consignee City ID #' . $request->input('consignee_city_id')]) . ' is deactivated';
        }

        if (!$consignee_city->zone_id) {
          return response()->json(['status' => 1, 'message' => 'Consignee City ID #' . $request->input('consignee_city_id')]) . ' is deactivated';
        }

        $pickup_city_id = $user_shipping_info->city_id;

        if ($request->input('consignee_city_id') != $pickup_city_id && $request->input('shipping_mode_id') == 4) {
          return response()->json(['status' => 1, 'message' => 'Same Day Delivery is not available for Different City Shipment']);
        }

        if (!CityDelivery::where('city_id', $request->input('consignee_city_id'))->where('booking_type_id', $request->input('service_type_id'))->where('shipping_mode_id', $request->input('shipping_mode_id'))->exists()) {
          return response()->json(['status' => 1, 'message' => 'Delivery is not allowed for City ID #' . $request->input('consignee_city_id') . ' with Service Type ID #' . $request->input('service_type_id') . ' and Shipping Mode ID #' . $request->input('shipping_mode_id')]);
        }

        $service_type_id = $request->input('service_type_id');
        $pickup_address_id = $request->input('pickup_address_id');
        $information_display = $request->input('information_display');
        $consignee_city_id = $request->input('consignee_city_id');
        $consignee_name = $request->input('consignee_name');
        $consignee_address = $request->input('consignee_address');
        $consignee_phone_number_1 = substr_replace($request->input('consignee_phone_number_1'), '-', 4, 0);

        if ($request->filled('consignee_phone_number_2')) {
            $consignee_phone_number_2 = substr_replace($request->input('consignee_phone_number_2'), '-', 4, 0);
        }
        else {
          $consignee_phone_number_2 = NULL;
        }

        if ($request->filled('consignee_email_address')) {
          $consignee_email_address = $request->input('consignee_email_address');
        }
        else {
          $consignee_email_address = NULL;
        }

        if ($request->filled('order_id')) {
          $order_id = $request->input('order_id');
        }
        else {
          $order_id = NULL;
        }

        if ($service_type_id != 3 || $request->input('package_type') == 1) {
          $package_type = TRUE;
        }
        else {
          $package_type = FALSE;
        }

        $pickup_date = $request->input('pickup_date');

        if ($request->filled('special_instructions')) {
          $special_instructions = $request->input('special_instructions');
        }
        else {
          $special_instructions = NULL;
        }

        $estimated_weight = $request->input('estimated_weight');
        $shipping_mode_id = $request->input('shipping_mode_id');

        if ($shipping_mode_id == 4) {
          $same_day_timing_id = $request->input('same_day_timing_id');
        }
        else {
          $same_day_timing_id = NULL;
        }

        $amount = $request->input('amount');
        $payment_mode_id = $request->input('payment_mode_id');

        $shipment_id = ShipperShipmentBookController::book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id);

        $tracking_number = ShipperShipmentBookController::generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);

        if ($service_type_id == 1) {
          $item_product_type_id = $request->input('item_product_type_id');

          if ($request->filled('item_description')) {
            $item_description = $request->input('item_description');
          }
          else {
            $item_description = NULL;
          }

          $item_quantity = $request->input('item_quantity');

          if ($request->input('item_insurance') == 1) {
            $item_price = str_replace(',', '', $request->input('product_value'));
            $item_insurance = TRUE;
          }
          else {
            $item_price = NULL;
            $item_insurance = FALSE;
          }

          $item_type = 0;

          ShipperShipmentBookController::add_item($shipment_id, $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);
        }
        else if ($service_type_id == 2) {
          $item_product_type_id = $request->input('item_product_type_id');

          if ($request->filled('item_description')) {
            $item_description = $request->input('item_description');
          }
          else {
            $item_description = NULL;
          }

          $item_quantity = $request->input('item_quantity');

          if ($request->input('item_insurance') == 1) {
            $item_price = str_replace(',', '', $request->input('product_value'));
            $item_insurance = TRUE;
          }
          else {
            $item_price = NULL;
            $item_insurance = FALSE;
          }

          $item_type = 0;

          ShipperShipmentBookController::add_item($shipment_id, $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);

          $replacement_item_product_type_id = $request->input('replacement_item_product_type_id');

          if ($request->filled('replacement_item_description')) {
            $replacement_item_description = $request->input('replacement_item_description');
          }
          else {
            $replacement_item_description = NULL;
          }

          $replacement_item_quantity = $request->input('replacement_item_quantity');

          $replacement_item_price = NULL;
          $replacement_item_insurance = NULL;
          $replacement_item_type = 1;

          ShipperShipmentBookController::add_item($shipment_id, $replacement_item_product_type_id, $replacement_item_description, $replacement_item_quantity, $replacement_item_price, $replacement_item_insurance, $replacement_item_type);
        }
        else if ($service_type_id == 3) {
          foreach ($request->input('items') as $item) {
            $item_product_type_id = $item['item_product_type_id'];

            if (isset($item['item_description']) && !empty($item['item_description'])) {
              $item_description = $item['item_description'];
            }
            else {
              $item_description = NULL;
            }

            $item_quantity = $item['item_quantity'];
            $item_price = $item['product_value'];

            if (isset($item['item_insurance']) && !empty($item['item_insurance'])) {
              $item_insurance = TRUE;
            }
            else {
              $item_insurance = FALSE;
            }

            $item_type = 2;

            ShipperShipmentBookController::add_item($shipment_id, $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);
          }
        }

        NotificationsController::send(2, $shipment_id);

        return response()->json(['status' => 0, 'message' => 'Shipment has been Booked!', 'tracking_number' => $tracking_number]);
      }
    }

    public function corporate_shipment_book(Request $request) {
        $user_id = $request->user_id;

        $rules = [
            'service_type_id' => ['required', 'integer', 'digits_between:1,10', 'exists:booking_types,id'],
            'pickup_address_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function($query) use($user_id) {
                $query->where('user_id', $user_id)->where('hidden', 0);
            })],
            'delivery_type_id' => ['required', 'integer', 'digits_between:1,10', 'exists:delivery_types,id'],
            'charges_mode_id' => ['required', 'integer', 'digits_between:1,10', 'exists:charges_modes,id'],
            'information_display' => ['required', 'boolean'],
            'consignee_city_id' => ['required', 'integer', 'digits_between:1,10', 'exists:cities,id'],
            'consignee_name' => ['required', 'between:1,100'],
            'consignee_address' => ['required', 'between:1,190'],
            'consignee_phone_number_1' => ['required', 'regex:/^[0][0-9]{10}$/'],
            'consignee_phone_number_2' => ['nullable', 'filled', 'regex:/^[0][0-9]{10}$/'],
            'consignee_email_address' => ['nullable', 'filled', 'email'],
            'order_id' => ['nullable', 'filled', Rule::unique('shipments')->where(function($query) use($user_id) {
                $query->where('user_id', $user_id);
            })],
            'package_type' => ['required_if:service_type_id,3', 'boolean'],
            'pickup_date' => ['required', 'date_format:Y-m-d', 'after:yesterday'],
            'special_instructions' => ['nullable', 'filled', 'between:0,190'],
            'estimated_weight' => ['required', 'numeric', 'between:0.1,10000'],
            'shipping_mode_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id'],
            'same_day_timing_id' => ['required_if:shipping_mode_id,4', 'integer', 'digits_between:1,10', 'exists:shipping_mode_same_day_timings,id'],
            'amount' => ['required', 'integer', 'digits_between:1,20', 'between:0,1000000'],
            'payment_mode_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('payment_modes', 'id')->where(function($query) {
                $query->whereNotIn('id', [2, 3]);
            })],

            'item_product_type_id' => ['required_if:service_type_id,1,2', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description' => ['required_if:service_type_id,1,2', 'between:0,250'],
            'item_quantity' => ['required_if:service_type_id,1,2', 'integer', 'digits_between:1,10', 'between:1,1000'],
            'item_insurance' => ['required_if:service_type_id,1,2', 'boolean'],
            'product_value' => ['required_if:item_insurance,1', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'replacement_item_product_type_id' => ['required_if:service_type_id,2', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'replacement_item_description' => ['required_if:service_type_id,2', 'between:0,250'],
            'replacement_item_quantity' => ['required_if:service_type_id,2', 'integer', 'digits_between:1,10', 'between:1,1000'],

            'items' => ['required_if:service_type_id,3', 'array'],
            'items.*.item_product_type_id' => ['required_if:service_type_id,3', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'items.*.item_description' => ['required_if:service_type_id,3', 'between:0,250'],
            'items.*.item_quantity' => ['required_if:service_type_id,3', 'integer', 'digits_between:1,10', 'between:1,1000'],
            'items.*.item_insurance' => ['required_if:service_type_id,3', 'boolean'],
            'items.*.product_value' => ['required_if:service_type_id,3', 'integer', 'digits_between:1,20', 'between:1,100000']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        }
        else {
            if (!CorporateRateStatus::where('user_id', $user_id)->where('shipping_mode_id', $request->input('shipping_mode_id'))->where('status', 1)->exists()) {
                return response()->json(['status' => 1, 'message' => 'Booking is not enabled for Shipping Mode ID #' . $request->input('shipping_mode_id') . ' on your Account']);
            }

            $user_shipping_info = UserShippingInfo::find($request->input('pickup_address_id'));

            if (!$user_shipping_info->status) {
                return response()->json(['status' => 1, 'message' => 'Pickup Address ID #' . $request->input('pickup_address_id') . ' is disabled']);
            }

            if (!$user_shipping_info->city->status) {
                return response()->json(['status' => 1, 'message' => 'Pickup Address\'s City ID #' . $user_shipping_info->city_id]) . ' is deactivated';
            }

            if (!$user_shipping_info->city->zone_id) {
                return response()->json(['status' => 1, 'message' => 'Pickup Address\'s City ID #' . $user_shipping_info->city_id]) . ' is deactivated';
            }

            if (!$user_shipping_info->city->pickup) {
                return response()->json(['status' => 1, 'message' => 'Pickup is not allowed for City ID #' . $user_shipping_info->city_id]);
            }

            $consignee_city = City::find($request->input('consignee_city_id'));

            if (!$consignee_city->status) {
                return response()->json(['status' => 1, 'message' => 'Consignee City ID #' . $request->input('consignee_city_id')]) . ' is deactivated';
            }

            if (!$consignee_city->zone_id) {
                return response()->json(['status' => 1, 'message' => 'Consignee City ID #' . $request->input('consignee_city_id')]) . ' is deactivated';
            }

            $pickup_city_id = $user_shipping_info->city_id;

            if ($request->input('consignee_city_id') != $pickup_city_id && $request->input('shipping_mode_id') == 4) {
                return response()->json(['status' => 1, 'message' => 'Same Day Delivery is not available for Different City Shipment']);
            }

            if (!CityDelivery::where('city_id', $request->input('consignee_city_id'))->where('booking_type_id', $request->input('service_type_id'))->where('shipping_mode_id', $request->input('shipping_mode_id'))->exists()) {
                return response()->json(['status' => 1, 'message' => 'Delivery is not allowed for City ID #' . $request->input('consignee_city_id') . ' with Service Type ID #' . $request->input('service_type_id') . ' and Shipping Mode ID #' . $request->input('shipping_mode_id')]);
            }
            if($request->input('charges_mode_id') == 1){
                return response()->json(['status' => 1, 'message' => 'Charges Mode ID '. $request->input('charges_mode_id') .' is not allowed for Corporate Account']);
            }

            $service_type_id = $request->input('service_type_id');
            $pickup_address_id = $request->input('pickup_address_id');
            $delivery_type_id = $request->input('delivery_type_id');
            $charges_mode_id = $request->input('charges_mode_id');
            $information_display = $request->input('information_display');
            $consignee_city_id = $request->input('consignee_city_id');
            $consignee_name = $request->input('consignee_name');
            $consignee_phone_number_1 = substr_replace($request->input('consignee_phone_number_1'), '-', 4, 0);

            $consignee_city_name = City::where('id', $consignee_city_id)->first();
            if($delivery_type_id == 2){
                $consignee_address = 'TRAX Office ' . $consignee_city_name['name'];
            }
            else {
                $consignee_address = $request->input('consignee_address');
            }
            if ($request->filled('consignee_phone_number_2')) {
                $consignee_phone_number_2 = substr_replace($request->input('consignee_phone_number_2'), '-', 4, 0);
            }
            else {
                $consignee_phone_number_2 = NULL;
            }

            if ($request->filled('consignee_email_address')) {
                $consignee_email_address = $request->input('consignee_email_address');
            }
            else {
                $consignee_email_address = NULL;
            }

            if ($request->filled('order_id')) {
                $order_id = $request->input('order_id');
            }
            else {
                $order_id = NULL;
            }

            if ($service_type_id != 3 || $request->input('package_type') == 1) {
                $package_type = TRUE;
            }
            else {
                $package_type = FALSE;
            }

            $pickup_date = $request->input('pickup_date');

            if ($request->filled('special_instructions')) {
                $special_instructions = $request->input('special_instructions');
            }
            else {
                $special_instructions = NULL;
            }

            $estimated_weight = $request->input('estimated_weight');
            $shipping_mode_id = $request->input('shipping_mode_id');

            if ($shipping_mode_id == 4) {
                $same_day_timing_id = $request->input('same_day_timing_id');
            }
            else {
                $same_day_timing_id = NULL;
            }

            $amount = $request->input('amount');
            $payment_mode_id = $request->input('payment_mode_id');

            $shipment_id = ShipperShipmentBookController::corporate_book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $estimated_weight, $shipping_mode_id, $delivery_type_id, $same_day_timing_id, $charges_mode_id, $amount, $payment_mode_id);

            $tracking_number = ShipperShipmentBookController::generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);

            if ($service_type_id == 1) {
                $item_product_type_id = $request->input('item_product_type_id');

                if ($request->filled('item_description')) {
                    $item_description = $request->input('item_description');
                }
                else {
                    $item_description = NULL;
                }

                $item_quantity = $request->input('item_quantity');

                if ($request->input('item_insurance') == 1) {
                    $item_price = str_replace(',', '', $request->input('product_value'));
                    $item_insurance = TRUE;
                }
                else {
                    $item_price = NULL;
                    $item_insurance = FALSE;
                }

                $item_type = 0;

                ShipperShipmentBookController::add_item($shipment_id, $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);
            }
            else if ($service_type_id == 2) {
                $item_product_type_id = $request->input('item_product_type_id');

                if ($request->filled('item_description')) {
                    $item_description = $request->input('item_description');
                }
                else {
                    $item_description = NULL;
                }

                $item_quantity = $request->input('item_quantity');

                if ($request->input('item_insurance') == 1) {
                    $item_price = str_replace(',', '', $request->input('product_value'));
                    $item_insurance = TRUE;
                }
                else {
                    $item_price = NULL;
                    $item_insurance = FALSE;
                }

                $item_type = 0;

                ShipperShipmentBookController::add_item($shipment_id, $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);

                $replacement_item_product_type_id = $request->input('replacement_item_product_type_id');

                if ($request->filled('replacement_item_description')) {
                    $replacement_item_description = $request->input('replacement_item_description');
                }
                else {
                    $replacement_item_description = NULL;
                }

                $replacement_item_quantity = $request->input('replacement_item_quantity');

                $replacement_item_price = NULL;
                $replacement_item_insurance = NULL;
                $replacement_item_type = 1;

                ShipperShipmentBookController::add_item($shipment_id, $replacement_item_product_type_id, $replacement_item_description, $replacement_item_quantity, $replacement_item_price, $replacement_item_insurance, $replacement_item_type);
            }
            else if ($service_type_id == 3) {
                foreach ($request->input('items') as $item) {
                    $item_product_type_id = $item['item_product_type_id'];

                    if (isset($item['item_description']) && !empty($item['item_description'])) {
                        $item_description = $item['item_description'];
                    }
                    else {
                        $item_description = NULL;
                    }

                    $item_quantity = $item['item_quantity'];
                    $item_price = $item['product_value'];

                    if (isset($item['item_insurance']) && !empty($item['item_insurance'])) {
                        $item_insurance = TRUE;
                    }
                    else {
                        $item_insurance = FALSE;
                    }

                    $item_type = 2;

                    ShipperShipmentBookController::add_item($shipment_id, $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);
                }
            }

            NotificationsController::send(2, $shipment_id);

            return response()->json(['status' => 0, 'message' => 'Shipment has been Booked!', 'tracking_number' => $tracking_number]);
        }
    }
    public function shipment_status(Request $request) {
      $user_id = $request->user_id;

      $rules = [
        'tracking_number' => ['required', 'integer', 'digits_between:12,20', Rule::exists('shipments', 'tracking_number')->where(function($query) use($user_id) {
          $query->where('user_id', $user_id);
        })],
        'type' => ['required', 'boolean']
      ];

      $validate = Validator::make($request->all(), $rules, $this->messages);

      $validate->setAttributeNames($this->names);

      if ($validate->fails()) {
        return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
      }
      else {
        $tracking_number = $request->tracking_number;
        $type = $request->type;

        $shipment = Shipment::where('tracking_number', $tracking_number)->first();

        if ($type == 0) {
          $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->latest()->first();

          if ($shipment_journey) {
            $current_status = $shipment_journey->shipment_status_shipper->name;
          }
          else {
            $current_status = $shipment->status_shipper->name;
          }
        }
        else {
          $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->whereNotNull('consignee_status_id')->latest()->first();

          if ($shipment_journey) {
            $current_status = $shipment_journey->shipment_status_consignee->name;
          }
          else {
            $current_status = $shipment->status_consignee->name;
          }
        }

        return response()->json(['status' => 0, 'message' => 'Status of Shipment #' . $tracking_number, 'current_status' => $current_status]);
      }
    }

    public function shipment_track(Request $request) {
      $user_id = $request->user_id;

      $rules = [
        'tracking_number' => ['required', 'integer', 'digits_between:12,20', Rule::exists('shipments', 'tracking_number')->where(function($query) use($user_id) {
          $query->where('user_id', $user_id);
        })],
        'type' => ['required', 'boolean']
      ];

      $validate = Validator::make($request->all(), $rules, $this->messages);

      $validate->setAttributeNames($this->names);

      if ($validate->fails()) {
        return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
      }
      else {
        $tracking_number = $request->tracking_number;
        $type = $request->type;

        $shipment = Shipment::where('tracking_number', $tracking_number)->first();

        $details = array();

        $details['tracking_number'] = $tracking_number;

        $shipper = $shipment->user;

        $details['shipper']['name'] = $shipper->name;

        if ($type == 0) {
          $details['shipper']['account_number'] = $shipper->id;
          $details['shipper']['phone_number_1'] = $shipper->phone;
          $details['shipper']['phone_number_2'] = $shipper->phone2;
          $details['shipper']['address'] = $shipper->address;
        }

        $details['shipper']['origin'] = $shipper->city->name;

        $details['consignee']['name'] = $shipment->consignee_name;
        $details['consignee']['phone_number_1'] = $shipment->consignee_phone_number_1;
        $details['consignee']['phone_number_2'] = $shipment->consignee_phone_number_2;
        $details['consignee']['destination'] = $shipment->consignee_city->name;
        $details['consignee']['address'] = $shipment->consignee_address;

        foreach ($shipment->items as $item) {
          $item_details = array();

          $item_details['product_type'] = $item->product->product_name;
          $item_details['description'] = $item->description;
          $item_details['quantity'] = $item->quantity;

          $details['order_information']['items'][] = $item_details;
        }

        if ($type == 0) {
          $details['order_information']['weight'] = ($shipment->actual_weight) ? floatval($shipment->actual_weight) : floatval($shipment->estimated_weight);
          $details['order_information']['shipping_mode'] = $shipment->shipping_mode->mode;
          $details['order_information']['amount'] = $shipment->amount;
          $details['order_information']['instructions'] = $shipment->special_instructions;
        }

        if ($type == 0) {
          foreach ($shipment->shipment_journey as $journey) {
            if ($journey->verification) {
              $journey_details = array();

              $journey_details['date_time'] = Carbon::parse($journey->created_at)->format('d/m/Y h:i A');
              $journey_details['status'] = $journey->shipment_status_shipper->name;

              $journey_details['status_reason'] = ($journey->status_reason_id) ? $journey->shipment_status_reason->name : NULL;

              $details['tracking_history'][] = $journey_details;
            }
          }
        }
        else {
          foreach ($shipment->shipment_journey as $journey) {
            if ($journey->consignee_status_id != NULL) {
              if ($journey->verification) {
                $journey_details = array();

                $journey_details['date_time'] = Carbon::parse($journey->created_at)->format('d/m/Y h:i A');
                $journey_details['status'] = $journey->shipment_status_consignee->name;

                $journey_details['status_reason'] = ($journey->status_reason_id) ? $journey->shipment_status_reason->name : NULL;

                $details['tracking_history'][] = $journey_details;
              }
            }
          }
        }

        return response()->json(['status' => 0, 'message' => 'Tracking of Shipment #' . $tracking_number, 'details' => $details]);
      }
    }

    public function shipment_charges(Request $request) {
      $user_id = $request->user_id;

      $rules = [
        'tracking_number' => ['required', 'integer', 'digits_between:12,20', Rule::exists('shipments', 'tracking_number')->where(function($query) use($user_id) {
          $query->where('user_id', $user_id);
        })]
      ];

      $validate = Validator::make($request->all(), $rules, $this->messages);

      $validate->setAttributeNames($this->names);

      if ($validate->fails()) {
        return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
      }
      else {
        $tracking_number = $request->tracking_number;

        $shipment = Shipment::where('tracking_number', $tracking_number)->first();

        $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->latest()->first();

        if ($shipment_journey) {
          $current_status_id = $shipment_journey->shipper_status_id;
        }
        else {
          $current_status_id = $shipment->shipper_status_id;
        }

        $charges = array();

        if ($shipment->packaging_material_request) {
          $charges['packaging_material_charges'] = $shipment->packaging_material_charges;
        }
        else if (in_array($current_status_id, [14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46])) {
          if ($shipment->weight_charges) {
            $charges['weight_charges'] = $shipment->weight_charges;
          }

          if ($shipment->cash_handling_charges) {
            $charges['cash_handling_charges'] = $shipment->cash_handling_charges;
          }

          if ($shipment->insurance_charges) {
            $charges['insurance_charges'] = $shipment->insurance_charges;
          }

          if ($shipment->fuel_surcharge) {
            $charges['fuel_surcharge'] = $shipment->fuel_surcharge;
          }

          if ($shipment->replacement_charges) {
            $charges['replacement_charges'] = $shipment->replacement_charges;
          }

          if ($shipment->try_and_buy_charges) {
            $charges['try_and_buy_charges'] = $shipment->try_and_buy_charges;
          }
        }
        else if (in_array($current_status_id, [20, 21, 22, 23, 24, 25, 44])) {
          if ($shipment->weight_charges) {
            $charges['weight_charges'] = $shipment->weight_charges;
          }

          if ($shipment->insurance_charges) {
            $charges['insurance_charges'] = $shipment->insurance_charges;
          }

          if ($shipment->fuel_surcharge) {
            $charges['fuel_surcharge'] = $shipment->fuel_surcharge;
          }

          if ($shipment->return_charges) {
            $charges['return_charges'] = $shipment->return_charges;
          }
        }
        else {
          if ($shipment->weight_charges) {
            $charges['weight_charges'] = $shipment->weight_charges;
          }

          if ($shipment->insurance_charges) {
            $charges['insurance_charges'] = $shipment->insurance_charges;
          }

          if ($shipment->fuel_surcharge) {
            $charges['fuel_surcharge'] = $shipment->fuel_surcharge;
          }
        }

        if (!empty($charges)) {
          return response()->json(['status' => 0, 'message' => 'Charges of Shipment #' . $tracking_number, 'charges' => $charges]);
        }
        else {
          return response()->json(['status' => 1, 'message' => 'No Charges']);
        }
      }
    }

    public function shipment_payment_status(Request $request) {
      $user_id = $request->user_id;

      $rules = [
        'tracking_number' => ['required', 'integer', 'digits_between:12,20', Rule::exists('shipments', 'tracking_number')->where(function($query) use($user_id) {
          $query->where('user_id', $user_id);
        })]
      ];

      $validate = Validator::make($request->all(), $rules, $this->messages);

      $validate->setAttributeNames($this->names);

      if ($validate->fails()) {
        return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
      }
      else {
        $tracking_number = $request->tracking_number;

        $shipment = Shipment::where('tracking_number', $tracking_number)->first();

        $shipment_payment_journey = $shipment->shipment_payment_journey;

        if (!$shipment_payment_journey->isEmpty()) {
          $current_payment_status = $shipment_payment_journey->first()->status->name;

          return response()->json(['status' => 0, 'message' => 'Payment Status of Shipment #' . $tracking_number, 'current_payment_status' => $current_payment_status]);
        }
        else {
          return response()->json(['status' => 1, 'message' => 'No Payment Status']);
        }
      }
    }

    public function shipment_payments(Request $request) {
      $user_id = $request->user_id;

      $rules = [
        'tracking_number' => ['required', 'integer', 'digits_between:12,20', Rule::exists('shipments', 'tracking_number')->where(function($query) use($user_id) {
          $query->where('user_id', $user_id);
        })]
      ];

      $validate = Validator::make($request->all(), $rules, $this->messages);

      $validate->setAttributeNames($this->names);

      if ($validate->fails()) {
        return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
      }
      else {
        $tracking_number = $request->tracking_number;

        $shipment = Shipment::where('tracking_number', $tracking_number)->first();

        $done_payment_shipments = $shipment->done_payment_shipments;

        if (!$done_payment_shipments->isEmpty()) {
          $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->latest()->first();

          if ($shipment_journey) {
            $current_status_id = $shipment_journey->shipper_status_id;
          }
          else {
            $current_status_id = $shipment->shipper_status_id;
          }

          $charges = array();

          if ($shipment->packaging_material_request) {
            $charges['packaging_material_charges'] = $shipment->packaging_material_charges;
          }
          else if (in_array($current_status_id, [14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46])) {
            if ($shipment->weight_charges) {
              $charges['weight_charges'] = $shipment->weight_charges;
            }

            if ($shipment->cash_handling_charges) {
              $charges['cash_handling_charges'] = $shipment->cash_handling_charges;
            }

            if ($shipment->insurance_charges) {
              $charges['insurance_charges'] = $shipment->insurance_charges;
            }

            if ($shipment->fuel_surcharge) {
              $charges['fuel_surcharge'] = $shipment->fuel_surcharge;
            }

            if ($shipment->replacement_charges) {
              $charges['replacement_charges'] = $shipment->replacement_charges;
            }

            if ($shipment->try_and_buy_charges) {
              $charges['try_and_buy_charges'] = $shipment->try_and_buy_charges;
            }
          }
          else if (in_array($current_status_id, [20, 21, 22, 23, 24, 25, 44])) {
            if ($shipment->weight_charges) {
              $charges['weight_charges'] = $shipment->weight_charges;
            }

            if ($shipment->insurance_charges) {
              $charges['insurance_charges'] = $shipment->insurance_charges;
            }

            if ($shipment->fuel_surcharge) {
              $charges['fuel_surcharge'] = $shipment->fuel_surcharge;
            }

            if ($shipment->return_charges) {
              $charges['return_charges'] = $shipment->return_charges;
            }
          }
          else {
            if ($shipment->weight_charges) {
              $charges['weight_charges'] = $shipment->weight_charges;
            }

            if ($shipment->insurance_charges) {
              $charges['insurance_charges'] = $shipment->insurance_charges;
            }

            if ($shipment->fuel_surcharge) {
              $charges['fuel_surcharge'] = $shipment->fuel_surcharge;
            }
          }

          $current_payment_status = NULL;

          $shipment_payment_journey = $shipment->shipment_payment_journey;

          if (!$shipment_payment_journey->isEmpty()) {
            $current_payment_status = $shipment_payment_journey->first()->status->name;
          }

          $payments = array();

          foreach ($done_payment_shipments as $done_payment_shipment) {
            $payment = array();

            $payment['id'] = $done_payment_shipment->done_payment_id;
            $payment['datetime'] = $done_payment_shipment->updated_at;
            $payment['type'] = $done_payment_shipment->type;
            $payment['amount'] = $done_payment_shipment->amount;
            $payment['charges'] = $done_payment_shipment->charges;
            $payment['gst'] = $done_payment_shipment->gst;
            $payment['payable'] = $done_payment_shipment->payable;

            $payments[] = $payment;
          }

          return response()->json(['status' => 0, 'message' => 'Payment(s) of Shipment #' . $tracking_number, 'charges' => $charges, 'current_payment_status' => $current_payment_status, 'payments' => $payments]);
        }
        else {
          return response()->json(['status' => 1, 'message' => 'No Payments']);
        }
      }
    }

    public function cities(Request $request) {
      $user_id = $request->user_id;

      $cities = City::where('status', 1);

      if ($cities->exists()) {
        $cities = $cities->get();

        $details = array();

        foreach ($cities as $city) {
          $detail = array();

          $detail['id'] = $city->id;
          $detail['name'] = $city->name;
          $detail['pickup'] = ($city->pickup) ? TRUE : FALSE;
          $detail['delivery'] = array();

          foreach ($city->deliveries as $delivery) {
            $detail['delivery'][$delivery->booking_type->booking_type][] = $delivery->shipping_mode->mode;
          }

          $details[] = $detail;
        }

        return response()->json(['status' => 0, 'message' => 'Pickup and Delivery Information of Cities', 'cities' => $details]);
      }
      else {
        return response()->json(['status' => 1, 'message' => ' No City Present']);
      }
    }
}