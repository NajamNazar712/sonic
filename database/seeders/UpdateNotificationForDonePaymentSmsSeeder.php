<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForDonePaymentSmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp =  \Carbon\Carbon::now()->format('Y-m-d H:i:s');

//        $id = 0;
//        $last_row = DB::table('notifications')->orderby('id','desc')->first();
//        $id = isset($last_row->id) ? $last_row->id : 0;
//        $id = (int) $id +1;

        DB::table('notifications')->insert(array(
            array('id' => 172, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Done Payment SMS', 'type_id' => 2, 'subject' => '', 'body' => 'Dear  [shipper_name],' . PHP_EOL . 'Your shipment has been paid by TRAX at [updated_at] of amount [total_amount].'. PHP_EOL .'Thank you for being our valued customer.', 'updated_by' => 7, 'status' => 1),
        ));
    }
}
