<?php

use Illuminate\Database\Seeder;

class UpdatePermissionSeederForEmployeeArea extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(
            
            array(
                array('id' => 842, 'name' => 'City Sub Area - Add', 'module_id' => 12),
                array('id' => 843, 'name' => 'City Sub Area - Edit', 'module_id' => 12),
                array('id' => 844, 'name' => 'City Sub Area - Status/Default', 'module_id' => 12),
                array('id' => 845, 'name' => 'Update Area(s) of Hub', 'module_id' => 28),
                array('id' => 850, 'name' => 'City Sub Area - VIEW', 'module_id' => 12),
            )
        );

        DB::table('activity_trail_actions')->insert(
            array(
                array('id' => 642, 'screen_name' => 'City Sub Area', 'action' => 'View'),
                array('id' => 643, 'screen_name' => 'City Sub Area', 'action' => 'Excel Download'),
                array('id' => 644, 'screen_name' => 'Update Area(s) of Hub', 'action' => 'View'),
            )
        );

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
            array(
                array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Human Resource > Assign Employee Area', 'url' => 'admin.human_resource.employee_areas.index', 'permission_id' => 845),
            )
        );


    }
}
