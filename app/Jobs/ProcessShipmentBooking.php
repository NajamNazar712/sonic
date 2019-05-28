<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use ShiftOneLabs\LaravelSqsFifoQueue\Bus\SqsFifoQueueable;

use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use App\Http\Controllers\NotificationsController;

use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\City;

class ProcessShipmentBooking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SqsFifoQueueable, SerializesModels;

    protected $booking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $booking)
    {
        $this->connection = 'sqs-fifo';
        $this->messageGroupId = 'shipment_booking';
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
        $consignee_phone_number_1 = substr_replace($this->booking['consignee_phone_number_1'], '-', 4, 0);

        if (!empty(trim($this->booking['consignee_phone_number_2']))) {
            $consignee_phone_number_2 = substr_replace($this->booking['consignee_phone_number_2'], '-', 4, 0);
        } else {
            $consignee_phone_number_2 = NULL;
        }

        if (!empty(trim($this->booking['consignee_email_address']))) {
            $consignee_email_address = $this->booking['consignee_email_address'];
        } else {
            $consignee_email_address = NULL;
        }

        if (!empty(trim($this->booking['order_id']))) {
            $order_id = $this->booking['order_id'];
        } else {
            $order_id = NULL;
        }

        $pickup_date = $this->booking['pickup_date'];

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

        $amount = $this->booking['amount'];

        $payment_mode_id = $this->booking['payment_mode_id'];

        $charges_mode_id = $this->booking['charges_mode_id'];

        $package_type = TRUE;

        if ($this->booking['account_type_id'] == 1) {
            $shipment_id = ShipperShipmentBookController::book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id, $charges_mode_id);
        }
        else {
            $delivery_type_id = $this->booking['delivery_type_id'];

            if ($delivery_type_id == 2) {
                $consignee_address = 'TRAX Office ' . $this->booking['consignee_city_name'];
            }

            $shipment_id = ShipperShipmentBookController::corporate_book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $estimated_weight, $shipping_mode_id, $delivery_type_id, $same_day_timing_id, $charges_mode_id, $amount, $payment_mode_id);
        }

        $tracking_number = ShipperShipmentBookController::generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);

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
