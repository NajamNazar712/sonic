<?php

use Illuminate\Database\Seeder;

class UpdateAdminSearchSonicSeederForIncidenceMonitoring extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Quality Assurance > Incidence Monitoring', 'url'=>'admin.retail.accounts.index', 'permission_id' => 535));
        
    }
}
