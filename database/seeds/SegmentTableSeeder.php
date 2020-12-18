<?php

use Illuminate\Database\Seeder;

class SegmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $segments_id = User::whereNull('segment_id');
        if($segments_id->exists()){
            $segments_id = $segments_id->get();
            foreach ($segments_id as $user){
                $user->segment_id = 1;
                $user->save();
            }
        }
    }
}
