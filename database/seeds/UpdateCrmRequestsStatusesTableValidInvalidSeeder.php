<?php

use Illuminate\Database\Seeder;

class UpdateCrmRequestsStatusesTableValidInvalidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('crm_request_statuses')->insert(array(
            array('id' => 6, 'name' => 'Valid'),
            array('id' => 7, 'name' => 'Invalid')
        ));
    }
}
