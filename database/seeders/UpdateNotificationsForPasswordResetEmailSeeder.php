<?php

use Illuminate\Database\Seeder;

class UpdateNotificationsForPasswordResetEmailSeeder extends Seeder
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
            array('id' => 159, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Password Reset Successfully', 'type_id' => 1, 'subject' => 'Password Reset Successfully', 'body' => 'Dear User,' . PHP_EOL . 'It is to notify you that your password has been reset successfully.' . PHP_EOL . PHP_EOL . 'Regards,' . PHP_EOL . 'TRAX' . PHP_EOL . 'Address:  Plot # 105, Mehran Town Sector 7 A Korangi, Karachi, Sindh 74900' . PHP_EOL . 'Helpline: +92-3-041-111-232', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
