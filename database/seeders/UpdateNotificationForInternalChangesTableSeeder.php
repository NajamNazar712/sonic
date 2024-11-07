<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForInternalChangesTableSeeder extends Seeder
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
            array('id' => 186, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Role Changed', 'type_id' => 1, 'subject' => 'Role Changed', 'body' => 'Dear Concern,' . PHP_EOL . 'Role Changed By [admin] To [role]', 'updated_by' => 7, 'status' => 1),
        ));
    }
}
