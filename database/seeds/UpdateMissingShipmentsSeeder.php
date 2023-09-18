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
        $tracking_numbers = [22310928454903, 22310128454907, 22328428454913, 22313428454915, 22320228454924, 22346728454966, 22313828454968, 22334728454977, 20220228455105, 29324228455258, 27110128455366, 31515828455562, 31529328455600, 17433728455601, 14414428455629, 17438328455630, 25128328455632, 14414428455633, 14414428455634, 14414428455635, 17410128455657, 28828828455848, 29314428455898, 29314428455902, 15815828455929, 22322328456329, 22331128456342, 27122328456454, 27128828456469, 27131128456482, 28828828456493, 22331928456746, 14415928456879, 22322328456924, 22322328456990, 22312828457001,101200328454953, 31531528454954, 17430428454971, 22322328455011, 22310328455017, 22322328455036, 38322328455072, 20220228455077, 22322328455102, 22322328455103, 17417428455104, 25131128455170, 174200328455183, 15922328455184, 22322328455245, 22322328455292, 22322328455346, 22322328455353, 14414428455359, 22322328455455, 22322328455550, 22322328455557, 22322328455594, 15815828455659, 22322328455661, 22322328455672, 20231828455827, 26726728455828, 14422328455833, 17427128455835, 22318528455860, 17417428455872, 10135528455873, 15915928455891, 22322328455892, 17417428455894, 17417428455897, 22322328455926, 22322328455935, 22322328455940, 22322328455973, 10713228455993, 22322328456056, 25131528456201, 22328428456208, 29320228456215, 14414428456217, 14438328456330, 18638328456350, 17430428456398, 22333228456452, 17417428456479, 22322328456489, 22322328456494, 22322328456522, 22322328456528, 25122328456532, 20222328456754, 38333228456782, 27112528456793, 17412528456799, 10145328456801, 11022328456805, 30230228456822, 22322328456828, 22322328456830, 22322328456836, 22322328456838, 22322328456844, 18622328456851, 22322328456908, 25122928456920, 17220228456954, 20220228456960, 20220228456975, 31522328456982, 18622328456984, 29314928456995, 22322328456998, 22322328457003, 22322328457009, 22322328457012, 30230228457014, 14418828457032, 22322328457033, 25138328457040, 25138328457041, 22322328457047, 15931528457050];

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
