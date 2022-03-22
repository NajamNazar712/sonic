<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForBirthdayMessageTableSeeder extends Seeder
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
            array('id' => 171, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Birthday Message', 'type_id' => 2, 'subject' => null, 'body' => 'Dear [name],' . PHP_EOL . 'You are a valued member and we appreciate your services. Have a hearty birthday; enjoy the day and all that comes with it. A very happy birthday from TRAX.', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
