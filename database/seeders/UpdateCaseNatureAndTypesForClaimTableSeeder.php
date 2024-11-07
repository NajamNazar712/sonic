<?php

use Illuminate\Database\Seeder;

class UpdateCaseNatureAndTypesForClaimTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('crm_request_case_nature')->insert(array(
            array('id' => 4, 'name' => 'Claim'),
        ));

        DB::table('crm_request_case_nature_types')->insert(array(
            array('nature_id' => 4, 'type' => 'Shipment Damage'),
            array('nature_id' => 4, 'type' => 'Content Short'),
            array('nature_id' => 4, 'type' => 'Lost'),
            array('nature_id' => 4, 'type' => 'Theft & Snatching'),
            array('nature_id' => 4, 'type' => 'Tariff'),
        ));
    }
}
