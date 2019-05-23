<?php

use Illuminate\Database\Seeder;

class CrmRequestStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('crm_request_statuses')->truncate();

        DB::table('crm_request_statuses')->insert(array(
            array('id' => 1, 'name' => 'Launched'),
            array('id' => 2, 'name' => 'In-Process'),
            array('id' => 3, 'name' => 'Resolved'),
            array('id' => 4, 'name' => 'Closed'),
            array('id' => 5, 'name' => 'Re-Open')
        ));
    }
}
