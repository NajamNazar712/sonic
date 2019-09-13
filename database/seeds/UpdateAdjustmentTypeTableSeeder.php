<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateAdjustmentTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('adjustment_types')->insert(array(
            array('id' => 5, 'name' => 'Replacement to regular adjustment', 'created_at' => $timestamp, 'updated_at' => $timestamp),

        ));
    }
}
