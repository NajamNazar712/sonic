<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForRatesRejection extends Seeder
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
        array('id' => 64, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Rates Rejection', 'type_id' => 1, 'subject' => '[account_id][name] Rates Rejected', 'body' => 'Dear Concerns,' . PHP_EOL . PHP_EOL .'The rates for the account [account_id] [name] have been rejected by finance department. Please contact for resolution.' . PHP_EOL . PHP_EOL . PHP_EOL .'Regards,'.PHP_EOL. PHP_EOL .'TRAX' . PHP_EOL . PHP_EOL . '[trax_logo]' . PHP_EOL . PHP_EOL . 'Address:  Plot # 4, DMCHS,Block 7/8, Adjacent to IBL Building Centre,Tipu Sultan Road, Karachi' . PHP_EOL . PHP_EOL . 'Helpline: +92-21-3-877-22-22', 'updated_by' => 3, 'status' => 1)
        ));
    }
}
