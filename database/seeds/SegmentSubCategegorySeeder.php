<?php

use App\Http\Models\Shipper\User;
use Illuminate\Database\Seeder;

class SegmentSubCategegorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('sub_category_segments')->insert(array(
            array('id' => 1, 'segment_id' => 1, 'name' => 'Logistics'),
            array('id' => 2, 'segment_id' => 1, 'name' => 'Express'),
            array('id' => 3, 'segment_id' => 1, 'name' => 'Warehouse'),
            array('id' => 4, 'segment_id' => 1, 'name' => 'International'),
            array('id' => 5, 'segment_id' => 2, 'name' => 'COD'),
            array('id' => 6, 'segment_id' => 2, 'name' => 'Logistics'),
            array('id' => 7, 'segment_id' => 2, 'name' => 'Express'),
            array('id' => 8, 'segment_id' => 2, 'name' => 'Warehouse'),
            array('id' => 9, 'segment_id' => 2, 'name' => 'International'),
            array('id' => 10, 'segment_id' => 2, 'name' => 'Hyperlocal'),
        ));


        $general_segments_id = User::where('segment_id',1);
        if($general_segments_id->exists()){
            $general_segments_id = $general_segments_id->get();
            foreach ($general_segments_id as $user){
                $user->sub_segment_id = 1;
                $user->save();
            }
        }

        $ecom_segments_id = User::where('segment_id',2);
        if($ecom_segments_id->exists()){
            $ecom_segments_id = $ecom_segments_id->get();
            foreach ($ecom_segments_id as $user){
                $user->sub_segment_id = 5;
                $user->save();
            }
        }
    }
}
