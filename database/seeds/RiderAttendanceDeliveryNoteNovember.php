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
        $from = Carbon::createFromFormat('Y-m-d H:i:s', '2022-10-21 00:00:01');
        $to = Carbon::createFromFormat('Y-m-d H:i:s', '2022-11-22 23:23:59');

        $length = $from->diffInDays($to);

        $dates = [];

        for ($i = 0; $i < $length; $i++){
            $date = $from->addDays($i);
            array_push($dates, $date);
}

        dd($dates);
    }
}
