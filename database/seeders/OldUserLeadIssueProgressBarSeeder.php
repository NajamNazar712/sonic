<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Shipper\User;

class OldUserLeadIssueProgressBarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::where('lead_id', '!=', null)
        ->where('status', 3)
        ->where('on_board_status', '<', 1)
        ->orWhereNull('on_board_status')
        ->where('created_at', '>', '2024-06-13 00:00:00')
        ->update(['on_board_status' => 1]);
    }
}
