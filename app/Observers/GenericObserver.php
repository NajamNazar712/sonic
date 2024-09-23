<?php

namespace App\Observers;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminRole;
use Auth;
use Log;
use App\Http\Models\Admin\UserRoleManagementLog;
use App\Http\Models\HR\EmployeeDesignation;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\EmployeeShift;
use App\Http\Models\City;


class GenericObserver
{
    public function saved($model)
    {
        $dirtyAttributes = $model->getDirty();
        $changes = [];
        // Iterate over each dirty attribute
        foreach ($dirtyAttributes as $attribute => $newValue) {
            // Get the original value of the attribute
            if($attribute != 'updated_at' && $attribute != 'updated_by') {
                $originalValue = $model->getOriginal($attribute);
                if($attribute == 'role_id') {
                    $originalValue = AdminRole::where('id', $originalValue)->value('name');

                }elseif($attribute == 'designation_id') {
                    $originalValue = EmployeeDesignation::where('id', $originalValue)->value('name');

                }elseif($attribute == 'shift_id') {
                    $originalValue = EmployeeShift::where('id', $originalValue)->value('name');

                }elseif($attribute == 'department_id') {
                    $originalValue = AdminDepartment::where('id', $originalValue)->value('name');

                }elseif($attribute == 'status') {
                    $originalValue = ($originalValue == 1) ? 'Enable' : 'Disable';

                }elseif($attribute == 'is_active') {
                    $originalValue = ($originalValue == 1) ? 'Enable' : 'Disable';
                    
                }elseif($attribute == 'default_hub_id') {
                    $originalValue = City::where('id', $originalValue)->value('name');
                    
                }
                $changes[$attribute] = $originalValue;
            }
        }
        if(!empty($changes)) {

            if($model instanceof Admin) {
                $screen = 'User Management';
            }else if( $model instanceof AdminRole) {
                $screen = 'Role Management';
            }
            if(auth()->check()) {
                $record = new UserRoleManagementLog;
                $record->changed_by_id = auth()->user()->id;
                $record->data = json_encode($changes);
                $record->changed_in_record_id = $model->id;
                $record->screen_name = $screen;
                $record->save();
            }
        }
        
    }
}   
