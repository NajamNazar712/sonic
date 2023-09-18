<?php

use App\Http\Models\Shipper\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateMissingShipmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tracking_numbers = [14431528455345, 14454728455526, 22318328454886, 22314428454890, 22317428454892, 22327128454905, 22328828454912, 22331528454914, 22323828454987, 22320228455008, 22319828455070, 22313328455078, 22315828455123, 22313528455180, 22333128455329, 22320228455378, 22322628455549, 22344328455568, 22311128455573, 22330228455620, 22316128455642, 22328828455647, 22328828455654, 22320228455656, 22323728455854, 22347028455895, 22316128455932, 22316128455933, 22330228455944, 22330228455961, 22310728455989, 22318628456048, 22325128456207, 22320228456375, 22326628456545, 22319528456857, 22325128456883, 22320228456866, 22314428456891, 22310728456893, 22317428456898, 22322328456904, 22314428456909, 22310728456911, 22317428456914, 22322328456918, 22353228454904, 22320228455251];

        foreach ($tracking_numbers as $tracking_number){
            $shipment = DB::connection('gcp')->table('shipments')->where('tracking_number', $tracking_number)->first();

            if($shipment){

                $booking_url = 'https://sonic.pk/api/shipment/book';

                $shipment_items = DB::connection('gcp')->table('shipment_items')->where('shipment_id', $shipment->id)->get();
                $product_type_id = 24;
                $item_description = '';
                $item_quantity = 1;
                foreach ($shipment_items as $item){
                    if($product_type_id == 24){
                        $product_type_id = $item->product_type_id;
                    }
                    $item_description .= $item->description;
                    $item_quantity = $item->quantity;
                }

                $open_shipment = false;
                $shipment_details = DB::connection('gcp')->table('shipment_details')->where('shipment_id', $shipment->id)->first();
                if($shipment_details){
                    $open_shipment = $shipment_details->is_open;
                }


                $token = User::find($shipment->user_id)->api_token;

                $client = new Client(['http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
                $response_booking = $client->post($booking_url, [
                    'headers' => [
                        'Authorization' => $token
                    ],
                    'form_params' => [
                        'service_type_id' => $shipment->booking_type_id,
                        'pickup_address_id' => $shipment->pickup_address_id,
                        'information_display' => $shipment->information_display,
                        'consignee_city_id' => $shipment->consignee_city_id,
                        'consignee_name' => $shipment->consignee_name,
                        'consignee_address' => $shipment->consignee_address,
                        'consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                        'consignee_email_address' => $shipment->consignee_email,
                        'order_id' => $shipment->order_id,
                        'item_product_type_id' => $product_type_id,
                        'item_description' => $item_description,
                        'item_quantity' => $item_quantity,
                        'item_insurance' => 0,
                        'pickup_date' => Carbon::now('Asia/Karachi')->toDateString(),
                        'estimated_weight' => $shipment->estimated_weight,
                        'shipping_mode_id' => $shipment->shipping_mode_id,
                        'amount' => $shipment->amount,
                        'payment_mode_id' => $shipment->payment_mode_id,
                        'special_instructions' => $shipment->special_instructions,
                        'pieces_quantity' => $shipment->pieces,
                        'delivery_type_id' => $shipment->walk_in_delivery_type_id,
                        'open_shipment' => $open_shipment,
                        'parcel_value' => $shipment->amount
                    ]
                ]);

                $status_code = $response_booking->getStatusCode();
                if ($status_code == 200) {
                    $data_booking = $response_booking->getBody();

                    $payload_booking = json_decode($data_booking);
                    Log::channel('trax_pay_test')->info(json_encode($payload_booking, true));
                    if ($payload_booking->status == 0) {
                        $new_tracking_number = $payload_booking->tracking_number;
                        Log::channel('trax_pay_test')->info('new tracking: '. $new_tracking_number);
                        $sonic_shipment = \App\Http\Models\Shipment::where('tracking_number', $new_tracking_number)->first();

                        if($sonic_shipment){
                            $sonic_shipment->tracking_number = $tracking_number;
                            $sonic_shipment->save();
                        }

                    }
                }

            }



        }

    }
}
