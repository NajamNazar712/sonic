<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForSelfCollectionForKarachiLahoreSmsTableSeeder extends Seeder
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
            array('id' => 178, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Self Collection SMS For Karachi & Lahore', 'type_id' => 2, 'subject' => null, 'body' => 'Dear [name],' . PHP_EOL . 'your parcel is arrived at our office please contact us at our helpline (021-111-118-729) for further process.', 'status' => 1,'updated_by' => 7)
        ));



    }
}
