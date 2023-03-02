<?php

namespace App\Console\Commands;

use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\Fuel\Rider\RiderFuelAllocation;
use App\Http\Models\Admin\Fuel\Rider\RiderFuelAllocationDeliveryNote;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RiderFuelAllocationDeliveryNoteCalculation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rider:fuel_allocation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rider Fuel Allocation Total Delivery Note Calculation';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = Carbon::yesterday()->format('Y-m-d');
        $rider_wise_details = array();

        $delivery_notes = DeliveryNote::whereDate('cash_collected_at', $date);
        if($delivery_notes->exists()){
            $delivery_notes = $delivery_notes->get();
            foreach ($delivery_notes as $delivery_note){
                $delivery_notes_details = ['delivery_note_id' => $delivery_note->id, 'dncc_amount' => $delivery_note->received_cod_amount];
                if(array_key_exists($delivery_note->rider_id, $rider_wise_details)){
                    if(array_key_exists($delivery_note->hub_id, $rider_wise_details[$delivery_note->rider_id])){
                        $rider_wise_details[$delivery_note->rider_id][$delivery_note->hub_id]['delivery_notes_count'] = $rider_wise_details[$delivery_note->rider_id][$delivery_note->hub_id]['delivery_notes_count'] + 1;
                        $rider_wise_details[$delivery_note->rider_id][$delivery_note->hub_id]['delivery_notes_dncc_amount'] = $rider_wise_details[$delivery_note->rider_id][$delivery_note->hub_id]['delivery_notes_dncc_amount'] + $delivery_note->received_cod_amount;
                        $rider_wise_details[$delivery_note->rider_id][$delivery_note->hub_id]['delivery_notes'][] = $delivery_notes_details;
                    }
                    else{
                        $rider_wise_details[$delivery_note->rider_id][$delivery_note->hub_id]['rider_id'] = $delivery_note->rider_id;
                        $rider_wise_details[$delivery_note->rider_id][$delivery_note->hub_id]['hub_id'] = $delivery_note->hub_id;
                        $rider_wise_details[$delivery_note->rider_id][$delivery_note->hub_id]['delivery_notes_count'] = 1;
                        $rider_wise_details[$delivery_note->rider_id][$delivery_note->hub_id]['delivery_notes_dncc_amount'] = $delivery_note->received_cod_amount;
                        $rider_wise_details[$delivery_note->rider_id][$delivery_note->hub_id]['delivery_notes'][] = $delivery_notes_details;
                    }
                }
                else{
                    $rider_wise_details[$delivery_note->rider_id][$delivery_note->hub_id]['rider_id'] = $delivery_note->rider_id;
                    $rider_wise_details[$delivery_note->rider_id][$delivery_note->hub_id]['hub_id'] = $delivery_note->hub_id;
                    $rider_wise_details[$delivery_note->rider_id][$delivery_note->hub_id]['delivery_notes_count'] = 1;
                    $rider_wise_details[$delivery_note->rider_id][$delivery_note->hub_id]['delivery_notes_dncc_amount'] = $delivery_note->received_cod_amount;
                    $rider_wise_details[$delivery_note->rider_id][$delivery_note->hub_id]['delivery_notes'][] = $delivery_notes_details;
                }
            }




            $date = Carbon::yesterday()->format('Y-m-d');
            if(count($rider_wise_details) > 0){
                foreach ($rider_wise_details as $rider_wise_detail){
                    foreach ($rider_wise_detail as $hub_wise_detail){
                        $rider_fuel_allocation = new RiderFuelAllocation();
                        $rider_fuel_allocation->rider_id = $hub_wise_detail['rider_id'];
                        $rider_fuel_allocation->hub_id = $hub_wise_detail['hub_id'];
                        $rider_fuel_allocation->date = $date;
                        $rider_fuel_allocation->delivery_notes = $hub_wise_detail['delivery_notes_count'];
                        $rider_fuel_allocation->dncc_amount = $hub_wise_detail['delivery_notes_dncc_amount'];
                        $rider_fuel_allocation->save();

                        foreach ($hub_wise_detail['delivery_notes'] as $rider_delivery_note){
                            $rider_fuel_allocation_delivery_note = new RiderFuelAllocationDeliveryNote();
                            $rider_fuel_allocation_delivery_note->rider_fuel_allocation_id = $rider_fuel_allocation->id;
                            $rider_fuel_allocation_delivery_note->delivery_note_id = $rider_delivery_note['delivery_note_id'];
                            $rider_fuel_allocation_delivery_note->dncc_amount = $rider_delivery_note['dncc_amount'];
                            $rider_fuel_allocation_delivery_note->save();
                        }
                    }
                }
            }
        }
    }
}
