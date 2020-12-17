<?php

use Illuminate\Database\Seeder;

class LeadStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('lead_statuses')->truncate();

        DB::table('lead_statuses')->insert(array(
            array('id' => 1, 'name' => 'Lead Received'),
            array('id' => 2, 'name' => 'Unresponsive'),
            array('id' => 3, 'name' => 'Irrelevant'),
            array('id' => 4, 'name' => 'Not Interested'),
            array('id' => 2, 'name' => 'Data Gathered'),
            array('id' => 2, 'name' => 'Proposal Sent'),
            array('id' => 5, 'name' => 'Proceed for Account Activation'),
            array('id' => 6, 'name' => 'Account Activated')
        ));
    }
}
