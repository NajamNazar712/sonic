<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CrmClosedCaseEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->where('name', 'CRM Closed Case Email')->delete();

        DB::table('notifications')->insert(array(
            array(
                'id' => 222,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'CRM Closed Case Email',
                'type_id' => 1,
                'subject' => 'CRM Closed Case',
                'body' => 'Dear [shipper], These tickets have been closed with resolution. Please rate us about your experience with resolution by clicking below link On Request Number.'.PHP_EOL.'[preview]',
                'updated_by' => 615,
                'status' => 1,
            )
        ));
    }
}
