<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteShipment;
use App\Http\Models\Shipment;
use App\Http\Models\Shipper\User;
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
        $from = Carbon::today()->subDay()->toDateTimeString();
        $to = Carbon::parse($from)->endOfDay()->toDateTimeString();

        $users = GlobalSettings::where('type', 'returned_shipment_notification')->first();
        $current_users = $users->text;

        $return_notes = ReturnNote::join('return_note_shipments as rnsh', 'rnsh.return_note_id', '=', 'return_notes.id')
            ->join('shipments', 'shipments.id', '=', 'rnsh.shipment_id')
            ->join('users as u', 'u.id', '=', 'shipments.user_id')
            ->select('rnsh.return_note_id as return_id', 'rnsh.shipment_id', 'shipments.user_id','u.phone as phone_number', DB::raw('COUNT(rnsh.shipment_id) as total_shipments'))
            ->groupBy('rnsh.return_note_id')
            ->whereIn('shipments.user_id', explode(',', $current_users))
            ->whereBetween('return_notes.updated_at', [$from, $to])
            ->get();
          NotificationsController::send(216,$return_notes);
    }
}
