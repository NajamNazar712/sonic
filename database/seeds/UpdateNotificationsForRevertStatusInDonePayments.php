<?php

use Illuminate\Database\Seeder;

class UpdateNotificationsForRevertStatusInDonePayments extends Seeder
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
            array('id' => 92, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Email To Shipper On Revert Status In Done Payments', 'type_id' => 1, 'subject' => 'Email To Shipper On Revert Status In Done Payments', 'body' => 'Dear [shipper_name],'. PHP_EOL . PHP_EOL .'Your payment ID [payment_id],has reverted today we will process it again in next working day.'. PHP_EOL . PHP_EOL .'Regards'. PHP_EOL .'Team TRAX', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
