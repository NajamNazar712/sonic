<?php

namespace Database\Seeders;

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
            array('id' => 5, 'name' => 'Data Gathered'),
            array('id' => 6, 'name' => 'Proposal Sent'),
            array('id' => 7, 'name' => 'Under Negotiation'),
            array('id' => 8, 'name' => 'Follow-up'),
            array('id' => 9, 'name' => 'In-process for Activation'),
            array('id' => 10, 'name' => 'Rejected'),
            array('id' => 11, 'name' => 'Blocked'),
            array('id' => 12, 'name' => 'Account Activated')
        ));
    }
}
