<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;
class ExpectedShipmentsChangeNotification extends Seeder
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
            array(
                'id' => 255,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Expected Shipments Changed',
                'type_id' => 1,
                'subject' => 'Expected Shipments Changed',
                'body' => 'Dear Concern, '. PHP_EOL .'Expected shipments count of [shipper] changed from [from] to [to]',
                'updated_by' => 3756,
                'status' => 1,
            )
        ));
    }
}
