<?php


use Carbon\Carbon;

use Illuminate\Database\Seeder;

class UpdateNotificationsForCRMSMSTableSeeder extends Seeder
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
            array('id' => 142, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'CRM SMS Notification Request', 'type_id' => 2, 'subject' => NULL, 'body' => 'Request ID: [request_id] , Tracking Number: [tracking_number]'. PHP_EOL .' Shipper Name: [shipper_name]'. PHP_EOL .' Shipper Email: [email]'. PHP_EOL .'Shipper Phone: [phone]'. PHP_EOL .'Destination: [destination]'. PHP_EOL .'Channel: [channel]'. PHP_EOL .'Case Nature: [case_nature]'. PHP_EOL .'Case Nature Type: [case_nature_type]'. PHP_EOL .'Status: [status]'. PHP_EOL .'Description: [details]'. PHP_EOL . PHP_EOL . PHP_EOL.'Regards,'.PHP_EOL.'TRAX'.PHP_EOL.'Address: Plot # 4, DMCHS,Block 7/8, Adjacent to IBL Building Centre,Tipu Sultan Road, Karachi
Helpline: +92-3-041-111-232', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
