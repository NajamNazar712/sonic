<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationsForFnf extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert(array(
            array('id' => 146, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'FNF Request', 'type_id' => 1, 'subject' => 'FNF Request [emp_id]', 'body' => 'The final settlement of FNF request of below staff awaits your approval.'. PHP_EOL .  PHP_EOL . '[link]'. PHP_EOL .  PHP_EOL . ' Trax Id : [emp_id]'. PHP_EOL . 'Name : [name]' . PHP_EOL . 'Designation : [designation]' , 'updated_by' => 3, 'status' => 1)
        ));
    }
}
