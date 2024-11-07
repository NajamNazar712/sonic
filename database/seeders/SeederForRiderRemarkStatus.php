<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Http\Models\Rider\RiderRemarkStatus;

class SeederForRiderRemarkStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RiderRemarkStatus::truncate();

        DB::table('rider_remark_statuses')->insert(array(
            array('id' => 1, 'name' => 'Created', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()),
            array('id' => 2, 'name' => 'In Process', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()),
            array('id' => 3, 'name' => 'Resolved', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()),
        )); 
    }
}
