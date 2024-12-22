<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Models\Invoice;
use App\Http\Models\InvoiceShipment;
use App\Http\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class UpdateInvoiceChargesMonthly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_corporate_invoice_charges_issue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mostly Weight Charges are updated by operation department after arrival and weight charges are deducted during arrival so this command help to compare old arrival charges with the latest and update in invoice_shipment table';

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

        $startDate =  Carbon::now()->subDays(1)->format('Y-m-d 00:00:00');
        $endDate = Carbon::now()->format('Y-m-d 23:59:59');

        $query = DB::table('invoice_shipments')
            ->select(
                'shipments.tracking_number',
                'shipments.id as shipment_id',
                'shipments.business_category_id',
                'invoice_shipments.invoice_id',
                'invoice_shipments.id as invoice_shipment_id',
                'z.id AS zone_id',
                'oc.id AS city_id',
                DB::raw('COALESCE(shipments.weight_charges, 0) AS weight_charges'),
                DB::raw('COALESCE(sac.faf_charges, 0) AS faf_charges'),
                DB::raw('COALESCE(shipments.fuel_surcharge, 0) AS fuel_surcharge'),
                DB::raw('(
            COALESCE(shipments.weight_charges, 0) +
            COALESCE(shipments.fuel_surcharge, 0) +
            COALESCE(sac.faf_charges, 0)
        ) AS new_charges'),
                DB::raw('COALESCE(invoice_shipments.charges, 0) AS invoice_charges'),
                DB::raw('COALESCE(invoice_shipments.gst, 0) AS invoice_gst'),
                DB::raw('COALESCE(invoice_shipments.invoice_amount, 0) AS invoice_amount'),
                DB::raw('COALESCE(z.gst, 0.13) AS gst')
            )
            ->leftJoin('shipments', 'invoice_shipments.shipment_id', '=', 'shipments.id')
            ->leftJoin('invoices', 'invoices.id', '=', 'invoice_shipments.invoice_id')
            ->leftJoin('shipment_additional_charges AS sac', 'sac.shipment_id', '=', 'shipments.id')
            ->leftJoin('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->leftJoin('users', 'shipments.user_id', '=', 'users.id')
            ->leftJoin('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftJoin('zones AS z', 'z.id', '=', 'oc.zone_id')
            ->where('invoice_shipments.type', 3)
            ->where('shipments.shipment_type', 1)
            ->where('users.account_type_id', 2)
            ->whereBetween('invoices.invoicing_date', [$startDate,$endDate])
//            ->where('invoices.user_id', 14814)
            ->having('new_charges', '!=', DB::raw('invoice_charges'))
            ->groupBy('invoice_shipments.shipment_id')
            ->get();


        $invoice_id = array();
        foreach ($query as $value){
            $new_weight_charges = $value->new_charges;
            if ($value->business_category_id == 1) {
                $new_gst = ROUND(($new_weight_charges * AdminFinanceController::gst($value->zone_id,$value->city_id)), 2, PHP_ROUND_HALF_DOWN);
            } else {
                $new_gst = ROUND(($new_weight_charges * AdminFinanceController::international_gst()), 2, PHP_ROUND_HALF_DOWN);
            }
            $payable = $new_weight_charges + $new_gst;
            $invoice_id[$value->invoice_id] = $value->invoice_id;

            if(!empty($payable) &&  !empty($value->invoice_shipment_id)) {
                InvoiceShipment::where('id', $value->invoice_shipment_id)->update(['charges' => $new_weight_charges, 'gst' => $new_gst, 'invoice_amount' => $payable]);
            }

        }

        if(count($invoice_id) > 0) {
            $result = DB::table('invoice_shipments')
                ->selectRaw('invoice_shipments.invoice_id, SUM(charges) AS total_charges, SUM(gst) AS total_gst, SUM(invoice_amount) AS total_invoice_amount')
                ->whereIn('invoice_id', $invoice_id)
                ->groupBy('invoice_id')
                ->get();


            foreach ($result as $value2) {
                Invoice::where('id', $value2->invoice_id)->update(['total_charges' => $value2->total_charges, 'total_gst' => $value2->total_gst, 'total_invoice_amount' => $value2->total_invoice_amount]);
            }
        }



    }
}
