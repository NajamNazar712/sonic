<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationsForCRMEmailTableSeeder extends Seeder
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
            array('id' => 31, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'CRM Notification Request', 'type_id' => 1, 'subject' => '[request_id] [tracking_number]', 'body' => '[shipper_name][email][phone][destination][channel][case_nature][case_nature_type][tracking_number][details]'. PHP_EOL . PHP_EOL . PHP_EOL.'Regards,'.PHP_EOL.'TRAX Logistics'.PHP_EOL.'Address: Plot # 4, DMCHS,Block 7/8, Adjacent to IBL Building Centre,Tipu Sultan Road, Karachi
Helpline: +92-3-041-111-232', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
