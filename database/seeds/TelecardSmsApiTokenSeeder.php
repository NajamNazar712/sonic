<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TelecardSmsApiTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('guest_api_tokens')->insert(array(
                array('id' => 1,'name' => 'telecard_rcp_sms','created_at' => $timestamp, 'updated_at' => $timestamp, 'token' => uniqid(base64_encode(str_random(60))))
            )
        );
    }
}
