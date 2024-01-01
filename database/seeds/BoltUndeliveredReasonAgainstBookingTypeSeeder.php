<?php

use Illuminate\Database\Seeder;

class BoltUndeliveredReasonAgainstBookingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('bolt_undelivered_reason_against_booking_types')->truncate();

        $reason_ids = [
            1,3,4,5,6,7,8,12,14,17,18,19,23,25,27,28,31,32,33,34,35,40,45,60,63,77
        ];

        $booking_types = 
        [
            1,2,3,5
        ];
        foreach ($booking_types as $booking_type) {
            foreach ($reason_ids as $reason_id) {
                if ($booking_type == 1 || $booking_type == 3 || $booking_type == 5) {
                    if (!in_array($reason_id, [32, 33, 31, 77])) {
                        DB::table('bolt_undelivered_reason_against_booking_types')->insert([
                            'booking_type_id' => $booking_type,
                            'reason_id' => $reason_id,
                        ]);
                    } 
                }else {
                    DB::table('bolt_undelivered_reason_against_booking_types')->insert([
                        'booking_type_id' => $booking_type,
                        'reason_id' => $reason_id,
                    ]);
                }
            }
        }
    }
}
