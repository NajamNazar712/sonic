<?php

namespace App\Jobs;

use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Retail\RetailRatesCalculationController;
use App\Http\Controllers\Retail\RetailShipmentBookController;
use App\Http\Models\Admin\Retail\RetailCashDeposit;
use App\Http\Models\Admin\Retail\RetailCashDepositShipment;
use App\Http\Models\Admin\Retail\RetailFranchise;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\Retail\RetailShipperInfo;
use App\Http\Models\Admin\Retail\RetailTraxCenter;
use App\Http\Models\City;
use App\Http\Models\Shipment;
use App\Http\Models\Shipper\UserShippingInfo;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;

class ProcessRetailShipmentBookingDB implements ShouldQueue
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
        $this->queue = 'retail_shipment_booking_db';
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
        $category = $this->booking['category'];
        $category_id = $this->booking['category_id'];
        $user_id = $this->booking['user_id'];
        $pickup_address_id = $this->booking['pickup_address_id'];
        $user_shipping_info = UserShippingInfo::find($pickup_address_id);
        $pickup_city_id = $user_shipping_info->city_id;
        $information_display = TRUE;
        $business_category_id = $this->booking['business_category_id'];

        $consignee_name = $this->booking['consignee_name'];
        $trax_box_id = $this->booking['trax_box_id'];
        $consignee_address = $this->booking['consignee_address'];
        $consignee_phone_number_1 = $this->booking['consignee_cell_number'];
        $consignee_phone_number_2 = NULL;
        $consignee_email_address = NULL;
        $order_id = $this->booking['order_id'];
        $package_type = FALSE;
        $special_instructions = $this->booking['special_instruction'];
        $city_id = City::where('name', $this->booking['destination'])->first()->id;

        $discount = 0;
        if($category == 1){
            $discount = RetailTraxCenter::find($category_id);
        }
        else{
            $discount = RetailFranchise::find($category_id);
        }

        $shipping_mode_check = $this->booking['shipping_mode_id'];
        if ($shipping_mode_check == 1) {
//            if($this->booking['business_category']== 1)
//            {
                $consignee_city_id = $city_id;
//            }
//            else{
//                $consignee_city_id = $this->booking['international_destination'];
//            }
            $shipping_mode_id = 2;
        }
        elseif ($shipping_mode_check == 4){
//            if($this->booking['business_category'] == 1) {
                $consignee_city_id = $city_id;
//            }
//            else{
//                $consignee_city_id = $this->booking['international_destination'];
//            }
            $shipping_mode_id = 3;
        }
        else{
//            if($this->booking['business_category'] == 1) {
                $consignee_city_id = $city_id;
//            }
//            else{
//                $consignee_city_id = $this->booking['international_destination'];
//            }
            $shipping_mode_id = 1;
        }
        $same_day_timing_id = NULL;

      /*  $this->booking['weight_charges'] = str_replace(',', '', $this->booking['weight_charges']);
        $this->booking['fuel_surcharge'] = str_replace(',', '', $this->booking['fuel_surcharge']);*/

        $city = City::find($pickup_city_id);
        /*$gst = $city->zone->gst;
        $total_charges_without_gst = $this->booking['weight_charges'] + $this->booking['fuel_surcharge'];
        $gst = $gst * $total_charges_without_gst;
        $total_charges = $total_charges_without_gst + $gst;*/


        if (strtolower($this->booking['volumetric_weight']) == 'yes') {
            $estimated_weight = (($this->booking['length'] * $this->booking['breadth'] * $this->booking['height']) / 5000);
            $length = $this->booking['length'];
            $breadth = $this->booking['breadth'];
            $height = $this->booking['height'];
        } else {
            $estimated_weight = $this->booking['weight'];
            $length = null;
            $breadth = null;
            $height = null;
        }


        $rates = RetailRatesCalculationController::rates($shipping_mode_check, $business_category_id, $pickup_city_id, $consignee_city_id, $trax_box_id, $discount, $estimated_weight);

        $charges_mode_id = $this->booking['charges_mode_id'];
//        if($shipping_mode_check == 3){
//            $amount = str_replace(',', '', $this->booking['cod']);
//            $r_amount = 0;
//            if($charges_mode_id == 2){
//                $amount = $amount + $total_charges;
//            }
//        }
//        else{
            $amount = 0;
            if($charges_mode_id == 2){
                $amount = $rates['charges_with_discount'];
            }
            $r_amount = 0;
//        }
        $payment_mode_id = 1;
        $try_and_buy_charges = NULL;

        $pieces_quantity = $this->booking['pieces'];
        $business_category_id = $this->booking['business_category_id'];


        $shipment_id = RetailShipmentBookController::book($user_id, 1, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $r_amount, $payment_mode_id, $charges_mode_id , $try_and_buy_charges, $pieces_quantity, $business_category_id, $length, $breadth, $height);

        $tracking_number = RetailShipmentBookController::generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);

        $product_type_id = $this->booking['product_id'];

        $item_description = NULL;

        $item_quantity = 1;

//        if (strtolower($this->booking['insurance_offered']) == 'yes') {
//            $price = str_replace(',', '', $this->booking['insurance_amount']);
//            $insurance = TRUE;
//        }
//        else {
            $price = NULL;
            $insurance = FALSE;
//        }

        $type = 0;

        RetailShipmentBookController::add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);

        if($pieces_quantity > 1){
            RetailShipmentBookController::create_shipment_pieces($shipment_id, $pieces_quantity);
        }

        if($this->booking['shipping_mode_id'] == 1){
//            if($this->booking['business_category'] == 1) {
                $destination = $this->booking['destination'];
//            }
//            else{
//                $destination = $this->booking['international_destination'];
//            }
        }
        else{
//            if($this->booking['business_category'] == 1) {
                $destination = $this->booking['destination'];
//            }
//            else{
//                $destination = $this->booking['international_destination'];
//            }
        }

        $shipper_info = RetailShipperInfo::where('shipper_phone_no', $this->booking['shipper_cell_number']);
        if($shipper_info->exists()){
            $shipper_info = $shipper_info->first();
            $shipper_info->shipper_phone_no = $this->booking['shipper_cell_number'];
            $shipper_info->shipper_name = $this->booking['shipper_name'];
            $shipper_info->shipper_cnic = $this->booking['shipper_cnic'];
            $shipper_info->shipper_address = $this->booking['shipper_address'];
            $shipper_info->city_id = $pickup_city_id;


            if ($this->booking['iban_number'] != null && $this->booking['account_number'] != null && $this->booking['bank_id'] != null) {
                $shipper_info->bank_id = $this->booking['bank_id'];
                $shipper_info->iban = $this->booking['iban_number'];
                $shipper_info->account_number = $this->booking['account_number'];
            }
            $shipper_info->save();
        }
        else{
            $shipper_info = new RetailShipperInfo();
            $shipper_info->shipper_phone_no = $this->booking['shipper_cell_number'];
            $shipper_info->shipper_name = $this->booking['shipper_name'];
            $shipper_info->shipper_cnic = $this->booking['shipper_cnic'];
            $shipper_info->shipper_address = $this->booking['shipper_address'];
            $shipper_info->city_id = $pickup_city_id;
            $shipper_info->save();
            if ($this->booking['iban_number'] != null && $this->booking['account_number'] != null && $this->booking['bank_id'] != null) {
                $shipper_info->bank_id = $this->booking['bank_id'];
                $shipper_info->iban = $this->booking['iban_number'];
                $shipper_info->account_number = $this->booking['account_number'];
            }
            $shipper_info->save();
        }
        $retail_shipment = new RetailShipment();
        $retail_shipment->shipment_id = $shipment_id;
        $retail_shipment->product_type_id = $this->booking['product_id'];
        $retail_shipment->shipping_mode = $this->booking['shipping_mode_id'];
        $retail_shipment->destination = $destination;
        $retail_shipment->payment_mode_id = $this->booking['payment_mode_id'];
        $retail_shipment->shipper_phone_no = $this->booking['shipper_cell_number'];
        $retail_shipment->shipper_name = $this->booking['shipper_name'];
        $retail_shipment->shipper_cnic = $this->booking['shipper_cnic'];
        $retail_shipment->shipper_address = $this->booking['shipper_address'];
        $retail_shipment->trax_box_id = $this->booking['trax_box_id'];
        $retail_shipment->total_charges = $amount;
        /*$retail_shipment->total_charges_without_gst = $total_charges_without_gst;
        $retail_shipment->gst = $gst;
        $retail_shipment->weight_charges = $this->booking['weight_charges'];
        $retail_shipment->cash_handling_charges = $this->booking[cash_handling_charges;
        $retail_shipment->fuel_surcharge = $this->booking['fuel_surcharge'];*/
        $retail_shipment->shipper_account_no = $shipper_info->id;
        $retail_shipment->weight = $estimated_weight;
        $retail_shipment->length = $length;
        $retail_shipment->breadth = $breadth;
        $retail_shipment->height = $height;
        $retail_shipment->retail_user_id = $this->booking['retail_user_id'];
        $retail_shipment->save();

        $shipment = Shipment::find($shipment_id);
        if($shipment->charges_mode_id != 2) {
            $date = Carbon::today()->toDateString();
            $cash_deposit = RetailCashDeposit::whereDate('created_at', $date)->where('category', $this->booking['category'])->where('retail_user_id', $this->booking['retail_user_id'])->where('finalize', 0);
            if ($cash_deposit->exists()) {
                $cash_deposit = $cash_deposit->first();
                $total_shipments = $cash_deposit->total_cn + 1;
                $total_cash = $cash_deposit->total_cash + $amount;
                $cash_deposit->total_cn = $total_shipments;
                $cash_deposit->total_cash = $total_cash;
                $cash_deposit->save();
            } else {
                $cash_deposit = new RetailCashDeposit();
                $cash_deposit->category = $this->booking['category'];
                $cash_deposit->retail_user_id = $this->booking['retail_user_id'];
                $cash_deposit->total_cn = 1;
                $cash_deposit->total_cash = $amount;
                $cash_deposit->save();
            }

            $cash_deposit_shipment = new RetailCashDepositShipment();
            $cash_deposit_shipment->cash_deposit_id = $cash_deposit->id;
            $cash_deposit_shipment->shipment_id = $shipment_id;
            $cash_deposit_shipment->shipping_mode_id = $this->booking['shipping_mode_id'];
            $cash_deposit_shipment->save();
        }


        AdminPickupsController::generate($shipment_id);
        NotificationsController::send(115, $tracking_number, $shipper_info->id);
    }
}
