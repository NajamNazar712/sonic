<?php

namespace App\Console\Commands;

use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingShipmentsForPayment;
use App\Http\Models\RetailPendingPayment;
use App\Http\Models\RetailPendingShipmentsForPayment;
use App\Http\Models\Shipment;
use Illuminate\Console\Command;
use DB;
class PendingPaymentShipmentsCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'count:pendingpaymentshipments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Calculate Pending Shipments for payment';

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
        DB::table('pending_shipments_for_payments')->truncate();
        $users = PendingPayment::pluck('user_id')->toArray();
        foreach ($users as $user_id){
            $shipment_count = Shipment::where('user_id', $user_id)->whereNotIn('shipper_status_id', [1, 14, 17, 20, 21, 22, 23, 24, 25, 30, 31, 36, 37, 38, 51])->count();
            $pending_payment_shipments = new PendingShipmentsForPayment();
            $pending_payment_shipments->user_id = $user_id;
            $pending_payment_shipments->pending_shipments_count = $shipment_count;
            $pending_payment_shipments->save();
        }
        $retail_users = RetailPendingPayment::pluck('user_id')->toArray();
        foreach ($retail_users as $retail_user_id){
            $retail_shipments = RetailShipment::where('shipper_account_no', $retail_user_id)->pluck('shipment_id')->toArray();
            $shipment_count = Shipment::whereIn('id', $retail_shipments)->whereNotIn('shipper_status_id', [1, 14, 17, 20, 21, 22, 23, 24, 25, 30, 31, 36, 37, 38, 51])->count();
            $pending_payment_shipments = new RetailPendingShipmentsForPayment();
            $pending_payment_shipments->user_id = $retail_user_id;
            $pending_payment_shipments->pending_shipments_count = $shipment_count;
            $pending_payment_shipments->save();
        }
    }
}
