<?php

use App\Http\Models\CRM\CrmRequestChannel;
use Illuminate\Database\Seeder;

class UpdateCrmChannelNameForTraxApp extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $crm_channel = CrmRequestChannel::where('id', 7);
        if($crm_channel->exists()){
            $crm_channel = $crm_channel->first();
            $crm_channel->channel = "Trax App";
            $crm_channel->save();
        }
    }
}
