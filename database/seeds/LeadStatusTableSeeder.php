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
            array('id' => 2, 'name' => 'Follow-Up'),
            array('id' => 3, 'name' => 'Proposal Sent'),
            array('id' => 4, 'name' => 'Unresponsive'),
            array('id' => 5, 'name' => 'Proceed for Account Activation'),
            array('id' => 6, 'name' => 'Account Activated')
        ));
    }
}
