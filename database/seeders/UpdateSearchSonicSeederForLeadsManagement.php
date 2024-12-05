<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateSearchSonicSeederForLeadsManagement extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Leads  >  Leads Management', 'url'=>'admin.leads.index', 'permission_id' => 416));
    }
}
