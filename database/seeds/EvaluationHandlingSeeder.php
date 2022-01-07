<?php

use Illuminate\Database\Seeder;

class EvaluationHandlingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('evaluation_handlings')->insert(array(
            array('id' => 1, 'campaign_id' => 1, 'handling' => 'Greetings'),
            array('id' => 2, 'campaign_id' => 1, 'handling' => 'Communication Skills'),
            array('id' => 3, 'campaign_id' => 1, 'handling' => 'Protocol Compliance'),
            array('id' => 4, 'campaign_id' => 1, 'handling' => 'Fatal Errors'),
            array('id' => 5, 'campaign_id' => 1, 'handling' => 'Call Closure'),
            array('id' => 6, 'campaign_id' => 2, 'handling' => 'Valid and Invalid '),
            array('id' => 7, 'campaign_id' => 2, 'handling' => 'In-Process'),
            array('id' => 8, 'campaign_id' => 2, 'handling' => 'Protocol Compliance'),
            array('id' => 9, 'campaign_id' => 2, 'handling' => 'Grammar'),
            array('id' => 10, 'campaign_id' => 2, 'handling' => 'Fatal Errors'),
            array('id' => 11, 'campaign_id' => 2, 'handling' => 'Resolution'),
            array('id' => 12, 'campaign_id' => 3, 'handling' => 'Salutation & Subject line'),
            array('id' => 13, 'campaign_id' => 3, 'handling' => 'Communication and Grammar'),
            array('id' => 14, 'campaign_id' => 3, 'handling' => 'Sentence and Paragraph Structure'),
            array('id' => 15, 'campaign_id' => 3, 'handling' => 'Protocol Compliance'),
            array('id' => 16, 'campaign_id' => 3, 'handling' => 'Signature'),
            array('id' => 17, 'campaign_id' => 3, 'handling' => 'Fatal Errors'),
            array('id' => 18, 'campaign_id' => 3, 'handling' => 'Resolution and Closure'),
        ));
    }
}
