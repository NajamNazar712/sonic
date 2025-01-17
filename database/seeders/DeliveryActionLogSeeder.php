<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DeliveryActionLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('delivery_actions')->insert(array(
            array('id' => 1, 'name' => 'Contact Support'),
            array('id' => 2, 'name' => 'Request Details'),
            array('id' => 3, 'name' => 'Contact Consignee'),
            array('id' => 4, 'name' => 'Navigate'),
            array('id' => 5, 'name' => 'Undelivered'),
            array('id' => 6, 'name' => 'Delivered')
        ));
    }
}
