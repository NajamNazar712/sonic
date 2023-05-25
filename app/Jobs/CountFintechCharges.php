<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Models\Shipment;
use App\Http\Models\Admin\FintechCompany;
use App\Http\Models\Admin\UserFintectCharges;
use App\Http\Models\Admin\standard_fintech_charges;
use App\Http\Models\Admin\shipmentFintechCharges;
use GuzzleHttp\Client;
class CountFintechCharges implements ShouldQueue


{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $valid_shipments;
    protected $payment_link;
    protected $unique_key;
    protected $url;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($valid_shipments,$payment_link,$unique_key,$url)
    {
        $this->valid_shipments = $valid_shipments;
        $this->payment_link    = $payment_link;
        $this->unique_key      = $unique_key;
        $this->url             = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $valid_shipments = $this->valid_shipments;
        if(is_array($valid_shipments)){
            try{
                foreach($valid_shipments as $valid_shipments_valuse){
                $user_id = Shipment::where('id',$valid_shipments_valuse)->first();
                $total_cod_amount = Shipment::where('id', $user_id->id)->where(function ($query) {
                    $query->where('booking_type_id', '!=', 4)
                        ->orWhere(function ($sub_query) {
                            $sub_query->where('booking_type_id', '=', 4)
                                ->where('charges_mode_id', '=', 2);
                        });
                })->sum('amount');
                $fintech_company = FintechCompany::where('id','1');
                    if($fintech_company->exists()){
                        $fintech_company_id = $fintech_company->first()->id;
                    }else{
                        $fintech_company_id = '1';
                    }
                $user_fintech_charges = UserFintectCharges::where('user_id',$user_id->user_id)->first();
                if(!empty($user_fintech_charges)){
                    $charges        =  $user_fintech_charges->fintech_charges;
                    $percentage     = ($total_cod_amount/100) * $charges;
                    $total_charges  = round($percentage) + $total_cod_amount;
                    $charges_applicable = '1';
                }  
                else{
                    $standart_fintech_charges = standard_fintech_charges::where('id','1')->first();
                    $standard_charges       = ($total_cod_amount/100) * $standart_fintech_charges->standard_fintech_charges;
                    $standard_charges_FED   = ($standard_charges/100) * $standart_fintech_charges->standard_fed_charges;
                    $total_charges = round($standard_charges) + round($standard_charges_FED);
                    $charges_applicable = '2';
                    }
                    Shipment::where('id', $user_id->id)->update([
                        'fintech_charges' => $total_charges
                    ]);
                    $shipment_fintech_charges =  new shipmentFintechCharges();
                    $shipment_fintech_charges->shipment_id     =  $user_id->id ;
                    $shipment_fintech_charges->fintech_charges =  $total_charges;
                    $shipment_fintech_charges->applied_to      =  $charges_applicable;
                    $shipment_fintech_charges->save();


                    $customer_details = Shipment::where('shipments.id',$valid_shipments_valuse)
                    ->join('cities','shipments.consignee_city_id','cities.id')
                    ->select(
                    'shipments.tracking_number as TrankingID', 
                    'shipments.consignee_name as Name',
                    'shipments.consignee_address as Address',
                    'cities.name as city_name')->first();

                    if($charges_applicable == '1'){
                        $fintech_charges = 0;
                    }
                    else{
                        $fintech_charges = $total_charges;
                    }
                    $request_body  = array(
                        'tracking_no'           => $customer_details->TrankingID,
                        'payment_link'          => $this->payment_link,
                        'unique_key'            => $this->unique_key,
                        'fintech'               => $fintech_charges,
                        'fintech_company_id'    => $fintech_company_id,
                        'cod_amount'            => $total_cod_amount,
                        'current_status'        => 'Out for Delivery',
                    );
                $client = new Client();
                $response = $client->request('Post', $this->url, [
                'form_params' => $request_body,
                ]);
            }
        }
            catch(exception $e){
                return $e;
            }
        }
    }
}
