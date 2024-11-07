<?php

use App\Http\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateNotificationsTableSeederForAddress extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            Notification::where('id',31)->update(['body' => '[shipper_name][email][phone][destination][channel][case_nature][case_nature_type][tracking_number][details][status]'. PHP_EOL . PHP_EOL . PHP_EOL.'Best Regards,'.PHP_EOL.'Team TRAX'.PHP_EOL.'Address: Plot 105, Sector 7-A, Mehran Town, Korangi, Karachi
For Help Dial: 021-111-118-729']);

        Notification::where('id',33)->update(['body' => 'Dear Shipper,'. PHP_EOL . PHP_EOL .'You have approved for additional charges of shipment which has been updated as Out of Service Area.'. PHP_EOL .'Tracking No. = [tracking_number]'. PHP_EOL . 'Destination = [destination]'. PHP_EOL . 'Out of Service Area Charges = [nsa_osa_estimated_charges]' . PHP_EOL .'Remarks = [remarks]'. PHP_EOL . PHP_EOL . PHP_EOL .  'Best Regards,'.PHP_EOL.'Team TRAX'.PHP_EOL.'Address: Plot 105, Sector 7-A, Mehran Town, Korangi, Karachi
For Help Dial: 021-111-118-729']);


        Notification::where('id',36)->update(['body' => 'Dear [company_name_a],'.PHP_EOL.'Please note that the account [company_name_b] bearing account ID [account_id] has been merged with you as a sister account.'.PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL.'Best Regards,'.PHP_EOL.'Team TRAX'.PHP_EOL.'[trax_logo]'.PHP_EOL.'Address: Plot 105, Sector 7-A, Mehran Town, Korangi, Karachi,'.PHP_EOL.'For Help Dial: 021-111-118-729']);

        Notification::where('id',37)->update(['body' => 'Dear [company_name_a],'.PHP_EOL.'Please note that the account [company_name_b] bearing account ID [account_id] has been removed from your group of sister account.'.PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL.'Best Regards,'.PHP_EOL.'Team TRAX'.PHP_EOL.'[trax_logo]'.PHP_EOL.'Address: Plot 105, Sector 7-A, Mehran Town, Korangi, Karachi,'.PHP_EOL.'For Help Dial: 021-111-118-729']);

        Notification::where('id',63)->update(['body' => 'Dear Shipper [shipper],' . PHP_EOL . PHP_EOL .'Please note that Pickup Request# [pickup_request_ID] is updated as Cancelled on [date].' . PHP_EOL . PHP_EOL . PHP_EOL .'Best Regards,'.PHP_EOL. PHP_EOL .'Team TRAX' . PHP_EOL . PHP_EOL . '[trax_logo]' . PHP_EOL . PHP_EOL . 'Address: Plot 105, Sector 7-A, Mehran Town, Korangi, Karachi,' . PHP_EOL . PHP_EOL . 'For Help Dial: 021-111-118-729']);

        Notification::where('id',64)->update(['body' => 'Dear Concerns,' . PHP_EOL . PHP_EOL .'The rates for the account [account_id] [name] have been rejected by finance department. Please contact for resolution.' . PHP_EOL . PHP_EOL . PHP_EOL .'Best Regards,'.PHP_EOL. PHP_EOL .'Team TRAX' . PHP_EOL . PHP_EOL . '[trax_logo]' . PHP_EOL . PHP_EOL . 'Address: Plot 105, Sector 7-A, Mehran Town, Korangi, Karachi,' . PHP_EOL . PHP_EOL . 'For Help Dial: 021-111-118-729']);

        Notification::where('id',142)->update(['body' => 'Request ID: [request_id] , Tracking Number: [tracking_number]'. PHP_EOL .' Shipper Name: [shipper_name]'. PHP_EOL .' Shipper Email: [email]'. PHP_EOL .'Shipper Phone: [phone]'. PHP_EOL .'Destination: [destination]'. PHP_EOL .'Channel: [channel]'. PHP_EOL .'Case Nature: [case_nature]'. PHP_EOL .'Case Nature Type: [case_nature_type]'. PHP_EOL .'Status: [status]'. PHP_EOL .'Description: [details]'. PHP_EOL . PHP_EOL . PHP_EOL.'Best Regards,'.PHP_EOL.'Team TRAX'.PHP_EOL.'Address: Plot 105, Sector 7-A, Mehran Town, Korangi, Karachi,
For Help Dial: 021-111-118-729']);
    }
}
