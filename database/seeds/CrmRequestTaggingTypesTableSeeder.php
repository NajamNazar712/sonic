<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CrmRequestTaggingTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('crm_request_tagging_types')->truncate();
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('crm_request_tagging_types')->insert(array(
            array('id' => 1, 'name' => 'Department', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'User','created_at'=>$timestamp,'updated_at'=>$timestamp)
        ));
    }
}
