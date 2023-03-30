<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\HR\Employee;
use App\Http\Models\Rider;
use App\Http\Models\V2Pickup\V2PickupNote;
use App\Http\Models\V2Pickup\V2RiderPickup;
use App\Http\Models\WMS\WmsPickupRun;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RiderDeactivateAutomatically extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:RiderDeactivateAutomaticallyAndGenerateEmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rider Deactivate Automatically After Week which are not active';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $settings = GlobalSettings::where('type', 'rider_deactivation_cron_status');
        if($settings->exists()){
            $settings = $settings->first();
            if($settings->setting_value == 1){
                $days = 3;
                $cron_days = GlobalSettings::where('type', 'rider_deactivation_cron_days');
                if($cron_days->exists()){
                    $cron_days = $cron_days->first();
                    $days = $cron_days->setting_value;
                }
                $date_week_age = Carbon::now()->subDays($days)->toDateTimeString();
                $today = Carbon::now()->toDateTimeString();
                $rider_ids = Rider::where('status', 1)->whereDate('created_at', '<', $date_week_age)->pluck('id')->toArray();
                $deliveries  = DeliveryNote::whereBetween('created_at', [$date_week_age, $today])->pluck('rider_id')->toArray();
                $v2_pickups  = V2PickupNote::whereBetween('created_at', [$date_week_age, $today])->pluck('rider_id')->toArray();
                $return_notes  = ReturnNote::whereBetween('created_at', [$date_week_age, $today])->pluck('rider_id')->toArray();
                $wms_pickup_run  = WmsPickupRun::whereBetween('created_at', [$date_week_age, $today])->pluck('wms_rider_id')->toArray();
                $rider_attendance = EmployeeAttendance::where('employee_type', 2)->whereBetween('attendance_date', [$date_week_age, $today])->whereNotNull('clock_in_datetime')->pluck('employee_id')->toArray();
                $rider_attendance = Rider::whereIn('employee_id',$rider_attendance)->pluck('id')->toArray();
                $rider_active = array_unique(array_merge($deliveries, $v2_pickups, $return_notes, $wms_pickup_run, $rider_attendance));
                $data = array_diff($rider_ids, $rider_active);
                if ($data != null){
                    $riders = Rider::wherein("id", $data)->get();
                    foreach ($riders as $datum) {
                        $data_set = Rider::find($datum->id);
                        $data_set->status = 0;
                        $data_set->save();
                        $staff = Admin::where('trax_id', $data_set->trax_id);
                        if(!$staff->exists()){
                            $employee_directory = Employee::where('trax_id', $data_set->trax_id);
                            if ($employee_directory->exists()) {
                                $employee_directory = $employee_directory->first();
                                $employee_directory->status_id = 2;

                                $riders = Rider::leftJoin('delivery_notes', function ($join) {
                                    $join->on('delivery_notes.rider_id', '=', 'riders.id')
                                        ->where('delivery_notes.created_at', '=',
                                            DB::raw('(select max(created_at) from delivery_notes where delivery_notes.rider_id = riders.id)'));
                                })
                                ->leftJoin('pickup_notes', function ($join) {
                                    $join->on('pickup_notes.rider_id', '=', 'riders.id')
                                        ->where('pickup_notes.created_at', '=',
                                            DB::raw('(select max(created_at) from pickup_notes where pickup_notes.rider_id = riders.id)'));
                                })
                                ->leftJoin('return_notes', function ($join) {
                                    $join->on('return_notes.rider_id', '=', 'riders.id')
                                        ->where('return_notes.created_at', '=',
                                            DB::raw('(select max(created_at) from return_notes where return_notes.rider_id = riders.id)'));
                                })
                                ->select('riders.name','riders.id','delivery_notes.created_at as delivery_notes_created_at','pickup_notes.created_at as pickup_notes_created_at','return_notes.created_at as return_notes_created_at')
                                ->where('riders.id',1)
                                ->get();
                                $pickup_date = Carbon::createFromFormat('Y-m-d H:i:s', $riders->first()->pickup_notes_created_at)->format('Y-m-d');
                                $delivery_date = Carbon::createFromFormat('Y-m-d H:i:s', $riders->first()->delivery_notes_created_at)->format('Y-m-d');
                                $return_date = Carbon::createFromFormat('Y-m-d H:i:s', $riders->first()->return_notes_created_at)->format('Y-m-d');
                                $max_date = max($pickup_date,$delivery_date,$return_date);

                                $employee_directory->last_working_date = $max_date;

                                $employee_directory->save();
                            }
                        }
                    }
                    NotificationsController::send(155, $data);
                }
            }
        }
    }
}
