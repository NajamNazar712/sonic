<?php

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CrmProgressReportEmailSeeder extends Seeder
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
                'id' => 223,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'CRM Progress Report Email',
                'type_id' => 1,
                'subject' => 'CRM Progress Report',
                'body' => '[preview]',
                'updated_by' => 615,
                'status' => 1,
            )
        ));
    }
}
