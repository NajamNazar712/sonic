<?php

use Illuminate\Database\Seeder;

class UpdateCrmTypeInternalToOtherTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('crm_request_case_nature_types')->where('type', 'Internal')->update(['type' => 'Other']);
    }
}
