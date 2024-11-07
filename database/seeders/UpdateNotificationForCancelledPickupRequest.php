<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForCancelledPickupRequest extends Seeder
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
        array('id' => 63, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Cancelled Pickup Request', 'type_id' => 1, 'subject' => 'Pickup Request# [pickup_request_ID] updated as Cancelled', 'body' => 'Dear Shipper [shipper],' . PHP_EOL . PHP_EOL .'Please note that Pickup Request# [pickup_request_ID] is updated as Cancelled on [date].' . PHP_EOL . PHP_EOL . PHP_EOL .'Regards,'.PHP_EOL. PHP_EOL .'TRAX' . PHP_EOL . PHP_EOL . '[trax_logo]' . PHP_EOL . PHP_EOL . 'Address:  Plot # 4, DMCHS,Block 7/8, Adjacent to IBL Building Centre,Tipu Sultan Road, Karachi' . PHP_EOL . PHP_EOL . 'Helpline: +92-21-3-877-22-22', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
