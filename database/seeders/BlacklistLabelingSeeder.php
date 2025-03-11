<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class BlacklistLabelingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('blacklist_labelings')->truncate();
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('blacklist_labelings')->insert(array(
            array('id' => 1, 'name' => 'Manual', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'Automatic','created_at'=>$timestamp,'updated_at'=>$timestamp)
        ));
    }
}
