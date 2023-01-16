<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForReversionReportTableSeeder extends Seeder
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
            array('id' => 205, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reversion From Delivered', 'type_id' => 1, 'subject' => 'Reversion From Delivered Shipment(s)', 'body' => 'Dear [admin].'. PHP_EOL .'Below Shipment(s) are reverted from Shipment Delivered, Kindly check'. PHP_EOL .'[preview]' , 'updated_by' => 7, 'status' => 0)
        ));
    }
}
