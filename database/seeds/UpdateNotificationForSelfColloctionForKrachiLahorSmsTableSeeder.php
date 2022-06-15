<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForSelfColloctionForKrachiLahorSmsTableSeeder extends Seeder
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
            array('id' => 7575, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Self Collection SMS For Lahore n Karachi', 'type_id' => 2, 'subject' => null, 'body' => 'Dear Consignee [name],' . PHP_EOL . 'your parcel is Arrived at our office please contact to our helpline (111-111-1111) for farther process.', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
