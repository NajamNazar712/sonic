<?php

use App\Http\Models\Admin\ActivityTrailAction;
use App\Http\Models\Admin\ModulePermission;
use App\Http\Models\Admin\AdminsScreenList;
use Illuminate\Database\Seeder;

class UpdateReturnDeliveredNotificationSettingNameToSmsNotificationsLimit extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $module_permission = ModulePermission::where('id', 861);
        if($module_permission->exists()){
            $module_permission = $module_permission->first();
            $module_permission->name = 'SMS Notifications Limit - View';
            $module_permission->save();
        }

        $activity_trail = ActivityTrailAction::where('id', 655);
        if($activity_trail->exists()){
            $activity_trail = $activity_trail->first();
            $activity_trail->screen_name = 'SMS Notifications Limit';
            $activity_trail->save();
        }

        $admin_screen_list = AdminsScreenList::where('name', '=', 'Setting > Shipper > SMS Notification Return Deliver To Shipper');
        if($admin_screen_list->exists()){
            $admin_screen_list = $admin_screen_list->first();
            $admin_screen_list->name = 'Setting > Shipper > SMS Notifications Limit';
            $admin_screen_list->url = 'admin.settings.sms_notifications_limit.index';
            $admin_screen_list->save();
        }
    }
}
