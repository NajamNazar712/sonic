<?php

use Illuminate\Database\Seeder;

class EvaluationCampaignNatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('evaluation_campaigns')->insert(array(
            array('id' => 1, 'campaign_id' => 1, 'campaign' => 'Incoming/RCP'),
            array('id' => 3, 'campaign_id' => 2, 'campaign' => 'Complains/Claim'),
            array('id' => 5, 'campaign_id' => 3, 'campaign' => 'Email Live Chat'),
        ));

        DB::table('evaluation_natures')->insert(array(
            array('id' => 1, 'nature' => 'Complain'),
            array('id' => 2, 'nature' => 'Inquiry'),
            array('id' => 3, 'nature' => 'Lead'),
            array('id' => 4, 'nature' => 'Followup'),
            array('id' => 5, 'nature' => 'Feedback'),
            array('id' => 6, 'nature' => 'Service Request'),
        ));
    }
}
