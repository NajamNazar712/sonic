<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class TierTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tier_types')->truncate();
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('tier_types')->insert(array(
            array('id' => 1, 'name' => 'Internal', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'External','created_at'=>$timestamp,'updated_at'=>$timestamp)
        ));
    }
}
