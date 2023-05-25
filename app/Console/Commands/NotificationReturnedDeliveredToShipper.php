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
//        dd($from,$to);

//         $users = GlobalSettings::where('type', 'returned_shipment_notification');

//         if($users->exists()) {
//             $users = $users->first();
//             $current_users = $users->text;
//             $c_user = explode(',', $current_users);
//             $return_notes = ReturnNote::join('return_note_shipments as rnsh', 'rnsh.return_note_id', '=', 'return_notes.id')
//                 ->join('shipments', 'shipments.id', '=', 'rnsh.shipment_id')
//                 ->join('users as u', 'u.id', '=', 'shipments.user_id')
//                 ->select('return_notes.id as return_id', 'rnsh.shipment_id', 'shipments.user_id', 'u.phone as phone_number', 'return_notes.shipments_count as total_shipments', 'u.email as email')
//                 ->groupBy('return_notes.id')
//                 ->whereIn('shipments.user_id', $c_user)
//                 ->whereBetween('return_notes.updated_at', [$from, $to])

// //            ->pluck('return_id')->toArray();
//                 ->get();//

//             ;
//             NotificationsController::send(216, $return_notes);

//         }

        $return_deliverd_to_shippers = ReturnDeliveredToShipperSms::join('users as u', 'u.id', '=', 'return_delivered_to_shipper_sms.user_id')
         ->join('shipments', 'shipments.id', '=', 'return_delivered_to_shipper_sms.shipment_id')
         ->select('return_delivered_to_shipper_sms.return_note_id as return_id', 'shipments.user_id', 'u.phone as phone_number',DB::raw('(select count(id) from return_delivered_to_shipper_sms where user_id = return_delivered_to_shipper_sms.user_id and return_note_id = return_delivered_to_shipper_sms.return_note_id  and status = 0) as shipment_count'))
         ->whereBetween('return_delivered_to_shipper_sms.created_at', [$from, $to])
         ->groupBy('return_delivered_to_shipper_sms.return_note_id')
        ->get();

        NotificationsController::send(216, $return_deliverd_to_shippers);

        ReturnDeliveredToShipperSms::where('status',0)->whereBetween('created_at', [$from, $to])->update(['status' => 1]);

    }
}
