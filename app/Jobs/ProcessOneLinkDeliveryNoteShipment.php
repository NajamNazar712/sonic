<?php

namespace App\Jobs;

use App\Http\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ProcessOneLinkDeliveryNoteShipment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $details;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $details)
    {
        $this->queue = 'one_link_delivery_note_shipments';
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $one_link_details = $this->details;
        $shipment_ids = $one_link_details['shipment_ids'];
        $delivery_note_id = $one_link_details['delivery_note_id'];

        if($delivery_note_id != null && is_array($shipment_ids)){
            foreach ($shipment_ids as $index => $shipment_id){
                $shipment_details = array();
                $shipment = Shipment::find($shipment_id);
                if($shipment){
                    $shipment_details[$index]['tracking_number'] = $shipment->tracking_number;
                    $shipment_details[$index]['id'] = $shipment->id;
                    $shipment_details[$index]['amount'] = $shipment->amount;
                    $shipment_details[$index]['consignee_name'] = $shipment->consignee_name;
                    $shipment_details[$index]['consignee_phone_number'] = $shipment->consignee_phone_number_1;
                }
            }

            try{
                $client = new Client(['base_uri' => 'https://link1link.trax.pk/api/sonic/', 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
                $response = $client->post('out_for_delivery_shipments', [
                    'form_params' => [
                        'delivery_note_id' => $delivery_note_id,
                        'shipments' => $shipment_details,
                    ]
                ]);

                var_dump($response->getBody());
            }
            catch(RequestException $e){

            }
        }
    }
}
