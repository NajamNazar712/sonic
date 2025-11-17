<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class draftTicketStatusAddOnGlobalSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('global_settings')->insert(array(
            array('type' => 'ticket_draft_shipper_status', 'setting_value' => 1,'text'=> "2,3,4,5,7,8,9,11,12,13,49,53,54,55,56,62,65,66,67,68,72,73,74", 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
