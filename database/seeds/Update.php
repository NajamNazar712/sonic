<?php

use Illuminate\Database\Seeder;

class Update extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $substitute_users = \App\Http\Models\Shipper\SubstituteUser::get();
        foreach ($substitute_users as $substitute_user){
            $module_permission = new \App\Http\Models\Shipper\SubstituteUserPermission();
            $module_permission->substitute_user_id = $substitute_user->id;
            $module_permission->permission_id = 16;
            $module_permission->save();
            $module_permission = new \App\Http\Models\Shipper\SubstituteUserPermission();
            $module_permission->substitute_user_id = $substitute_user->id;
            $module_permission->permission_id = 17;
            $module_permission->save();
            $module_permission = new \App\Http\Models\Shipper\SubstituteUserPermission();
            $module_permission->substitute_user_id = $substitute_user->id;
            $module_permission->permission_id = 18;
            $module_permission->save();
        }
    }
}
