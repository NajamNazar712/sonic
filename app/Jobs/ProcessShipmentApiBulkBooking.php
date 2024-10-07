<?php

namespace App\Jobs;

use App\Http\Controllers\Admins\FTLController;
use App\Http\Controllers\APIController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use App\Http\Models\Admin\BookingDestinationMappingKeyword;
use App\Http\Models\Admin\FtlRequest;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\NonServiceArea;
use App\Http\Models\Blacklist\ConsigneeInformation;
use App\Http\Models\City;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentOrderDate;
use App\Http\Models\ShipmentReplacementParcelImage;
use App\Http\Models\ShipmentShipperReference;
use App\Http\Models\Shipper\SubstituteUser;
use App\Http\Models\SubstituteUserShipment;
use App\ShipmentBookedApiCount;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ProcessShipmentApiBulkBooking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $booking;
    protected $user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $booking, $user_id)
    {
        $this->queue = 'shipment_booking_api_bulk';
        $this->booking = $booking;
        $this->user_id = $user_id;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user_id = $this->user_id;
        foreach ($this->booking as $row) {
            if (!empty($row['substitute_user_email'])) {
                if ($row['substitute_user_email'] != null) {
                    $sub_user = SubstituteUser::where(['user_id' => $user_id, 'email' => $row['substitute_user_email'], 'status' => 1]);
                    if ($sub_user->exists()) {
                        $sub_user = $sub_user->first();
                        $sub_user_id = $sub_user->id;
                        $substitute_user_shipment = new SubstituteUserShipment();
                        $substitute_user_shipment->substitute_user_id = $sub_user_id;
                        $substitute_user_shipment->shipment_id = $row["shipment_id"];
                        $substitute_user_shipment->save();

                        $shipment = Shipment::find($row["shipment_id"]);
                        $shipment->booked_by = 2;
                        $shipment->save();
                    }
                }
            }


            if (!empty($row['order_date'])) {
                if ($row['order_date'] != null) {
                    $order_date = new ShipmentOrderDate();
                    $order_date->shipment_id = $row["shipment_id"];
                    $order_date->order_date = $row['order_date'];
                    $order_date->save();
                }
            }

            if (!empty($row['shipper_reference_number_1']) || !empty($row['shipper_reference_number_2']) || !empty($row['shipper_reference_number_3']) || !empty($row['shipper_reference_number_4']) || !empty($row['shipper_reference_number_5'])) {
                $shipper_reference = new ShipmentShipperReference();
                $shipper_reference->shipment_id = $row["shipment_id"];
                if (!empty($row['shipper_reference_number_1'])) {
                    $shipper_reference->reference_1 = $row['shipper_reference_number_1'];
                }
                if (!empty($row['shipper_reference_number_2'])) {
                    $shipper_reference->reference_2 = $row['shipper_reference_number_2'];
                }
                if (!empty($row['shipper_reference_number_3'])) {
                    $shipper_reference->reference_3 = $row['shipper_reference_number_3'];
                }
                if (!empty($row['shipper_reference_number_4'])) {
                    $shipper_reference->reference_4 = $row['shipper_reference_number_4'];
                }
                if (!empty($row['shipper_reference_number_5'])) {
                    $shipper_reference->reference_5 = $row['shipper_reference_number_5'];
                }
                $shipper_reference->save();
            }

            if ($row['service_type_id'] == 1 || $row['service_type_id'] == 5) {
                $item_product_type_id = $row['item_product_type_id'];

                if (!empty($row['item_description'])) {
                    $item_description = $row['item_description'];
                } else {
                    $item_description = null;
                }

                $item_quantity = $row['item_quantity'];


                if ($row['item_insurance'] == 1) {
                    $item_price = str_replace(',', '', $row['product_value']);
                    $item_insurance = true;
                } else {
                    $item_price = null;
                    $item_insurance = false;
                }

                $item_type = 0;

                ShipperShipmentBookController::add_item($row["shipment_id"], $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);

                if ($row['pieces_quantity'] > 1) {
                    ShipperShipmentBookController::create_shipment_pieces($row["shipment_id"], $row["pieces_quantity"]);
                }
            } else if ($row['service_type_id'] == 2) {
                $item_product_type_id = $row['item_product_type_id'];

                if (!empty($row['item_description'])) {
                    $item_description = $row['item_description'];
                } else {
                    $item_description = null;
                }

                $item_quantity = $row['item_quantity'];

                if ($row['item_insurance'] == 1) {
                    $item_price = str_replace(',', '', $row['product_value']);
                    $item_insurance = true;
                } else {
                    $item_price = null;
                    $item_insurance = false;
                }

                $item_type = 0;

                ShipperShipmentBookController::add_item($row["shipment_id"], $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);

                $replacement_item_product_type_id = $row['replacement_item_product_type_id'];

                if (!empty($row['replacement_item_description'])) {
                    $replacement_item_description = $row['replacement_item_description'];
                } else {
                    $replacement_item_description = null;
                }

                $replacement_item_quantity = $row['replacement_item_quantity'];

                $replacement_item_price = null;
                $replacement_item_insurance = null;
                $replacement_item_type = 1;

                ShipperShipmentBookController::add_item($row["shipment_id"], $replacement_item_product_type_id, $replacement_item_description, $replacement_item_quantity, $replacement_item_price, $replacement_item_insurance, $replacement_item_type);

                if (!empty($row['replacement_item_image'])) {
                    $shipment_parcel_image = ShipmentReplacementParcelImage::where('shipment_id', $row["shipment_id"]);
                    if ($shipment_parcel_image->exists()) {
                        $shipment_parcel_image = $shipment_parcel_image->first();
                        Storage::disk('public')->delete($shipment_parcel_image->picture_path);
                    } else {
                        $shipment_parcel_image = new ShipmentReplacementParcelImage();
                        $shipment_parcel_image->shipment_id = $row["shipment_id"];
                    }
                    $time = Carbon::now()->toDateString();
                    $picture_path = 'replacement_parcel/' . $row["shipment_id"] . '_' . $time . '.png';
                    Storage::disk('public')->put($picture_path, file_get_contents($row['replacement_item_image']));
                    $shipment_parcel_image->picture_path = $picture_path;
                    $shipment_parcel_image->save();
                }
            } else if ($row["service_type_id"] == 3) {
                $try_and_buy_cod_amount = intval($row['try_and_buy_fees']);
                foreach ($row['items'] as $item) {
                    $item_product_type_id = $item['item_product_type_id'];

                    if (isset($item['item_description']) && !empty($item['item_description'])) {
                        $item_description = $item['item_description'];
                    } else {
                        $item_description = null;
                    }

                    $item_quantity = $item['item_quantity'];
                    $item_price = $item['product_value'];

                    if (isset($row['item_insurance']) && !empty($row['item_insurance'])) {
                        $item_insurance = true;
                    } else {
                        $item_insurance = false;
                    }

                    $item_type = 2;

                    $try_and_buy_cod_amount = $try_and_buy_cod_amount + intval($item_price);
                    ShipperShipmentBookController::add_item($row["shipment_id"], $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);
                }
                $shipment_try_and_buy = Shipment::find($row["shipment_id"]);
                $shipment_try_and_buy->amount = $try_and_buy_cod_amount;
                $shipment_try_and_buy->save();
            }

            ShipperShipmentBookController::addressAreaConsigneeShipper($row["shipment_id"], $row["pickup_address_id"], $row["consignee_city_id"], $row["consignee_address"]);

            if ($row["service_type_id"] == 6) {
                $ftl_request_id = $row['approve_freight_request'];
                FtlRequest::where('id', $ftl_request_id)->update(['shipment_id' => $row["shipment_id"], 'status_id' => 5, 'collection_type' => $row['ftl_collection_type']]);
                FTLController::FTLRequestStatusHistory($ftl_request_id, 5, $user_id);
            }

            // Notify about the shipment booking
            NotificationsController::send(2, $row["shipment_id"]);
            $now = Carbon::now()->format('H:i:s');
            $pickup_city = City::where('id', $row['consignee_city_id'])->whereNotNull('pickup_cut_off_time');

            if ($pickup_city->exists()) {
                $pickup_city = $pickup_city->first();
                $cutofftime = $pickup_city->pickup_cut_off_time . ":00:00";
            } else {
                $settingsfortime = GlobalSettings::where('type', 'pickup_request_cut_off_time')->first();
                $cutofftime = $settingsfortime->setting_value . ":00:00";
            }

            if ($now > $cutofftime) {
                NotificationsController::send(152, $row["shipment_id"]);
                NotificationsController::send(153, $row["shipment_id"]);
            }

        }
    }
}
