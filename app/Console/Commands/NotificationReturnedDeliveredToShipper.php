<?php

namespace App\Console\Commands;

use App\Http\Models\Shipment;
use App\Http\Models\Shipper\User;
use Illuminate\Console\Command;
use Carbon\Carbon;



class NotificationReturnedDeliveredToShipper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:returned_delivered_sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $start_date = Carbon::now()->endOfDay()->subDay()->setTimezone('UTC');
        $end_date = Carbon::now()->endOfDay()->setTimezone('UTC');

        $start_date_formatted = $start_date->format('Y-m-d H:i:s.u');
        $end_date_formatted = $end_date->format('Y-m-d H:i:s.u');
      /*  dd($start_date_formatted,$end_date_formatted );*/

        $users = User::join('shipments as shipp', 'shipp.user_id', '=', 'users.id')
            ->join('shipments_journey as shaj', 'shaj.shipment_id', '=', 'shipp.id')
            ->where('users.status', 3)
            ->where('shaj.shipper_status_id', 25)
            ->whereBetween('shipp.created_at', [
                $start_date_formatted,
                $end_date_formatted
            ])
            ->get();

        dd($users);



        /*->select('hubs.name as hub_name', 'petty_cash_consignees.consignee_name as consignee_name', 'petty_cash_consignees.id as id')
        ->first();*/




    }
}
