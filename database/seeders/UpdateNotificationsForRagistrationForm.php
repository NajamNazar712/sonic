<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationsForRagistrationForm extends Seeder
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
            array('id' => 38, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Customer Registration Form', 'type_id' => 1, 'subject' => 'Customer Registration Form', 'body' => 'Dear [shipper_name],' . PHP_EOL . 'Please follow the link below to download your Customer Registration Form. Click below if you accept our terms and conditions which are also mentioned in the document.' . PHP_EOL . PHP_EOL . '[button]' . PHP_EOL . PHP_EOL .'[link]'. PHP_EOL . 'Best Regards,' . PHP_EOL . 'Trax Logistcs.'. PHP_EOL .'[trax_logo]', 'updated_by' => 6, 'status' => 0)
        ));
    }
}
