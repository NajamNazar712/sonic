<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationsForNSAReattemptTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert(array(
            array('id' => 33, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reattempt Request for OSA/NSA Reason', 'type_id' => 1, 'subject' => 'Approval for Out of Service Area delivery with Additional Charges: [tracking_number]', 'body' => 'Dear Shipper,'. PHP_EOL . PHP_EOL .'You have approved for additional charges of shipment which has been updated as Out of Service Area.'. PHP_EOL .'Tracking No. = [tracking_number]'. PHP_EOL . 'Destination = [destination]'. PHP_EOL . 'Out of Service Area Charges = [nsa_osa_estimated_charges]' . PHP_EOL .'Remarks = [remarks]'. PHP_EOL . PHP_EOL . PHP_EOL .  'Regards,'.PHP_EOL.'TRAX'.PHP_EOL.'Address: Plot # 4, DMCHS,Block 7/8, Adjacent to IBL Building Centre,Tipu Sultan Road, Karachi
Helpline: +92-3-041-111-232', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
