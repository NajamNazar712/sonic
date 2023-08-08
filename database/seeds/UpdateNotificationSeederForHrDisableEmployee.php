<?php

use Illuminate\Database\Seeder;

class UpdateNotificationSeederForHrDisableEmployee extends Seeder
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
             array('id' => 218, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Disable Contractual Employee', 'type_id' => 1, 
            'subject' => 'Disabled Contractual Employee', 
            'body' => 'Dear Concern,
            This is inform to you, [employee_name] ([employee_type]) of department ([depatment]) has been disabled by [updated_by].',
            'updated_by' => 7, 'status' => 1)
         ));
    }
}
