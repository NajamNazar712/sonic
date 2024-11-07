<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CsatCaseTypeSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('global_settings')->where('type', 'csat_type')->orWhere('type', 'csat_formula')->delete();

        DB::table('global_settings')->insert([
            [
                'setting_value' => 1,
                'type' => 'csat_type',
                'text' => '1,2,3,5,11,12,17,10,37,39,20,33,14',
                'created_at' => Carbon::now(),
            ],
            [
                'setting_value' => 1,
                'type' => 'csat_formula',
                'text' => '4,5',
                'created_at' => Carbon::now(),
            ],
        ]);
    }
}