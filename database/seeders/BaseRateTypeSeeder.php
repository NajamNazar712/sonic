<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BaseRateTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('base_rate_types')->truncate();
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('base_rate_types')->insert(array(
            array('id' => 1, 'name' => 'Base Rate', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'Fuel Surcharge','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 3, 'name' => 'Cash Handling','created_at'=>$timestamp,'updated_at'=>$timestamp)
        ));
    }
}
