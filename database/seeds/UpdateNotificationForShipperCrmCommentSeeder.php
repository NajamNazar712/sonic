<?php

use App\Http\Models\Notification;
use Illuminate\Database\Seeder;

class UpdateNotificationForShipperCrmCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Notification::where('id',136)->delete();
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert(array(
            array('id' => 136, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shipper CRM Comment', 'type_id' => 1, 'subject' => 'No Reply', 'body' => 'Claim Response' .PHP_EOL. 'Dear Shipper [shipper],' . PHP_EOL .'[message]', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
