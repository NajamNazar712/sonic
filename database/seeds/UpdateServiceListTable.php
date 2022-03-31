<?php

use Illuminate\Database\Seeder;

class UpdateServiceListTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now();

        DB::table('service_list')->insert(array(
            array('id' => 6, 'name' => "All Services", 'status' => 1, 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
        ));
    }
}
