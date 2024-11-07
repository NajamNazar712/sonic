<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationsForTelenorDeliveredShipmentSMS extends Seeder
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
            array('id' => 104, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Telenor Delivered Shipment SMS', 'type_id' => 2, 'subject' => 'Telenor Delivered Shipment
', 'body' => 'معززصارف ٹریکس کی جانب سے آپ کوٹیلی نار کا ڈیبٹ کارڈ فراہم کردیا گیا ہے۔ کارڈ موصول نہ ہونے کی صورت میں ٹریکس ہیلپ لائین پر رابطہ کریں۔' . PHP_EOL . 'ٹریکس ہیلپ لائین:22 22 77 8-0213', 'updated_by' => 3, 'status' => 0),
        ));
    }
}
