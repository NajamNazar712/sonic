<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApoloCronlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('apollo_cron_job_logs')->insert([
            [
                'id' => 1,
                'job_name' => 'apollo:fetch-shipments-status',
                'last_run_time' => null,  // Set to `null` initially or use `Carbon::now()` if needed
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
