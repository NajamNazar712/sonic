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
            'name' => 'Atif Sami',
            'email' => 'atif.sami@iblgrp.com',
            'phone_number' => '0302-8283918',
            'cnic' => '33333-3333333-3',
            'role_id' => 1,
            'password' => '$2y$10$XW1bVUtw5BhyvMU/rEtit.6yugg5jewx1q3Z/RQMo5xFOxaAJrlkq',
            'status' => 1
        ]);

        Admin::create([
            'name' => 'Qasim Naseer',
            'email' => 'qasim.naseer@iblgrp.com',
            'phone_number' => '0301-8264520',
            'cnic' => '33333-3333333-3',
            'role_id' => 1,
            'password' => '$2y$10$aIwkwy077ybevKbByjk6U.Sd0o80ozeAIZEAvdrv87B7un64jV4wO',
            'status' => 1
        ]);

        Admin::create([
            'name' => 'Muhammad Yousuf Fazal',
            'email' => 'yousuf.fazal@iblgrp.com',
            'phone_number' => '0302-8233528',
            'cnic' => '33333-3333333-3',
            'role_id' => 1,
            'password' => '$2y$10$RVjkL1loCo81tbqrMChC/.Xz.iuItAOwlNEhNAI61VMjqhguQg03i',
            'status' => 1
        ]);

        Admin::create([
            'name' => 'Faisal Hasan',
            'email' => 'faisal.hasan@trax.pk',
            'phone_number' => '0342-2175251',
            'cnic' => '33333-3333333-3',
            'role_id' => 1,
            'password' => '$2y$10$kUdHXIiucSiJVwC/SpYoQOqZK6J2exTlhVRmdgTdT.oKRMDvcsod6',
            'status' => 1
        ]);

        Admin::create([
            'name' => 'Danish Zahid',
            'email' => 'danish.zahid@trax.pk',
            'phone_number' => '0347-2400094',
            'cnic' => '33333-3333333-3',
            'role_id' => 1,
            'password' => '$2y$10$Lw15KyJgm4mF02BM2CZzhOrTer4wZN75mfPNtnQSnn7Iqne7VH1q6',
            'status' => 1
        ]);

        Admin::create([
            'name' => 'Muhammad Waqas',
            'email' => 'muhammad.waqas@trax.pk',
            'phone_number' => '0345-2560242',
            'cnic' => '33333-3333333-3',
            'role_id' => 1,
            'password' => '$2y$10$MMmJp48pH52xjBTlnW0WEeCCupZXCyeNHn/onZXPT1oidEKq4A7bS',
            'status' => 1
        ]);

        Admin::create([
            'name' => 'Syed Salman Ali Jafri',
            'email' => 'syed.salman@trax.pk',
            'phone_number' => '0334-2094542',
            'cnic' => '33333-3333333-3',
            'role_id' => 1,
            'password' => '$2y$10$/zSCwtaR/YsW.UCa8qmGROAA/ok5gxHAB5C4aIgI9JeCkg1g0Dc.W',
            'status' => 1
        ]);

        Admin::create([
            'name' => 'Muhammad Hassan Khan',
            'email' => 'hassan@trax.pk',
            'phone_number' => '0334-3169511',
            'cnic' => '33333-3333333-3',
            'role_id' => 1,
            'password' => '$2y$10$PB19ewEQH1tWhJ8Zwn2PO.SHzas3gMMMLvffwMe0aaCw88HtS5Al.',
            'status' => 1
        ]);
    }
}



