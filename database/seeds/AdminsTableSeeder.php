<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\Admin;

use Carbon\Carbon;

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
        DB::table('admin_hubs')->truncate();

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

        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        $ids = array(9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74);
        $names = array('Hammad Saleem','David Munir','Wajid Siddiqui','Fawad Ahmed','Faizan Kalam','Mohammad Shafique','Shehryar Majid','Moeen Shahadat','Imtiaz Shah','ali imran','Shoaib Ahmed','Mohammad Asif','Usman Bukhari','Jahanzaib Jahanzaib','Hafeez Shah','Khadim Hussain','Salah  Uddin','Sajjad Sajjad','Shahid Shahid','Junaid Khan','Naveed Naveed','Aamir Shahzad','Mohammad Farooq','Waqas Ahmed Dar','Qurban Ujjan','Abdul Khaliq','Asif Hameed','Syed  Sharique Ali','Shahzeb KHan','Ammar Mir','Hasnain Shah','Faisalabad Operations','DBF RWP/ISB','Annus Siddiqui','Moiz Khan','Umer Abid','DBF lahore','Rana  Imran','Qaseem Haider','Saifullah Shakirani','Shafay Tariq','Ramish Azeem','Shafqat Maik','Iftikhar Hussain','Muhammad Shahriyar','Akash Ali','Uzair Anees','Ali Mughal','Noman Aziz','Noman Alam','Hamza Mujeeb','Talha Motiwala','Rateesh Kumar','Umair Khan','Mudasir Ali','Muhammad Usman','Usman Iqbal','Adeel Ali','Sabir Ali','Noman Ahmed','Rahat Ali','Mohsin Ali','Faisal Mehboob','Ali Nawaz','Muhammad Afzaal','Junaid Suleman');
        $phone = array('3222862584','3491305920','3232323905','3322149092','3142777206','3002527922','3452350023','3335344991','3333472740','3171251963','3344400221','3171251980','3162567213','3171251979','3171251982','3171251966','3162565966','3162566040','3162566109','3162567191','3162567071','3171251962','3171251981','3453176923','3337162311','3352559332','3453150041','3330219834','3368294271','3345817490','3335814330','3111555065','3111555065','3111555065','3111555065','3111555065','3111555065','3076326260','3323124828','3111555065','3111555065','3111555065','3111555065','3111555065','3111555065','3111555065','3111555065','3111555065','3111555065','3111555065','3111555065','3111555065','3111555065','3111555065','3111555065','3111555065','3111555065','3111555065','3041111232','3111555065','3111555065','3111555065','3111555065','3111555065','3111555065','3323124828');
        $cnic = '4211110009990';
        $emails = array('hammad.saleem@trax.pk','david.munir@trax.pk','wajid.siddiqui@trax.pk','fawad.ahmed@trax.pk','faizan.kalam@trax.pk','mohammad.shafique@trax.pk','shehryar.majid@trax.pk','moeen.shahadat@trax.pk','imtiaz.shah@trax.pk','ops.bhv@trax.pk','ops.guj@trax.pk','ops.hdd@trax.pk','usman.bukhari@trax.pk','tabasumawan@gmail.com','hafeezalishah83@gmail.com','ops.uet@trax.pk','salahudinghulammohudin@gmail.com ','mohammadsajjadqureshi@gmail.com ','kamratnt@gmail.com ','ops.pew@trax.pk','naveedshah3843436@gmail.com','ops.swl@trax.pk','ops.sgd@trax.pk','waqas.dar@trax.pk','ops.skz@trax.pk','abdul.khaliq@trax.pk','asif.hameed@trax.pk','syed.sharique@trax.pk','shahzeb.khan@trax.pk','ammar.mir@trax.pk','ops.jlm@trax.pk','ops.fsd@trax.pk','dwp.rwp@trax.pk','annus.siddiqui@trax.pk','moiz.khan@trax.pk','umer.abid@trax.pk','dbf.lhe@trax.pk','rana.imran@trax.pk','qaseem.haider@trax.pk','saifullah.shakirani@trax.pk','shafay.tariq@trax.pk','ramish.azeem@trax.pk','shafqat.malik@trax.pk','iftikhar.hussain@trax.pk','shahriyar@trax.pk','akash.ali@trax.pk','uzair.anees@trax.pk','ali.mughal@trax.pk','noman.aziz@trax.pk','noman.alam@trax.pk','hamza.mujeeb@trax.pk','talha.motiwala','rateesh.kumar','umair.khan@trax.pk','mudasir.ali@trax.pk','ops.isb@trax.pk','usman.iqbal@trax.pk','adeel.ali@trax.pk','sabir.ali@trax.pk','noman.ahmed@trax.pk','rahat.ali@trax.pk','mohsin.ali@trax.pk','faisal.mehboob@trax.pk','ali.nawaz@trax.pk','muhammad.afzaal@trax.pk','junaid.suleman@trax.pk');
        $roles = array(15,13,16,2,14,3,10,9,8,10,10,10,9,10,10,10,10,10,10,10,10,10,10,4,10,14,11,15,13,13,10,11,13,13,13,10,13,8,12,13,14,11,11,11,11,10,13,10,6,13,13,14,13,13,11,11,10,16,10,11,2,3,9,11,10,16);
        $password = array('6mY4dOtp','kO6UWSet','MMKLhVo7','btWRMA1b','IBFmDdz6','yBbkJwGu','xNlhAGtp','GpAHKT2x','rxbe7tnl','eOgfwArx','zDsElYhz','1sl7mkQO','LPnG66jl','kvdPBQfq','X8vgPi6g','9ksv5pXz','5MPZcs2J','ZoLiPJnl','SXQxlmLT','EaGfpi5p','tJjM0mLT','8N24IW82','mdUlOx9u','hAMlTtUU','B1DGcbPb','wG5HZ4Cr','UfnqlqCj','rDlE3kXs','Q7O6t0AM','Jpa7RZzo','jYxg7hK9','OXFJEaNK','oZgr6Bze','29E3Y13a','fH66c26y','5MDVakh4','tWOsJHW0','NTIQSlE3','fJO0WK28','jKwePQNc','rVLvcRmy','nAoVLDWU','kiZyjR2G','iydOC3i5','dpCqvayP','tI8Dtdso','j22yzadc','tCkOHtY5','07DHmDRB','QoZBQZoR','SyhnhIJo','hUnSyNHm','QGb47Ey7','MSeY95O6','iFhW1sMo','o1gSKlhs','1EJk6WvB','SBHkkOgU','8lorNO2w','qGaWMJ0q','YbCqF1FQ','WrIvz34X','o7BT5s24','w0yGNcK0','vHDZvd53','U7gZt1aX');
        $status = 1;


        for($i=0;$i<count($ids);$i++) {

            DB::table('admins')->insert(array(
                array('id' => $ids[$i], 'name' => $names[$i], 'email' => $emails[$i], 'phone_number' => $phone[$i], 'cnic' => $cnic, 'role_id' => $roles[$i], 'password' => bcrypt($password[$i]), 'status' => $status,'created_at'=>$timestamp,'updated_at'=>$timestamp),
            ));
        }

        $all_hubs = array(101,106,109,110,111,119,122,125,128,130,131,134,135,144,158,165,172,174,176,186,465,199,202,223,414,237,238,244,243,251,255,257,264,267,270,271,283,281,284,293,302,315,318,304,319,333,339,340);

        for($i=0;$i<count($ids);$i++)
        {


            if($ids[$i]==9 ||$ids[$i]==10 ||$ids[$i]==11 ||$ids[$i]==12 ||$ids[$i]==13 ||$ids[$i]==14 ||$ids[$i]==32 ||$ids[$i]==34 ||$ids[$i]==36 ||$ids[$i]==37 ||$ids[$i]==38 ||$ids[$i]==42 ||$ids[$i]==43 ||$ids[$i]==47 ||$ids[$i]==48 ||$ids[$i]==55 ||$ids[$i]==57 ||$ids[$i]==58 ||$ids[$i]==59 ||$ids[$i]==60 ||$ids[$i]==61 ||$ids[$i]==62 ||$ids[$i]==69 ||$ids[$i]==70)
            {

                for ($j = 0; $j < count($all_hubs); $j++) {

                    DB::table('admin_hubs')->insert(array(
                        array('admin_id' => $ids[$i], 'hub_id' => $all_hubs[$j]),
                    ));
                }
            }
        }

        $hubs = array(202,110,158,172,284,333,340,101,199,315,302,318,202,186,144,174,174,223,202,202,174,174,223,223,223,174,223,144,202,223,223,202);
        $ids = array(15,18,19,20,22,23,25,26,27,29,31,33,35,39,40,41,44,45,49,50,51,52,53,56,63,64,65,67,68,72,73,74);


        for($i=0;$i<count($ids);$i++) {

            DB::table('admin_hubs')->insert(array(
                array('admin_id' => $ids[$i], 'hub_id' => $hubs[$i]),
            ));
        }

        $hubs = array(101,111,122,125,165,174,176,186,465,199,414,237,238,244,255,264,271,304,319,340);
        $id = 16;

        for($i=0;$i<count($hubs);$i++) {

            DB::table('admin_hubs')->insert(array(
                array('admin_id' => $id, 'hub_id' => $hubs[$i]),
            ));
        }


        $hubs = array(131,172,202,243,257,270,283,281,318,333);
        $id = 17;

        for($i=0;$i<count($hubs);$i++) {

            DB::table('admin_hubs')->insert(array(
                array('admin_id' => $id, 'hub_id' => $hubs[$i]),
            ));
        }


        $hubs = array(106,109,110,251,134,135,293,119,128,130,284,267,339);
        $id = 21;

        for($i=0;$i<count($hubs);$i++) {

            DB::table('admin_hubs')->insert(array(
                array('admin_id' => $id, 'hub_id' => $hubs[$i]),
            ));
        }

        $hubs = array(283,281);
        $id = 24;

        for($i=0;$i<count($hubs);$i++) {

            DB::table('admin_hubs')->insert(array(
                array('admin_id' => $id, 'hub_id' => $hubs[$i]),
            ));
        }

        $hubs = array(271,176,304,319,264,238);
        $id = 28;

        for($i=0;$i<count($hubs);$i++) {

            DB::table('admin_hubs')->insert(array(
                array('admin_id' => $id, 'hub_id' => $hubs[$i]),
            ));
        }

        $hubs = array(293,339,106,109,128,130,119,267);
        $id = 30;

        for($i=0;$i<count($hubs);$i++) {

            DB::table('admin_hubs')->insert(array(
                array('admin_id' => $id, 'hub_id' => $hubs[$i]),
            ));
        }


        $hubs = array(223,144,158,302,315,106,109,110,251,134,135,293,119,128,130,284,267,339,101,111,122,125,165,174,176,186,465,199,414,237,238,244,255,264,271,304,319,340);
        $id = 46;

        for($i=0;$i<count($hubs);$i++) {

            DB::table('admin_hubs')->insert(array(
                array('admin_id' => $id, 'hub_id' => $hubs[$i]),
            ));
        }


        $hubs = array(101,237,165);
        $id = 54;

        for($i=0;$i<count($hubs);$i++) {

            DB::table('admin_hubs')->insert(array(
                array('admin_id' => $id, 'hub_id' => $hubs[$i]),
            ));
        }



        $hubs = array(223,144,158,302,315,106,109,110,251,134,135,293,119,128,130,284,267,339,101,111,122,125,165,174,176,186,465,199,414,237,238,244,255,264,271,304,319,340);
        $id = 66;

        for($i=0;$i<count($hubs);$i++) {

            DB::table('admin_hubs')->insert(array(
                array('admin_id' => $id, 'hub_id' => $hubs[$i]),
            ));
        }


        $hubs = array(223,144,158,302,315);
        $id = 71;

        for($i=0;$i<count($hubs);$i++) {

            DB::table('admin_hubs')->insert(array(
                array('admin_id' => $id, 'hub_id' => $hubs[$i]),
            ));
        }
    }
}



