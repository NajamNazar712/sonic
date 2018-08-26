<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\Admin;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->truncate();

        Admin::create([
            'name' => 'admin',
            'email' => 'admin@email.com',
            'phone_number' => '0333-3333333',
            'cnic' => '3333-3333333-3',
            'role_id' => 1,
            'password' => bcrypt('password'),
            'status' => 1
        ]);

    }
}



