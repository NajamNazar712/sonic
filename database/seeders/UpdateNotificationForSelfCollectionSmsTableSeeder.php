<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateNotificationForSelfCollectionSmsTableSeeder extends Seeder
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
            array('id' => 75, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Self Collection SMS', 'type_id' => 2, 'subject' => null, 'body' => 'Dear Consignee [name],' . PHP_EOL . 'your parcel is Arrived at our office please collect it from following address' . PHP_EOL . '[address]', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
