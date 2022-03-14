<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForShortOfBusinessShippersSeeder extends Seeder
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
            array('id' => 170, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Short Of Business Shippers', 'type_id' => 1, 'subject' => 'Short of Business Alert', 'body' => "Dear [sale_person], " . PHP_EOL . "Its been 3 days since your following Shippers haven't worked with Trax." . PHP_EOL . "[preview]", 'updated_by' => 664, 'status' => 0)
        ));

        DB::table('app_notifications')->insert(array(
            array('id' => 16, 'name' => 'Short of Business Shipper', 'title' => 'Short of Business Alert', 'body' => "Hi [sale_person] , your Shipper(s) haven't worked with us since 3 days".PHP_EOL."[shipper_names]", 'app_id' => 1,'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
