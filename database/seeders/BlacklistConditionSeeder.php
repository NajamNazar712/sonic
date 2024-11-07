<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class BlacklistConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('blacklist_conditions')->truncate();
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('blacklist_conditions')->insert(array(
            array('id' => 1, 'name' => 'Return Ratio', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'Delivery Ratio','created_at'=>$timestamp,'updated_at'=>$timestamp)
//            array('id' => 3, 'name' => 'No of Orders','created_at'=>$timestamp,'updated_at'=>$timestamp)
        ));
    }
}
