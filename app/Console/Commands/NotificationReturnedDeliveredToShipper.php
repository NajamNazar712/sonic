<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteShipment;
use App\Http\Models\Shipment;
use App\Http\Models\Shipper\User;
use App\ReturnDeliveredToShipperSms;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



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
        $from =  Carbon::now()->startOfDay()->toDateTimeString();
        $to = Carbon::parse($from)->endOfDay()->toDateTimeString();

        $return_deliverd_to_shippers = ReturnDeliveredToShipperSms::join('return_notes as rn','rn.id','=','return_delivered_to_shipper_sms.return_note_id')
            ->join('shipments', 'shipments.id', '=', 'return_delivered_to_shipper_sms.shipment_id')
            ->join('users as u', 'u.id', '=', 'return_delivered_to_shipper_sms.user_id')
            ->where('return_delivered_to_shipper_sms.status', 0)
            ->whereBetween('return_delivered_to_shipper_sms.created_at', [$from, $to])
            ->select('return_delivered_to_shipper_sms.return_note_id as return_id',
                'return_delivered_to_shipper_sms.user_id as user_id','u.phone as phone_number',
                DB::raw("(select count(return_note_id)
            from return_delivered_to_shipper_sms
            where status = 0
            and  created_at >= '$from'
            and  created_at <= '$to'
            and return_note_id = rn.id) as shipment_count"))
            ->groupBy('return_delivered_to_shipper_sms.return_note_id')
            ->get();

        NotificationsController::send(216, $return_deliverd_to_shippers);

        ReturnDeliveredToShipperSms::where('status',0)->whereBetween('created_at', [$from, $to])->update(['status' => 1]);

    }
}
