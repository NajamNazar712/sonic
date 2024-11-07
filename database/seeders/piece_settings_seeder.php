<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class piece_settings_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time_stamp =\Carbon\Carbon::now();

        \Illuminate\Support\Facades\DB::table('trax_piece_settings')->insert(array(
            array('id' => 1,'description'=> 'for one enter pieces count without pieces' ,'status'=>1,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id' => 2,'description'=> 'for from - to with assign pieces' ,'status'=>1,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id' => 3,'description'=> 'for scan pieces with assign pieces' ,'status'=>1,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),

        ));
    }
}
