<?php

use Illuminate\Database\Seeder;

class RiderDeactivateAutomaticallySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert(array(
            array('id' => 155, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Rider deactivate automatically After pickup and Delivery Sheets not made', 'type_id' => 1, 'subject' => ' Rider Deactivation ', 'body' => '[preview]', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
