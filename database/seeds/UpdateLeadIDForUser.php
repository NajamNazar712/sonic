<?php

use Illuminate\Database\Seeder;

class UpdateLeadIDForUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $leads = DB::table('leads')->select('id','lead_id')->where('admins.status', 1)->get();

        foreach ($leads as $lead) {
            DB::table('users')::where('lead_id',$lead->lead_id)->update(['lead_id' => $lead->id]);
        }
    }
}
