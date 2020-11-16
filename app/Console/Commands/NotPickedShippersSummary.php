<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\V2Pickup\V2PickupRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotPickedShippersSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:notpickedshipperssummary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email for not picked shippers summary';

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
        $date = Carbon::yesterday()->toDateString();
        $date_from = $date . ' 08:00:00';
        $next_day = Carbon::parse($date)->addDay(1);
        $date_to = $next_day->toDateString();
        $date_to = $date_to . ' 07:59:59';

        $pickup_requests = V2PickupRequest::join('users as u', 'v2_pickup_requests.shipper_id', '=', 'u.id')
            ->join('v2_pickup_request_statuses as prs', 'prs.id', '=', 'v2_pickup_requests.status_id')
            ->join('v2_pickup_request_attempts as vpra','vpra.pickup_request_id','=','v2_pickup_requests.id')
            ->join('v2_pickup_request_not_pick_reasons as npr','npr.id','=','vpra.reason_id')
            ->join('sale_person_tags as spt', function ($join) {
                $join->on('spt.user_id', '=', 'u.id')
                    ->where('spt.status','=',0);
            })
            ->join('admins as a','a.id','=','spt.admin_id')
            ->select('v2_pickup_requests.id as id', 'v2_pickup_requests.created_at as requested_date', 'u.name as shipper_name','npr.name as reason','a.id as admin_id','a.name as sales_person','a.email as saleperson_email')
            ->where('v2_pickup_requests.status_id', 3)
            ->where('st.status',0)
            ->whereNotNull('vpra.reason_id')
            ->wherebetween('v2_pickup_requests.created_at',[$date_from,$date_to])
            ->get();
            if (count($pickup_requests) > 0) {
                $pickup_data = array();

                foreach ($pickup_requests as $index => $pickup) {
                    $pickup_data[$pickup->admin_id][] = $pickup;
                }
                foreach ($pickup_data as $admin_id => $data) {
                    NotificationsController::send(99, $data);
                }
            }
        NotificationsController::send(100, null);
    }
}
