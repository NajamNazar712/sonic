<?php

use Illuminate\Database\Seeder;

class ReferenceBoltSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('lead_references')->insert(array(
            array('id' => 8, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Bolt'),
        ));
    }
}
