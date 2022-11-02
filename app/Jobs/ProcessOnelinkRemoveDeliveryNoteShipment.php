<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessOnelinkRemoveDeliveryNoteShipment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $delivery_note_id;
    protected $shipment_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $delivery_note_id, int $shipment_id)
    {
        $this->queue = 'one_link_delivery_note_shipment_remove';
        $this->delivery_note_id = $delivery_note_id;
        $this->shipment_id = $shipment_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $delivery_note_id = $this->delivery_note_id;
        $shipment_id = $this->shipment_id;
        if($delivery_note_id != null && $shipment_id != null){

            try{
                $client = new Client(['base_uri' => 'https://link1link.trax.pk/api/sonic/', 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
                $response = $client->put('out_for_delivery_shipments', [
                    'form_params' => [
                        'delivery_note_id' => $delivery_note_id,
                        'shipment_id' => $shipment_id
                    ]
                ]);
            }
            catch(RequestException $e){

            }
        }
    }
}
