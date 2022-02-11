<?php

use App\Http\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateNotificationsForReturnConfirmationPendingSms extends Seeder
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
        array('id' => 169, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'RCP Sms', 'type_id' => 2, 'subject' => null, 'body' => 'Dear [consignee],' . PHP_EOL .'
Your order [tracking_number] from [brand_name] worth Rs. [amount] is marked as returned. Reply with TRX (space) Y (tracking_number) to receive it. ', 'updated_by' => 3, 'status' => 1)
        )
        );
    }
}
