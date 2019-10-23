<?php

use Illuminate\Database\Seeder;

class PickupActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pickup_actions')->insert(array(
            array('id' => 1, 'name' => 'Navigate'),
            array('id' => 2, 'name' => 'Call'),
            array('id' => 3, 'name' => 'Not Pick'),
            array('id' => 4, 'name' => 'Pick')
        ));
    }
}
