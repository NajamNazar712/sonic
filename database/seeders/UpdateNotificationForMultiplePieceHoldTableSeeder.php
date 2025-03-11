<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateNotificationForMultiplePieceHoldTableSeeder extends Seeder
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
            array('id' => 84, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Multiple Piece Shipment On Hold', 'type_id' => 1, 'subject' => 'Shipments on Hold [date]', 'body' => 'Dear [shipper_name],' . PHP_EOL . 'Please review below listed shipments On Hold with us due to discrepancy in number of pieces. Please go to On Hold shipments screen and confirm action required from our end. '. PHP_EOL. PHP_EOL . '[tracking_number][product_description][cod_amount][origin][destination][status]'. PHP_EOL . PHP_EOL . '[preview]' . PHP_EOL . PHP_EOL .'Regards,'. PHP_EOL . PHP_EOL . 'Team Trax', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
