<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForMergedAddRemovedTableSeeder extends Seeder
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
            array('id' => 36, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Account Added as Sister Account', 'type_id' => 1, 'subject' => 'Account [account_id] [company_name_b] Added as Sister Account', 'body' => 'Dear [company_name_a],'.PHP_EOL.'Please note that the account [company_name_b] bearing account ID [account_id] has been merged with you as a sister account.'.PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL.'Regards,'.PHP_EOL.'TRAX'.PHP_EOL.'[trax_logo]'.PHP_EOL.'Address: Plot # 4, DMCHS,Block 7/8, Adjacent to IBL Building Centre,Tipu Sultan Road, Karachi,'.PHP_EOL.'Helpline: +92-21-3-877-22-22', 'updated_by' => 3, 'status' => 0),

            array('id' => 37, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Account Removed as Sister Account', 'type_id' => 1, 'subject' => 'Account [account_id] [company_name_b] Removed as Sister Account', 'body' => 'Dear [company_name_a],'.PHP_EOL.'Please note that the account [company_name_b] bearing account ID [account_id] has been removed from your group of sister account.'.PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL.'Regards,'.PHP_EOL.'TRAX'.PHP_EOL.'[trax_logo]'.PHP_EOL.'Address: Plot # 4, DMCHS,Block 7/8, Adjacent to IBL Building Centre,Tipu Sultan Road, Karachi,'.PHP_EOL.'Helpline: +92-21-3-877-22-22', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
