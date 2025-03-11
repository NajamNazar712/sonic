<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateNotificationForNonComplianceOfRequirements extends Seeder
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
            array('id' => 93, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Non-Compliance of Requirements', 'type_id' => 1, 'subject' => 'Accounts Closure Due to Non-Compliance of Requirements' , 'body' => 'Dear Sir/Madam,'. PHP_EOL .PHP_EOL.'I am writing this in reference to the account closure due to incomplete documents halted with us since a week.'.PHP_EOL .PHP_EOL.'We have tried to approach you multiple times to attain those documents as they are essential for account verification and payment proceedings but got no response from your side so I regret to inform you that we are closing your account but whenever you will need our services you can resume your account by providing us the required documents.' . PHP_EOL .PHP_EOL .'I would like to request you to fulfill the requirements as per the procedure followed according to the Trax rules at your earliest. Your cooperation will be highly appreciated.'. PHP_EOL .PHP_EOL.'Regards,'. PHP_EOL .PHP_EOL.'Team Trax.', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
