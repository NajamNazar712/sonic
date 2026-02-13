<?php

namespace App\Http\Models;

use App\Http\Models\Admin\OperationRidersCategory;
use App\Http\Models\HR\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\RiderChangeLog;
use Illuminate\Support\Facades\DB;
class Rider extends Model
{
     // Format dates as 'YYYY-MM-DD HH:mm:ss'
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    protected $fillable = [
        'city_id', 'name', 'phone', 'cnic', 'address', 'route_id', 'rider_main_category_id', 'rider_category_id', 'status', 'pin', 'special_rider_checkbox', 'created_by', 'updated_by', 'trax_id', 'rider_type_id', 'operation_rider_id', 'employee_id', 'ccd', 'shift_id', 'reporting_location_id','incentive_amount','area_id' ,'allow_delivered_status'
    ];
    public function city(){
        return $this->belongsTo('App\Http\Models\City');
    }
    public function route(){
        return $this->belongsTo('App\Http\Models\Route');
    }
    public function rider_category(){
        return $this->belongsTo('App\Http\Models\RiderCategory');
    }
    public function deliverynotes(){
        return $this->hasMany('App\Http\Models\Admin\DeliveryNote');
    }
    public function open_parcel_rider(){
        return $this->hasOne('App\Http\Models\OpenParcelHistory');
    }
    public function employee(){
        return $this->belongsTo(Employee::class,'trax_id','trax_id');
    }

    public function area()
    {
        return $this->belongsTo('App\Http\Models\CityArea', 'area_id', 'id');
    }

    public function rider_operation_category()
    {
        return $this->belongsTo(OperationRidersCategory::class,'operation_rider_id','id');
    }

    protected static function booted()
    {
        static::updated(function (Rider $rider) {

            // fields to track (no pin/dummy_pin)
            $trackFields = [
                'city_id',
                'name',
                'phone',
                'cnic',
                'address',
                'trax_id',
                'incentive_amount',
                'area_id',
                'shift_id',
                'rider_category_id',
                'rider_main_category_id',
                'operation_rider_id',
                'allow_delivered_status',
                'special_rider',
                'ccd',
                'route_id',
                'status',
                'blacklist',
                'rider_type_id',
            ];

            //  user friendly labels (keys shown to users)
            $fieldLabels = [
                'city_id' => 'City',
                'name' => 'Rider Name',
                'phone' => 'Phone',
                'cnic' => 'CNIC',
                'address' => 'Address',
                'trax_id' => 'Trax ID',
                'incentive_amount' => 'Incentive Amount',
                'area_id' => 'Area',
                'shift_id' => 'Shift',
                'rider_category_id' => 'Rider Category',
                'rider_main_category_id' => 'Rider Main Category',
                'operation_rider_id' => 'Operation Rider',
                'allow_delivered_status' => 'Allow Delivered Status',
                'special_rider' => 'Special Rider',
                'ccd' => 'Credit Card Delivery',
                'route_id' => 'Route',
                'status' => 'Status',
                'blacklist' => 'Blacklisted',
                'rider_type_id' => 'Rider Type',
            ];

            // lookup for ID fields -> show name instead of id
            $lookup = [
                'city_id' => ['table' => 'cities', 'key' => 'id', 'label' => 'name'],
                'route_id' => ['table' => 'routes', 'key' => 'id', 'label' => 'start'], // or 'code'
                'area_id' => ['table' => 'city_areas', 'key' => 'id', 'label' => 'name'], // change label column if needed
                'shift_id' => ['table' => 'employee_shifts', 'key' => 'id', 'label' => 'name'],
                'rider_category_id' => ['table' => 'rider_categories', 'key' => 'id', 'label' => 'name'],
                'rider_main_category_id' => ['table' => 'rider_main_categories', 'key' => 'id', 'label' => 'name'],
                'operation_rider_id' => ['table' => 'operation_riders', 'key' => 'id', 'label' => 'name'],
            ];

            $labelOf = function (string $field, $value) use ($lookup) {
                if ($value === null || $value === '') return null;
                if (!isset($lookup[$field])) return $value;

                $row = $lookup[$field];
                $label = DB::table($row['table'])->where($row['key'], $value)->value($row['label']);
                return $label !== null ? $label : $value;
            };

            //  only changed fields in this update
            $dirty = $rider->getDirty();
            if (empty($dirty)) return;

            $changes = [];

            foreach ($trackFields as $field) {
                if (!array_key_exists($field, $dirty)) continue;

                $oldValue = $rider->getOriginal($field);
                $newValue = $rider->{$field};

                $key = $fieldLabels[$field] ?? $field;

                if (in_array($field, ['ccd', 'special_rider', 'allow_delivered_status', 'blacklist'])) {
                    $oldHuman = ($oldValue == 1 ? 'Yes' : 'No');
                    $newHuman = ($newValue == 1 ? 'Yes' : 'No');
                } elseif ($field === 'status') {
                    $oldHuman = ($oldValue == 1 ? 'Active' : 'Inactive');
                    $newHuman = ($newValue == 1 ? 'Active' : 'Inactive');
                } elseif ($field === 'rider_type_id') {
                    $oldHuman = ($oldValue == 1 ? 'Permanent' : 'Incentive');
                    $newHuman = ($newValue == 1 ? 'Permanent' : 'Incentive');
                } else {
                    $oldHuman = $labelOf($field, $oldValue);
                    $newHuman = $labelOf($field, $newValue);
                }

                $oldHuman = ($oldHuman === null || $oldHuman === '') ? '-' : $oldHuman;
                $newHuman = ($newHuman === null || $newHuman === '') ? '-' : $newHuman;

                $changes[$key] = ['old' => $oldHuman, 'new' => $newHuman];
            }

            if (empty($changes)) return;

            $route = request()->route();
            $routeName = $route ? $route->getName() : '';

            if (strpos($routeName, 'employee_directory') !== false) {
                $screen = 'Employee Directory';
            } elseif (strpos($routeName, 'riders') !== false) {
                $screen = 'Rider';
            } else {
                $screen = 'Rider';
            }

            RiderChangeLog::create([
                'rider_id'   => $rider->id,
                'changed_by' => Auth::id(),
                'changes'    => json_encode($changes),
                'screen'     => $screen,
            ]);
        });
    }
}
