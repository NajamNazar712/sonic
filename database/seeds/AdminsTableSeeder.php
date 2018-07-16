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
            'username' => 'admin',
            'department' => 'Admin Department',
            'city_id'=>202,
            'password' => bcrypt('password')
        ]);

    }
}



