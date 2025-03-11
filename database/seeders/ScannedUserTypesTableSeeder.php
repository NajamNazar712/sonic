<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScannedUserTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('scanned_user_types')->insert([
            ['id' => 1, 'name' => 'admins'],
            ['id' => 2, 'name' => 'users'],
            ['id' => 3, 'name' => 'substitute_users'],
            ['id' => 4, 'name' => 'retail_users'],
            ['id' => 5, 'name' => 'riders'],
        ]);
    }
}
