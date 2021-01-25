<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForUserCreationStepTableSeeder extends Seeder
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
            array('id' => 81, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Tagging History', 'type_id' => 1, 'subject' => 'Tagging History', 'body' => 'This is to bring to your kind attention that you have been assigned as a new sales person for the following shipper(s).'. PHP_EOL .'[preview]' , 'updated_by' => 3, 'status' => 0)
        ));
    }
}
