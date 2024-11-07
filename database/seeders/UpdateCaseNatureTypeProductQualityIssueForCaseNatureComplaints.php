<?php

use Illuminate\Database\Seeder;

class UpdateCaseNatureTypeProductQualityIssueForCaseNatureComplaints extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('crm_request_case_nature_types')->insert(array(
            array('nature_id' => 1, 'type' => 'Product/Quality Issue'),
        ));
    }
}
