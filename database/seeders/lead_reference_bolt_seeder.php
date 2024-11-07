<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class lead_reference_bolt_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now();
        DB::table('lead_references')->insert(array(
           array('id'=>10,'name'=>'Bolt','created_at'=>$timestamp,'updated_at'=>$timestamp)
        ));
    }
}
