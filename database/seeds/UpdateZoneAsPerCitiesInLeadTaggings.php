<?php

use App\Http\Models\Admin\Lead\LeadTagging;
use App\Http\Models\City;
use Illuminate\Database\Seeder;

class UpdateZoneAsPerCitiesInLeadTaggings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lead_taggings = LeadTagging::get();
        foreach ($lead_taggings as $lead_tagging) {
            $city = City::find($lead_tagging->city_id);
            if($city){
                if($city->zone_id){
                    LeadTagging::where('id', $lead_tagging->id)->update(['zone_id' => $city->zone_id]);
                }
            }
        }
    }
}
