<?php

use App\Http\Controllers\ShipmentsPaymentJourneyController;
use App\Http\Models\Admin\AdjustmentLog;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentCalculation;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\RetailAdjustmentLog;
use App\Http\Models\RetailPendingPayment;
use App\Http\Models\RetailPendingPaymentShipment;
use App\Http\Models\Shipment;
use Illuminate\Database\Seeder;

class UpdatePaymentsForManualAdjustmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shipment_ids = [14656368,14654889,14654035,14656228,14598220,14606895,14583752,14620267,14537584,14606752,14634544,14656129,14626181,14584137,14587380,14608593,14625320,14591835,14632846,14609780,14637799,14637936,14578971,14590285,14607726,14639836,14655983,14634537,14634595,14638144,14625047,14627456,14654479,14606960,14656958,14637905,14637617,14654833,14587077,14656235,14617061,14656817,14599708,14654109,14656352,14637647,14608211,14655916,14637703,14637637,14634646,14638553,14634555,14654114,14599547,14583556,14626818,14655795,14592238,14618496,14637679,14634564,14626073,14591903,14654769,14654449,14649170,14632888,14499520,14657251,14599575,14609314,14657135,14654790,14599902,14606833,14633687,14656108,14633857,14633801,14654017,14654203,14599899,14608540,14654250,14626844,14638898,14599733,14654076,14637720,14656393,14655996,14606622,14654513,14634624,14611022,14626350,14653840,14648603,14631757,14654011,14654087,14655875,14620428,14637906,14634065,14566705,14654552,14607472,14608656,14634031,14637517,14637480,14583868,14633512,14626008,14657278,14599850,14637880,14638751,14656331,14638806,14610725,14620126,14673560,14608680,14606577,14634446,14589094,14634491,14589992,14583391,14623376,14656901,14627429,14631838,14588268,14637979,14648880,14654445,14591041,14595659,14648521,14642789,14633987,14654232,14574417,14698738,14655830,14607552,14671176,14624843,14607708,14625091,14637848,14666842,14670901,14627849,14654534,14638642,14606710,14633401,14633826,14610773,14642840,14673582,14665816,14637790,14673551,14646401,14653916,14673384,14698994,14579288,14607798,14648942,14700561,14649893,14558114,14646478,14699285,14669034,14668331,14623585,14565109,14654135,14657032,14671917,14653854,14579710,14642062,14698689,14671922,14671938,14698655,14641703,14673267,14673295,14700746,14450141,14674532,14673117,14670812,14638124,14698605,14670864,14671926,14638517,14673309,14625635,14674180,14669178,14674509,14674208,14667282,14673200,14673235,14671930,14699694,14670873,14668290,14610750,14620589,14597505,14670919,14669417,14666821,14729464,14627408,14669455,14627759,14670730,14674344,14674475,14673427,14673536,14699233,14674516,14673439,14702136,14666751,14705377,14627784,14711463,14668343,14698797,14670961,14637607,14673485,14703533,14745831,14741652,15273502];
        $payables = [-1338.38,-2915.14,-2383.38,-3120.07,-5455.61,-3183.38,-4095.61,-1088.38,-5079.53,-4024.53,-2797.38,-21217.38,-7710.07,-4447.07,-5108.53,-18273.45,491.55,-3040.61,-3983.38,-10288.07,-3223.38,-1488.38,-1783.38,-3388.38,-2488.38,-5183.07,-3650.61,294.93,-2890.61,-1390.07,-1718.38,-15842.45,229.39,-4533.38,-7910.07,-3733.38,-1443.38,-5633.61,-11324.53,-2163.38,-19928.11,-3689.38,-2713.38,-3633.38,-3653.38,-1983.38,-2843.38,294.93,-3845.61,-5135.61,229.39,-2983.38,196.62,-2213.38,-1309.38,-1438.38,-2563.38,-7051.61,-1878.38,-938.38,-826.38,-11738.07,-4285.61,-15832.53,-2295.61,-15311.53,-2395.61,229.39,-888.38,-8503.07,-1443.38,-34144.46,-6156.61,-2520.14,-1883.38,-1713.38,-3866.61,-9370.07,196.62,196.62,-2528.38,-2978.38,-1101.38,-6095.61,-1688.38,-868.07,-3152.61,-12874.53,-2455.61,-2212.07,-1430.14,-7583.92,-1165.14,-1928.38,196.62,-7248.61,-3625.07,-1443.38,-383.38,196.62,-1183.38,-2488.38,-10193.38,-3140.61,-2993.38,196.62,-2289.38,-1125.14,-3175.61,-3183.61,196.62,-24058.45,294.93,-5091.07,196.62,-2895.61,-5370.07,-1238.38,-938.38,-1720.07,-1383.38,-11064.53,-988.38,-11002.33,-2444.38,-8393.38,-8088.99,-8042.07,-2878.38,196.62,-1638.07,-13577.53,-4595.07,-4970.07,-988.38,196.62,-35019.76,-1088.38,-883.38,-2418.38,-12105.07,-5041.53,-2633.38,-2325.07,229.39,-1533.38,-18125.68,-3623.14,445.67,-3543.61,-938.38,-3935.61,-2075.14,-1900.61,-7605.07,-938.38,-3025,-5930.61,-2150.61,-3479.38,-12467.99,229.39,196.62,-1833.38,-2500.61,-1659.38,-1138.38,491.55,-1188.38,-2016.38,-2530.14,-14171.07,-2488.38,-2233.38,-2333.38,-1238.38,-2580.38,-2950.61,-5265.07,-2331.38,-2928.38,-5744.61,-1303.38,-4620.07,-7825.61,-2833.38,-3479.38,-938.38,-3288.38,-5292.61,-3705.61,-718.38,-3125,-3190,-1698.38,-42567.68,-3800,-2578.38,-1588.38,-5726.76,-2168.38,-1718.38,-1993.38,-1316.38,-2828.38,-1580,-718.38,-938.38,196.62,-3808.38,196.62,-45726.14,-988.38,-938.38,-3433.38,196.62,-6858.45,-4955.61,-2438.38,196.62,-1433.38,-4478.38,-4443.38,-2595.61,196.62,-2708.38,-11635.07,196.62,-6746.61,-1318.38,-3748.38,-1583.38,-3851.38,-2483.38,-1443.38,-588.38,-718.38,-4573.38,-1383.38,-7422.61,294.93,-4381.07,-1718.38,-4588.45,-2333.38,-2033.38,-2883.38,-1833.38,-20446,-1338.38,786.48,-3893.61,-2192.94];

        $adjustment_type = 15;
        $payable_remarks = 'Due to error, double payment was done';

        foreach ($shipment_ids as $index => $shipment_id) {

            $shipment = Shipment::find($shipment_id);
            if($shipment){

                $amount = 0;
                $charges = 0;
                $gst = 0;
                if ($shipment->shipment_type == 1) {
                    $pending_payment = PendingPayment::where('user_id', $shipment->user_id);

                    if ($pending_payment->exists()) {
                        $pending_payment = $pending_payment->first();

                        $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
                        $pending_payment->adjusted_shipments = $pending_payment->adjusted_shipments + 1;

                        $pending_payment->save();
                    } else {
                        $pending_payment = new PendingPayment();

                        $pending_payment->user_id = $shipment->user_id;
                        $pending_payment->total_shipments = 1;
                        $pending_payment->delivered_shipments = 0;
                        $pending_payment->returned_shipments = 0;
                        $pending_payment->adjusted_shipments = 1;

                        $pending_payment->save();
                    }

                    $pending_payment_shipment = new PendingPaymentShipment();

                    $pending_payment_shipment->pending_payment_id = $pending_payment->id;
                    $pending_payment_shipment->shipment_id = $shipment_id;
                    $pending_payment_shipment->type = 2;
                    $pending_payment_shipment->amount = $amount;
                    $pending_payment_shipment->charges = $charges;
                    $pending_payment_shipment->gst = $gst;
                    $pending_payment_shipment->payable = $payables[$index];

                    $pending_payment_shipment->save();

                    if ($adjustment_type) {
                        $adjustment_log = new AdjustmentLog();

                        $adjustment_log->shipment_id = $shipment_id;
                        $adjustment_log->adjustment_type_id = $adjustment_type;
                        $adjustment_log->admin_id = 49;
                        $adjustment_log->adjustment_amount = $payables[$index];
                        $adjustment_log->remarks = $payable_remarks;
                        $adjustment_log->pending_id = $pending_payment->id;
                        $adjustment_log->type = 1;

                        $adjustment_log->save();
                    }


                    $pending_payment_charges = PendingPaymentCalculation::where('pending_payment_id', $pending_payment->id);
                    if ($pending_payment_charges->exists())
                        $pending_payment_charges = $pending_payment_charges->first();
//                        $pending_payment_charges->amount = $pending_payment_charges->amount + $amount;
//                        $pending_payment_charges->charges = $pending_payment_charges->charges + $charges;
//                        $pending_payment_charges->gst = $pending_payment_charges->gst + $gst;
//                        $pending_payment_charges->wht = $pending_payment_charges->wht + 0;
                        $pending_payment_charges->payable = $pending_payment_charges->payable + $payables[$index];
                        $pending_payment_charges->save();
                    }
                    else {
                        $pending_payment_charges = new PendingPaymentCalculation();
                        $pending_payment_charges->pending_payment_id = $pending_payment->id;
                        $pending_payment_charges->amount = 0;
                        $pending_payment_charges->charges = 0;
                        $pending_payment_charges->gst = 0;
                        $pending_payment_charges->wht = 0;
                        $pending_payment_charges->payable = $payables[$index];
                        $pending_payment_charges->save();
                    }

                    ShipmentsPaymentJourneyController::add($shipment_id, 4, 49, $payable_remarks);
            }
        }
    }
}
