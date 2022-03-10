<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Shipment;
use App\Http\Models\Shipper\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ShortOfBusinessShippers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipper:short_of_business';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Short of business shippers last three days';

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
        $to = Carbon::now()->format("Y-m-d");
        if(Carbon::today()->format('D') == 'Sun' || Carbon::yesterday()->format('D') != 'Sun' || Carbon::now()->subDays(3)->format('D') != 'Sun'){
            $from = Carbon::now()->subDays(4)->format("Y-m-d");
        } else{
            $from = Carbon::now()->subDays(3)->format("Y-m-d");
        }
        $booked_shippers = Shipment::whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])->distinct('user_id')->pluck('user_id')->toArray();
        $other_shippers = User::join('sale_person_tags as st', 'st.user_id', '=', 'users.id')
            ->whereNotIn('users.id', $booked_shippers)->where('users.status', 3)->where('st.status', 0)
            ->select('users.id as shipper_id', 'st.admin_id as sale_person_id');
        if($other_shippers->exists()){
            $other_shippers = $other_shippers->get();
            $data = [];
            foreach ($other_shippers as $other_shipper){
                $data[$other_shipper->sale_person_id][] = $other_shipper->shipper_id;
            }
            foreach ($data as $key => $row){
                NotificationsController::send(170, $key, $row);
                NotificationsController::app_notification(16,$key,1,$key,$row);
            }
        }
    }
}
