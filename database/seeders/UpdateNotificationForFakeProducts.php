<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateNotificationForFakeProducts extends Seeder
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
            array('id' => 94, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Fake Products', 'type_id' => 1, 'subject' => 'Accounts Closure Due to Fake Products' , 'body' => 'Dear Sir/Madam,'. PHP_EOL .PHP_EOL.'We are intimating you regarding your account. We received many complaints from your respective customers that the products which they ordered are not as shown on your website/facebook page.'.PHP_EOL .PHP_EOL.'We regret to inform you that your account will be blocked until and unless quality of your product gets improved.' . PHP_EOL .PHP_EOL.'Regards,'. PHP_EOL .PHP_EOL.'Team Trax.', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
