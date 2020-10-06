<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForNonActivityShipmentBookings extends Seeder
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
            array('id' => 95, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Non-Activity Shipment Bookings', 'type_id' => 1, 'subject' => 'Accounts Closure Due to non-activity/ shipment bookings' , 'body' => 'Dear Sir/Madam,'. PHP_EOL .PHP_EOL.'This is with reference to your account. From the last 15 days we haven’t seen any booking/activity .'.PHP_EOL .PHP_EOL.'performed on your account registered with us. Please share with us any specific issue if you are facing.'.PHP_EOL .PHP_EOL.'due to which you have stopped working with us.' .PHP_EOL .PHP_EOL.'Please share details on following email address : sales@trax.pk.' . PHP_EOL .PHP_EOL.'Regards'. PHP_EOL .PHP_EOL.'Trax Sales Team', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
