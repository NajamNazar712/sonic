<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\Retail\RetailCashDeposit;

class UpdateCashDeposittotalCashAfterRetailReleaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cash_deposits = RetailCashDeposit::where('id', '>=', 10669);
        if($cash_deposits->exists()){
            $cash_deposits = $cash_deposits->get();
            foreach ($cash_deposits as $cash_deposit){
                $cash_deposit_shipments = $cash_deposit->shipments;
                $new_total_cash = 0;
                foreach ($cash_deposit_shipments as $cash_deposit_shipment){
                    if(!in_array($cash_deposit_shipment->shipment->shipper_status_id, [17, 25]) ){
                        $new_total_cash = $new_total_cash + $cash_deposit_shipment->retail_shipment->total_charges;
                    }
                }
                $cash_deposit->total_cash = $new_total_cash;
                $cash_deposit->save();
            }
        }
    }
}
