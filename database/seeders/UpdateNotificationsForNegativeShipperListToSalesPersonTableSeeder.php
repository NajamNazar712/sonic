<?php

namespace Database\Seeders;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationsForNegativeShipperListToSalesPersonTableSeeder extends Seeder
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
            array('id' => 56, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Negative Shipper List', 'type_id' => 1, 'subject' => 'Negative balance shippers list', 'body' => 'Please check these shippers have negative balance.'. PHP_EOL . PHP_EOL. PHP_EOL.' [account_id] [shipper_name]'. PHP_EOL . '[preview]', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
