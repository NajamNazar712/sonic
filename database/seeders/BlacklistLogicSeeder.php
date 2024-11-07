<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class BlacklistLogicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('blacklist_logics')->truncate();
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('blacklist_logics')->insert(array(
            array('id' => 1, 'name' => 'equals to', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'not equals to','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 3, 'name' => 'greater than equals to','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 4, 'name' => 'greater than','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 5, 'name' => 'less than equals to','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 6, 'name' => 'less than','created_at'=>$timestamp,'updated_at'=>$timestamp)
        ));
    }
}
