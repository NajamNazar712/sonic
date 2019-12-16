<?php

use Illuminate\Database\Seeder;

class UpdateNotificationsForReversePickupForContactPerson extends Seeder
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
            array('id' => 52, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reverse Pickup for Contact Person', 'type_id' => 2, 'subject' => NULL, 'body' => 'Dear [contact_person],' . PHP_EOL . 'A rider is on the way for pickup on behalf of [company_name].' . PHP_EOL . 'Rider: [rider_name], [rider_phone]' . PHP_EOL . 'Trax: 0213-8772222', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
