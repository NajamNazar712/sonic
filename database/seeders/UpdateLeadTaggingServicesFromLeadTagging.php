<?php

use Illuminate\Database\Seeder;

class UpdateLeadTaggingServicesFromLeadTagging extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lead_taggings = DB::table('lead_taggings')->get();
        foreach ($lead_taggings as $lead_tagging) {
            
            DB::table('lead_tagging_services')->insert(['lead_tagging_id' => $lead_tagging->id, 'service_id' => $lead_tagging->service_id]);

        }
    }
}
