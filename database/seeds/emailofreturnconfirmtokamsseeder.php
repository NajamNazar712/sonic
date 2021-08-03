<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class emailofreturnconfirmtokamsseeder extends Seeder
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
            array('id' => 143, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Email For Return Confirm To KAMS', 'type_id' => 1, 'subject' => ' Return Confirm Mail to KAMS ', 'body' => '[preview]', 'updated_by' => 3, 'status' => 1)
        ));
    }
}
