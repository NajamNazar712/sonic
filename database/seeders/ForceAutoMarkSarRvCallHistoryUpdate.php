<?php

use App\RvAgentCallHistory;
use Illuminate\Database\Seeder;

class ForceAutoMarkSarRvCallHistoryUpdate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Globaly updated.
        RvAgentCallHistory::where('remarks', 'As per CX dept')->update(['updated_type_id'=>1, 'updated_by_id'=>346]);
    }
}
