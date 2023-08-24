<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CrmResponseRateEmailSeeder extends Seeder
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
                'id' => 221,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'CRM Response Rate Email',
                'type_id' => 1,
                'subject' => 'CRM Response Rate',
                'body' => 'Dated: [date]' . PHP_EOL . PHP_EOL . '[preview]',
                'updated_by' => 615,
                'status' => 1,
            )
        ));
    }
}
