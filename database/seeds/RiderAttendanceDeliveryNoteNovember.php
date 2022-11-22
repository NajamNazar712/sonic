<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RiderAttendanceDeliveryNoteNovember extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $from = Carbon::createFromFormat('Y-m-d', '2022-10-21');
        $to = Carbon::createFromFormat('Y-m-d', '2022-11-20');

        $length = $from->diffInDays($to);

        $dates = [];
        $all_dates = array();
        while ($from->lte($to)){
            $all_dates[] = $from->toDateString();

            $from->addDay();
        }
        $details = [];
        foreach($all_dates as $date){
            $riders = [];
            $delivery_notes = \App\Http\Models\Admin\DeliveryNote::whereDate('created_at', $date);
            if($delivery_notes->exists()){
                $delivery_notes = $delivery_notes->get();
                foreach ($delivery_notes as $delivery_note){
                    if(!in_array($riders, $delivery_note->rider_id)){
                        $details[$delivery_note->rider_id][] = $delivery_note->created_at;
                    }
                }
            }
        }
        dd($details);
    }
}
