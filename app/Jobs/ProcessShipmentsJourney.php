<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use ShiftOneLabs\LaravelSqsFifoQueue\Bus\SqsFifoQueueable;

use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipment;
use App\Http\Models\CargoConsignment;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\ReturnNote;


class ProcessShipmentsJourney implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SqsFifoQueueable, SerializesModels;

    protected $entry;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $entry)
    {
        $this->connection = 'sqs-fifo';
        $this->messageGroupId = 'shipments_journey';
        $this->entry = $entry;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $shipment_journey = new ShipmentsJourney();

        $shipment_journey->shipment_id = $this->entry['shipment_id'];
        $shipment_journey->verification = $this->entry['verification'];
        $shipment_journey->shipper_status_id = $this->entry['shipper_status_id'];
        $shipment_journey->consignee_status_id = $this->entry['consignee_status_id'];
        $shipment_journey->status_reason_id = $this->entry['status_reason_id'];
        $shipment_journey->remarks = $this->entry['remarks'];
        $shipment_journey->user_id = $this->entry['user_id'];
        $shipment_journey->admin_id = $this->entry['admin_id'];
        $shipment_journey->reference_1_id = $this->entry['reference_1_id'];
        $shipment_journey->reference_2_id = $this->entry['reference_2_id'];
        $shipment_journey->received_or_refused_by = $this->entry['received_or_refused_by'];
        $shipment_journey->ip_address = $this->entry['ip_address'];

        if (in_array($this->entry['shipper_status_id'], [1, 2, 17, 19, 39, 40, 41, 42, 43, 47, 50])) {
            $shipment = Shipment::find($this->entry['shipment_id']);

            if ($shipment) {
                $shipment_journey->city_id = $shipment->pickup_address->city_id;
            }
        }
        else if (in_array($this->entry['shipper_status_id'], [3, 21, 26, 32])) {
            $cargo_consignment = CargoConsignment::find($this->entry['reference_1_id']);

            if ($cargo_consignment) {
                $shipment_journey->city_id = $cargo_consignment->origin_hub_id;
            }
        }
        else if (in_array($this->entry['shipper_status_id'], [4, 22, 27, 33])) {
            $cargo_consignment = CargoConsignment::find($this->entry['reference_1_id']);

            if ($cargo_consignment) {
                $shipment_journey->city_id = $cargo_consignment->destination_hub_id;
            }
        }
        else if (in_array($this->entry['shipper_status_id'], [5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 18])) {
            if ($this->entry['reference_1_id']) {
                $delivery_note = DeliveryNote::find($this->entry['reference_1_id']);

                if ($delivery_note) {
                    $shipment_journey->city_id = $delivery_note->hub_id;
                }
                else if ($this->entry['shipper_status_id'] == 13) {
                    $shipment = Shipment::find($this->entry['shipment_id']);

                    if ($shipment) {
                        $shipment_journey->city_id = $shipment->consignee_city_id;
                    }
                }
            }
        }
        else if (in_array($this->entry['shipper_status_id'], [23, 24, 25, 28, 29, 30, 31, 34, 35, 36, 37, 38, 44, 45, 46, 47, 48])) {
            $return_note = ReturnNote::find($this->entry['reference_1_id']);

            if ($return_note) {
                $shipment_journey->city_id = $return_note->hub_id;
            }
        }
        else if ($this->entry['shipper_status_id'] == 20) {
            $shipment = Shipment::find($this->entry['shipment_id']);

            if ($shipment) {
                $shipment_journey->city_id = $shipment->consignee_city_id;
            }
        }

        $shipment_journey->save();
    }
}
