<?php

namespace App\Jobs;

use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use App\Http\Models\City;
use App\Http\Models\Shipment;
use App\http\Models\ShipmentOrderDate;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessShipmentBookingDistributionDB implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $booking;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($booking)
    {
        $this->queue = 'shipment_booking_distribution_db';
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
        $service_type_id = 1;
        if(array_key_exists("open_shipment",$this->booking)){
            if (strtolower($this->booking['open_shipment']) == 'yes') {
                $open_shipment = 1;
            } else {
                $open_shipment = 0;
            }
        }else{
            $open_shipment = 0;
        }



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

        $charges_mode_id = $this->booking['charges_mode_id'];
        if (!empty(trim($this->booking['order_id']))) {
            $order_id = $this->booking['order_id'];
        } else {
            $order_id = NULL;
        }

        if (isset($this->booking['special_instructions']) && !empty(trim($this->booking['special_instructions']))) {
            $special_instructions = $this->booking['special_instructions'];
        } else {
            $special_instructions = NULL;
        }

        $estimated_weight = $this->booking['estimated_weight'];
        $shipping_mode_id = $this->booking['shipping_mode_id'];

        if ($shipping_mode_id == 4) {
            $same_day_timing_id = $this->booking['same_day_timing_id'] ?? Null;
        } else {
            $same_day_timing_id = NULL;
        }

        $package_type = TRUE;

        $amount = 0;
        $pieces_quantity = 1;

        $return_address_id = NULL;
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


        if($business_category_id == 2){
            $delivery_type_id = 1;
        }
        else{
            $delivery_type_id = $this->booking['delivery_type_id'];
        }


        if ($delivery_type_id == 2) {
            $consignee_address = 'TRAX Office ' . $this->booking['consignee_city_name'];
        }

        $shipment_id = ShipperShipmentBookController::corporate_book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $delivery_type_id, $same_day_timing_id, $charges_mode_id, $amount, $payment_mode_id, $pieces_quantity, $self_collection, $business_category_id, NULL, $open_shipment, $return_address_id);

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

        for ($i = 1; $i <= 10; $i++) {
            if($this->booking['distribution_product_type_id_'.$i] != null)
            {
                $product_type_id = $this->booking['distribution_product_type_id_' . $i];

                $item_quantity = $this->booking['distribution_item_per_sku_'.$i];
                $units_per_item = $this->booking['distribution_unit_per_item_'.$i];
                $price = str_replace(',', '', $this->booking['distribution_item_price_'.$i]);

                if (strtolower($this->booking['distribution_item_insurance_'.$i]) == 'yes') {
                    $insurance = TRUE;
                } else {
                    $insurance = FALSE;
                }

                ShipperShipmentBookController::add_distribution_item($shipment_id, $product_type_id, $item_quantity, $units_per_item, $price, $insurance);

                $amount += $price;
            }
        }

        $distribution_shipment = Shipment::find($shipment_id);
        $distribution_shipment->amount = $amount;
        $distribution_shipment->save();

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
    }
}
