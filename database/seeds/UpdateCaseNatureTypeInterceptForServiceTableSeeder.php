<?php

use Illuminate\Database\Seeder;

class UpdateCaseNatureTypeInterceptForServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('crm_request_case_nature_types')->insert(array(
            array('nature_id' => 2, 'type' => 'Intercept'),
        ));
    }
}
