<?php

use App\Http\Models\Notification;
use Illuminate\Database\Seeder;

class ReturnDeliverToShipperEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        Notification::where('id',39)->update(
            ['updated_at' => $timestamp ,'body' => 'Dear Customer,' . PHP_EOL .'Tracking ID [tracking_number] has been returned to you on [status_updated_at] and received by [receiver_name]'. PHP_EOL . 'Hope you are doing great please note that below mentioned Total Shipments [shipments_count] are returned back to you in safe and sound condition today under Return Note Number  [return_notes_id]. In case of any query regarding these shipments you may respond us back in 48 hours.']
        );
    }
}
