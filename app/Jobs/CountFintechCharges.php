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
use App\Http\Models\Admin\TraxPayTransaction;
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
                    }
                    else{
                        $fintech_company_id = '1';
                    }
                        $user_fintech_charges = UserFintectCharges::where('user_id',$user_id->user_id)->where('status','1')->first();
                        $standart_fintech_charges = standard_fintech_charges::where('id','1')->first();
                    if(!empty($user_fintech_charges)){
                        $charges            = $user_fintech_charges->fintech_charges;
                        $percentage         = ($total_cod_amount/100) * $charges;
                        $cal_fed            = ($percentage/100) * $standart_fintech_charges->standard_fed_charges;  
                        $total_charges      = number_format($percentage + $cal_fed, 2) ;
                        $charges_applicable = '1';
                    }  
                    else{
                        $standard_charges       = ($total_cod_amount/100) * $standart_fintech_charges->standard_fintech_charges;
                        $standard_charges_FED   = ($standard_charges/100) * $standart_fintech_charges->standard_fed_charges;
                        $total_charges          = number_format($standard_charges + $standard_charges_FED, 2) ;
                        $charges_applicable     = '2';
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
                    $traxpaytransaction = new TraxPayTransaction();
                    $traxpaytransaction::where('shipment_id',$valid_shipments_valuse)->update([
                        'link'           => $this->payment_link,
                        'cod_amount'     => $total_cod_amount,
                        'fintech_amount' => $fintech_charges,
                    ]);
                    $request_body  = array(
                        'payment_link'          => $this->payment_link,
                        'unique_code'           => $this->unique_key,
                        'pay_type_id'           => 1,
                        'fintech_company'       => $fintech_company_id,
                        'cod_amount'            => $total_cod_amount,
                        'fintech_amount'        => $fintech_charges,
                    );
                    $options = [
                        'form_params' => $request_body,
                        'http_errors' => false,
                    ];
                    $client = new Client();
                    $response = $client->request('Post', $this->url,$options);
                    if ($response->getStatusCode() !== 200) {
                        abort(404, 'URL not found');
                    }
                }
            }
            catch(exception $e){
                return $e;
            }
        }
    }
}
