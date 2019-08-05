<?php

use Illuminate\Database\Seeder;

class UpdateNotificationPendingRatesTableSeeder extends Seeder
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
            array('id' => 34, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Pending Rates Added', 'type_id' => 1, 'subject' => 'Rate updated [user_id] on [updated_at]', 'body' => 'Please check that the tagged sales person [tagged_sales_person], has updated the rates of the account no [user_id] on [updated_at]', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
