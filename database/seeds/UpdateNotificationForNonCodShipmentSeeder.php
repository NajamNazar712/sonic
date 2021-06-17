<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationForNonCodShipmentSeeder extends Seeder
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
            array('id' => 132, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Non Cod Shipment', 'type_id' => 2, 'subject' => null, 'body' => 'Dear customer' . PHP_EOL . 'your order from [company_name] is on the way. Please Keep your CNIC ready.' . PHP_EOL . 'AWB#[tracking_number].' . PHP_EOL . 'Rider:[rider]' . PHP_EOL . 'Trax-03041111232', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
