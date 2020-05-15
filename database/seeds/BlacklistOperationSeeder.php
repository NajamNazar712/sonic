<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class BlacklistOperationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('blacklist_operations')->truncate();
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('blacklist_operations')->insert(array(
            array('id' => 1, 'name' => 'And', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'Or','created_at'=>$timestamp,'updated_at'=>$timestamp)
        ));
    }
}
