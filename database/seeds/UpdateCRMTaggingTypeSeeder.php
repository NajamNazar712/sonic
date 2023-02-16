<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateCRMTaggingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('crm_request_tagging_types')->insert(array(
            array('id' => 4, 'name' => 'KAM', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 5, 'name' => 'Operations','created_at'=>$timestamp,'updated_at'=>$timestamp)
        ));
    }
}
