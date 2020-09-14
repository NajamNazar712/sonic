<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForWalkinShipment extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert(array(
            array('id' => 85, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Walk-in Shipment booked', 'type_id' => 1, 'subject' => 'Walk-in Shipment booked[tracking_number]', 'body' => 'Dear Concerns,'. PHP_EOL .'Please find the following Walk-in shipment is booked as follows:'. PHP_EOL .'[tracking_number]'. PHP_EOL .' [origin] '. PHP_EOL .' [destination] '. PHP_EOL .' [total_charges]', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
