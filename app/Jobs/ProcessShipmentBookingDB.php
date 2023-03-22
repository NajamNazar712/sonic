<?php

namespace App\Jobs;

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
use Carbon\Carbon;

use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use App\Http\Controllers\NotificationsController;

use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\City;
use App\Http\Models\ShipmentDetail;

class ProcessShipmentBookingDB implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $booking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $booking)
    {
        $this->queue = 'shipment_booking_db';
        $this->booking = $booking;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user_id = $this->booking['user_id'];
        $service_type_id = $this->booking['service_type_id'];
        if(array_key_exists("open_shipment",$this->booking)){
            if (strtolower($this->booking['open_shipment']) == 'yes') {
                $open_shipment = 1;
            } else {
                $open_shipment = 0;
            }
        }else{
            $open_shipment = 0;

        }
        
        
        if($service_type_id == 5){
            if (!empty(trim($this->booking['consignee_email_address']))) {
                $user_email_id = $this->booking['consignee_email_address'];
            }
            else{
                $user = User::find($user_id);
                $user_email_id = $user->email;
            }
            $pickup_city_id = City::where('name', $this->booking['consignee_city_name'])->first()->id;

            $phone_number = ShipperShipmentBookController::phone_number($this->booking['consignee_phone_number_1']);
            $pickup_address_id = ShipperShipmentBookController::add_pickup_address($user_id, $this->booking['consignee_address'], $this->booking['consignee_name'], NULL, substr_replace($phone_number, '-', 4, 0), $user_email_id, $pickup_city_id, 0, TRUE);

            $pickup_delivery_address_id = $this->booking['pickup_address_id'];
            $pickup_delivery_address = UserShippingInfo::find($pickup_delivery_address_id);

            $consignee_city_id = $pickup_delivery_address->city_id;
            $consignee_name = $pickup_delivery_address->poc;
            $consignee_address = $pickup_delivery_address->pickup_address;
            $consignee_phone_number_1 = $pickup_delivery_address->phone;
            $consignee_phone_number_2 = NULL;
            $consignee_email_address = $pickup_delivery_address->email;
            $information_display = TRUE;
            $payment_mode_id = 1;
            $self_collection = FALSE;
        }
        else{
            $pickup_address_id = $this->booking['pickup_address_id'];
            $pickup_city_id = UserShippingInfo::find($this->booking['pickup_address_id'])->city_id;

            if (strtolower($this->booking['information_display']) == 'yes') {
                $information_display = TRUE;
            } else {
                $information_display = FALSE;
            }


            $consignee_city_id = City::where('name', $this->booking['consignee_city_name'])->first()->id;
            $consignee_name = $this->booking['consignee_name'];
            $consignee_address = $this->booking['consignee_address'];
            $consignee_phone_number_1 = ShipperShipmentBookController::phone_number($this->booking['consignee_phone_number_1']);

            if (!empty(trim($this->booking['consignee_phone_number_2']))) {
                $consignee_phone_number_2 = ShipperShipmentBookController::phone_number($this->booking['consignee_phone_number_2']);
            }
            else{
                $consignee_phone_number_2 = null;
            }

            if (!empty(trim($this->booking['consignee_email_address']))) {
                $consignee_email_address = $this->booking['consignee_email_address'];
            } else {
                $consignee_email_address = NULL;
            }
            $payment_mode_id = $this->booking['payment_mode_id'];
            if (array_key_exists("self_collection",$this->booking))
            {
                if (strtolower($this->booking['self_collection']) == 'yes') {
                    $self_collection = TRUE;
                } else {
                    $self_collection = FALSE;
                }
            }else{
                $self_collection = FALSE;
            }
            
        }

        $charges_mode_id = $this->booking['charges_mode_id'];
        if (!empty(trim($this->booking['order_id']))) {
            $order_id = $this->booking['order_id'];
        } else {
            $order_id = NULL;
        }

        if (!empty(trim($this->booking['special_instructions']))) {
            $special_instructions = $this->booking['special_instructions'];
        } else {
            $special_instructions = NULL;
        }

        $estimated_weight = $this->booking['estimated_weight'];
        $shipping_mode_id = $this->booking['shipping_mode_id'];

        if ($shipping_mode_id == 4) {
            $same_day_timing_id = $this->booking['same_day_timing_id'];
        } else {
            $same_day_timing_id = NULL;
        }

        $package_type = TRUE;

        if ($service_type_id == 3) {
            $try_and_buy_charges = $this->booking['try_and_buy_charges'];
            $amount = 0;
            $parcel_value = 0;
        }
        elseif ($service_type_id == 5){
            $try_and_buy_charges = NULL;
            $amount = 0;
            $parcel_value = 0;
        }
        else {
            $try_and_buy_charges = NULL;
            $amount = $this->booking['amount'];
            $parcel_value = $this->booking['parcel_value'];
        }
        $pieces_quantity = 1;

        if($service_type_id == 1){
            if (isset($this->booking['pieces_quantity'])) {
                $pieces_quantity = $this->booking['pieces_quantity'];
            }
        }

        $return_address_id = NULL;
        if($service_type_id == 1 || $service_type_id == 2){
            if (isset($this->booking['return_address_id'])) {
                $return_address_id = $this->booking['return_address_id'];
            }
        }
        $business_category_id = $this->booking['business_category_id'];
        if($business_category_id == 1){
            if (strpos($consignee_phone_number_1, '-') !== 4) {
                $consignee_phone_number_1 = substr_replace($consignee_phone_number_1, '-', 4, 0);
            }
            if ($consignee_phone_number_2 != null && strpos($consignee_phone_number_2, '-') !== 4) {
                $consignee_phone_number_2 = substr_replace($consignee_phone_number_2, '-', 4, 0);
            }
        }

        if (empty($payment_mode_id)) {
            $payment_mode_id = 1;
        }

        if ($this->booking['account_type_id'] == 1) {
            $shipment_id = ShipperShipmentBookController::book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id, $charges_mode_id, $try_and_buy_charges,$pieces_quantity, $self_collection, $business_category_id, $open_shipment, $return_address_id,$parcel_value);
        }
        else {
            if($service_type_id == 5){
                $delivery_type_id = 1;
            }
            else{
                if($business_category_id == 2){
                    $delivery_type_id = 1;
                }
                else{
                    $delivery_type_id = $this->booking['delivery_type_id'];
                }
            }

            if ($delivery_type_id == 2) {
                $consignee_address = 'TRAX Office ' . $this->booking['consignee_city_name'];
            }

            $shipment_id = ShipperShipmentBookController::corporate_book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $delivery_type_id, $same_day_timing_id, $charges_mode_id, $amount, $payment_mode_id, $pieces_quantity, $self_collection, $business_category_id, $try_and_buy_charges, $open_shipment, $return_address_id,$parcel_value);
        }
            
            if($this->booking['substitute_user_id'] != null){
                $substitute_user_shipment = new SubstituteUserShipment();
                $substitute_user_shipment->substitute_user_id = $this->booking['substitute_user_id'];
                $substitute_user_shipment->shipment_id = $shipment_id;
                $substitute_user_shipment->save();
            }

        if($this->booking['prefix'] != NULL){
            $tracking_number = ShipperShipmentBookController::generate_prefix_tracking_number($shipment_id, $this->booking['order_id']);
        }
        else{
            $tracking_number = ShipperShipmentBookController::generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);
        }

        if($this->booking['business_category_id'] == 2){
            $international_shipment = new InternationalShipment();
            $international_shipment->shipment_id = $shipment_id;
            $international_shipment->postal_code = $this->booking['postal_code'];
            $international_shipment->save();
        }

        if($this->booking['order_date'] != null){
            $order_date = new ShipmentOrderDate();
            $order_date->shipment_id = $shipment_id;
            $order_date->order_date = $this->booking['order_date'];
            $order_date->save();
        }

        if($this->booking['shipper_reference_number_1'] != null || $this->booking['shipper_reference_number_2'] != null || $this->booking['shipper_reference_number_3'] != null || $this->booking['shipper_reference_number_4'] != null || $this->booking['shipper_reference_number_5'] != null){
            $shipper_reference = new ShipmentShipperReference();
            $shipper_reference->shipment_id = $shipment_id;
            if($this->booking['shipper_reference_number_1'] != null) {
                $shipper_reference->reference_1 = $this->booking['shipper_reference_number_1'];
            }
            if($this->booking['shipper_reference_number_2'] != null) {
                $shipper_reference->reference_2 = $this->booking['shipper_reference_number_2'];
            }
            if($this->booking['shipper_reference_number_3'] != null) {
                $shipper_reference->reference_3 = $this->booking['shipper_reference_number_3'];
            }
            if($this->booking['shipper_reference_number_4'] != null) {
                $shipper_reference->reference_4 = $this->booking['shipper_reference_number_4'];
            }
            if($this->booking['shipper_reference_number_5'] != null) {
                $shipper_reference->reference_5 = $this->booking['shipper_reference_number_5'];
            }
            $shipper_reference->save();
        }

        if ($service_type_id == 1 || $service_type_id == 5) {
            $item_product_type_id = $this->booking['item_product_type_id'];

            if (!empty(trim($this->booking['item_description']))) {
                $item_description = $this->booking['item_description'];
            } else {
                $item_description = NULL;
            }

            $item_quantity = $this->booking['item_quantity'];

            if (strtolower($this->booking['item_insurance']) == 'yes') {
                $item_price = str_replace(',', '', $this->booking['item_price']);
                $item_insurance = TRUE;
            } else {
                $item_price = NULL;
                $item_insurance = FALSE;
            }

            $item_type = 0;

            ShipperShipmentBookController::add_item($shipment_id, $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);
            if($service_type_id == 1 && $pieces_quantity > 1){
                ShipperShipmentBookController::create_shipment_pieces($shipment_id, $pieces_quantity);
            }
        }
        else if ($service_type_id == 2) {
            $item_product_type_id = $this->booking['item_product_type_id'];

            if (!empty(trim($this->booking['item_description']))) {
                $item_description = $this->booking['item_description'];
            } else {
                $item_description = NULL;
            }

            $item_quantity = $this->booking['item_quantity'];

            if (strtolower($this->booking['item_insurance']) == 'yes') {
                $item_price = str_replace(',', '', $this->booking['item_price']);
                $item_insurance = TRUE;
            } else {
                $item_price = NULL;
                $item_insurance = FALSE;
            }

            $item_type = 0;

            ShipperShipmentBookController::add_item($shipment_id, $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);

            $replacement_item_product_type_id = $this->booking['replacement_item_product_type_id'];

            if (!empty(trim($this->booking['replacement_item_description']))) {
                $replacement_item_description = $this->booking['replacement_item_description'];
            } else {
                $replacement_item_description = NULL;
            }

            $replacement_item_quantity = $this->booking['replacement_item_quantity'];

            $replacement_item_price = NULL;
            $replacement_item_insurance = NULL;
            $replacement_item_type = 1;

            ShipperShipmentBookController::add_item($shipment_id, $replacement_item_product_type_id, $replacement_item_description, $replacement_item_quantity, $replacement_item_price, $replacement_item_insurance, $replacement_item_type);
        }
        else if ($service_type_id == 3) {
            $try_and_buy_cod_amount = intval($try_and_buy_charges);
            $item_product_type_id = $this->booking['item_product_type_id_1'];

            if (!empty(trim($this->booking['item_description_1']))) {
                $item_description = $this->booking['item_description_1'];
            } else {
                $item_description = NULL;
            }

            $item_quantity = $this->booking['item_quantity_1'];

            if (strtolower($this->booking['item_insurance_1']) == 'yes') {
                $item_insurance = TRUE;
            } else {
                $item_insurance = FALSE;
            }
            $item_price = str_replace(',', '', $this->booking['item_price_1']);

            $item_type = 2;
            $try_and_buy_cod_amount = $try_and_buy_cod_amount + intval($item_price);
            ShipperShipmentBookController::add_item($shipment_id, $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);

            if($this->booking['item_product_type_id_2'] != null){
                $item_product_type_id = $this->booking['item_product_type_id_2'];

                if (!empty(trim($this->booking['item_description_2']))) {
                    $item_description = $this->booking['item_description_2'];
                } else {
                    $item_description = NULL;
                }

                $item_quantity = $this->booking['item_quantity_2'];


                if (strtolower($this->booking['item_insurance_2']) == 'yes') {
                    $item_insurance = TRUE;
                } else {
                    $item_insurance = FALSE;
                }
                $item_price = str_replace(',', '', $this->booking['item_price_2']);

                $item_type = 2;

                $try_and_buy_cod_amount = $try_and_buy_cod_amount + intval($item_price);
                ShipperShipmentBookController::add_item($shipment_id, $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);
            }

            if($this->booking['item_product_type_id_3'] != null){
                $item_product_type_id = $this->booking['item_product_type_id_3'];

                if (!empty(trim($this->booking['item_description_3']))) {
                    $item_description = $this->booking['item_description_3'];
                } else {
                    $item_description = NULL;
                }

                $item_quantity = $this->booking['item_quantity_3'];

                if (strtolower($this->booking['item_insurance_3']) == 'yes') {
                    $item_insurance = TRUE;
                } else {
                    $item_insurance = FALSE;
                }
                $item_price = str_replace(',', '', $this->booking['item_price_3']);

                $item_type = 2;

                $try_and_buy_cod_amount = $try_and_buy_cod_amount + intval($item_price);
                ShipperShipmentBookController::add_item($shipment_id, $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);
            }

            if($this->booking['item_product_type_id_4'] != null){
                $item_product_type_id = $this->booking['item_product_type_id_4'];

                if (!empty(trim($this->booking['item_description_4']))) {
                    $item_description = $this->booking['item_description_4'];
                } else {
                    $item_description = NULL;
                }

                $item_quantity = $this->booking['item_quantity_4'];

                if (strtolower($this->booking['item_insurance_4']) == 'yes') {
                    $item_insurance = TRUE;
                } else {
                    $item_insurance = FALSE;
                }
                $item_price = str_replace(',', '', $this->booking['item_price_4']);

                $item_type = 2;

                $try_and_buy_cod_amount = $try_and_buy_cod_amount + intval($item_price);
                ShipperShipmentBookController::add_item($shipment_id, $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);
            }

            if($this->booking['item_product_type_id_5'] != null){
                $item_product_type_id = $this->booking['item_product_type_id_5'];

                if (!empty(trim($this->booking['item_description_5']))) {
                    $item_description = $this->booking['item_description_5'];
                } else {
                    $item_description = NULL;
                }

                $item_quantity = $this->booking['item_quantity_5'];

                if (strtolower($this->booking['item_insurance_5']) == 'yes') {
                    $item_insurance = TRUE;
                } else {
                    $item_insurance = FALSE;
                }
                $item_price = str_replace(',', '', $this->booking['item_price_5']);

                $item_type = 2;

                $try_and_buy_cod_amount = $try_and_buy_cod_amount + intval($item_price);

                ShipperShipmentBookController::add_item($shipment_id, $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);
            }
            $shipment_try_and_buy = Shipment::find($shipment_id);
            $shipment_try_and_buy->amount = $try_and_buy_cod_amount;
            $shipment_try_and_buy->save();
        }

        if(!empty($this->booking['nsas']) && $this->booking['nsa']) {
            $present = '';
            $address_array = preg_split("/[ ,]+/", $this->booking['consignee_address']);

            foreach ($this->booking['nsas'] as $nsa) {
                foreach ($address_array as $address_value) {
                    if (strtolower($nsa) == strtolower($address_value)) {
                        if ($present != '') {
                            $present .= ', ' . $address_value;
                        }
                        else {
                            $present = $address_value;
                        }
                    }
                }
            }

            if ($present != '') {
                NotificationsController::send(32, $shipment_id, $present);
            }
        }

        NotificationsController::send(2, $shipment_id);
        $settingsfortime = GlobalSettings::where('type', 'pickup_request_cut_off_time')->first();
        $now = Carbon::now()->format('H:i:s');
        $cutofftime = $settingsfortime->setting_value.":00:00";
        if($now>$cutofftime)
        {
            NotificationsController::send(152, $shipment_id);
            NotificationsController::send(153, $shipment_id);
        }
    }
}
