<?php

use Illuminate\Database\Seeder;

class UpdateCrmRequestCaseNatureTypeForHighAging extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('crm_request_case_nature_types')->insert(array(
            array('id' => 34, 'nature_id' => 1, 'type' => 'High Aging', 'status_id' => 0)
        ));
    }
}
