<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateNotificationForSalesPersonTagSms extends Seeder
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
            array('id' => 119, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Tagging History SMS', 'type_id' => 2, 'subject' => null, 'body' => 'The Account : [shipper_name] has been assigned a new sales person [new_sale_person] ' , 'updated_by' => 3, 'status' => 0)
        ));
    }
}
