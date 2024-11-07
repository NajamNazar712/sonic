<?php

use App\Http\Models\Admin\Admin;
use App\Http\Models\Shipper\User;
use Illuminate\Database\Seeder;

class UpdatePhoneNumbersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        foreach($users as $user)
        {
            if(substr($user->phone, 0, 1)!='0')
            {
                $phone = "0".$user->phone;
                if(substr($phone, 4, 1)==" ")
                {
                    $new_phone = substr_replace($phone, "-", 4, 1);
//                    echo $user->id."||".$new_phone."\n";
                    User::where('id',$user->id)->update(['phone'=>$new_phone]);
                }
                elseif(substr($phone, 4, 0)!="-")
                {
                    $new_phone = substr_replace($phone, "-", 4, 0);
//                    echo $user->id."||".$new_phone."\n";
                    User::where('id',$user->id)->update(['phone'=>$new_phone]);
                }

            }
            elseif(substr($user->phone, 0, 2)=='92')
            {
                $phone = "0".ltrim($user->phone,'92');
                if(substr($phone, 4, 1)==" ")
                {
                    $new_phone = substr_replace($phone, "-", 4, 1);
                    //echo $user->id."||".$new_phone."\n";
                    User::where('id',$user->id)->update(['phone'=>$new_phone]);
                }
                elseif(substr($phone, 4, 0)!="-")
                {
                    $new_phone = substr_replace($phone, "-", 4, 0);
//                    echo $user->id."||".$new_phone."\n";
                    User::where('id',$user->id)->update(['phone'=>$new_phone]);
                }
                //User::where('id',$user->id)->update(['phone'=>$phone]);
            }
            elseif(substr($user->phone, 0, 1)=='0')
            {
                $phone = $user->phone;
                if(substr($phone, 4, 1)==" ")
                {
                    $new_phone = substr_replace($phone, "-", 4, 1);
//                    echo $user->id."||".$new_phone."\n";
                    User::where('id',$user->id)->update(['phone'=>$new_phone]);
                }
                elseif(substr($phone, 4, 1)!="-")
                {
                    $new_phone = substr_replace($phone, "-", 4, 0);
//                    echo $user->id."||".$new_phone."\n";
                    User::where('id',$user->id)->update(['phone'=>$new_phone]);
                }


                //User::where('id',$user->id)->update(['phone'=>$phone]);
            }







            //Phone 2

            if(substr($user->phone2, 0, 1)!='0' && $user->phone2!="")
            {
                $phone = "0".$user->phone2;
                if(substr($phone, 4, 1)==" ")
                {
                    $new_phone = substr_replace($phone, "-", 4, 1);
//                    echo $user->id."||".$new_phone."\n";
                    User::where('id',$user->id)->update(['phone2'=>$new_phone]);
                }
                elseif(substr($phone, 4, 0)!="-")
                {
                    $new_phone = substr_replace($phone, "-", 4, 0);
//                    echo $user->id."||".$new_phone."\n";
                    User::where('id',$user->id)->update(['phone2'=>$new_phone]);
                }

            }
            elseif(substr($user->phone2, 0, 2)=='92'&& $user->phone2!="")
            {
                $phone = "0".ltrim($user->phone2,'92');
                if(substr($phone, 4, 1)==" ")
                {
                    $new_phone = substr_replace($phone, "-", 4, 1);
                    //echo $user->id."||".$new_phone."\n";
                    User::where('id',$user->id)->update(['phone2'=>$new_phone]);
                }
                elseif(substr($phone, 4, 0)!="-")
                {
                    $new_phone = substr_replace($phone, "-", 4, 0);
//                    echo $user->id."||".$new_phone."\n";
                    User::where('id',$user->id)->update(['phone2'=>$new_phone]);
                }
                //User::where('id',$user->id)->update(['phone'=>$phone]);
            }
            elseif(substr($user->phone2, 0, 1)=='0' && $user->phone2!="")
            {
                $phone = $user->phone2;
                if(substr($phone, 4, 1)==" ")
                {
                    $new_phone = substr_replace($phone, "-", 4, 1);
//                    echo $user->id."||".$new_phone."\n";
                    User::where('id',$user->id)->update(['phone2'=>$new_phone]);
                }
                elseif(substr($phone, 4, 1)!="-")
                {
                    $new_phone = substr_replace($phone, "-", 4, 0);
//                    echo $user->id."||".$new_phone."\n";
                    User::where('id',$user->id)->update(['phone2'=>$new_phone]);
                }


                //User::where('id',$user->id)->update(['phone'=>$phone]);
            }


        }


        User::where('id',309)->update(['phone'=>'0333-4561777']);


        User::where('id',1016)->update(['phone2'=>'0333-3280984']);
        User::where('id',962)->update(['phone2'=>'0213-5344667']);
        User::where('id',959)->update(['phone2'=>'0512-875304']);
        User::where('id',925)->update(['phone2'=>'0336-2095930']);
        User::where('id',856)->update(['phone2'=>'0213-2601415-6']);
        User::where('id',851)->update(['phone2'=>'0323-1494746']);
        User::where('id',567)->update(['phone2'=>'0334-3242602']);
        User::where('id',479)->update(['phone2'=>'0300-2344710']);
        User::where('id',454)->update(['phone2'=>'0423-5756003']);
        User::where('id',399)->update(['phone2'=>'0333-2487746']);
        User::where('id',366)->update(['phone2'=>null]);
        User::where('id',309)->update(['phone2'=>'0324-4561777']);
        User::where('id',287)->update(['phone2'=>'0213-5869953']);
        User::where('id',282)->update(['phone2'=>'0423-7663175']);
        User::where('id',245)->update(['phone2'=>'0213-7238238']);
        User::where('id',243)->update(['phone2'=>'0316-2674589']);
        User::where('id',231)->update(['phone2'=>null]);
        User::where('id',174)->update(['phone2'=>'0213-4543434']);
        User::where('id',163)->update(['phone2'=>'0213-4520183-84-85']);
        User::where('id',136)->update(['phone2'=>'0311-1743677']);





        //For admins

        $admins = Admin::all();
        foreach($admins as $admin)
        {

            if(substr($admin->phone_number, 0, 1)!='0')
            {
                $phone = "0".$admin->phone_number;
                if(substr($phone, 4, 1)==" ")
                {
                    $new_phone = substr_replace($phone, "-", 4, 1);
//                    echo $user->id."||".$new_phone."\n";
                    Admin::where('id',$admin->id)->update(['phone_number'=>$new_phone]);
                }
                elseif(substr($phone, 4, 0)!="-")
                {
                    $new_phone = substr_replace($phone, "-", 4, 0);
//                    echo $user->id."||".$new_phone."\n";
                    Admin::where('id',$admin->id)->update(['phone_number'=>$new_phone]);
                }

            }
            elseif(substr($admin->phone_number, 0, 2)=='92')
            {
                $phone = "0".ltrim($admin->phone_number,'92');
                if(substr($phone, 4, 1)==" ")
                {
                    $new_phone = substr_replace($phone, "-", 4, 1);
                    //echo $user->id."||".$new_phone."\n";
                    Admin::where('id',$admin->id)->update(['phone_number'=>$new_phone]);
                }
                elseif(substr($phone, 4, 0)!="-")
                {
                    $new_phone = substr_replace($phone, "-", 4, 0);
//                    echo $user->id."||".$new_phone."\n";
                    Admin::where('id',$admin->id)->update(['phone_number'=>$new_phone]);
                }
                //User::where('id',$user->id)->update(['phone'=>$phone]);
            }
            elseif(substr($admin->phone_number, 0, 1)=='0')
            {
                $phone = $admin->phone_number;
                if(substr($phone, 4, 1)==" ")
                {
                    $new_phone = substr_replace($phone, "-", 4, 1);
//                    echo $user->id."||".$new_phone."\n";
                    Admin::where('id',$admin->id)->update(['phone_number'=>$new_phone]);
                }
                elseif(substr($phone, 4, 1)!="-")
                {
                    $new_phone = substr_replace($phone, "-", 4, 0);
//                    echo $user->id."||".$new_phone."\n";
                    Admin::where('id',$admin->id)->update(['phone_number'=>$new_phone]);
                }


                //User::where('id',$user->id)->update(['phone'=>$phone]);
            }



        }



    }
}
