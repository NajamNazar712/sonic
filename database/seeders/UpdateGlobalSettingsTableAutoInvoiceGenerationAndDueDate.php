<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class UpdateGlobalSettingsTableAutoInvoiceGenerationAndDueDate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('global_settings')->insert(array(
            array('type' => 'auto_invoice_generation_time', 'setting_value' => 8, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('type' => 'due_date_days', 'setting_value' => 10, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
