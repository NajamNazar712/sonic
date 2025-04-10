<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\FingaIntegrationController;
use App\Models\FinjaLogSettlementRecord;


class CODAmountChangeSendToWallet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->queue = 'cod_amount_change_send_to_wallet';
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $api = config('app.FINGA_URL');
        $token = FingaIntegrationController::getToken($api);

        try{

            if($token) {

                $requestPayload = [
                    "client_id" => $this->data['client_id'],
                    "wallet_id" => $this->data['wallet_id'],
                    "shipment_id" => $this->data['tracking_number'],
                    "amount" => floatval($this->data['amount']),
                ];
                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'Authorization' => "Bearer " . $token,
                
                ])->post($api.'transactions/log/change', $requestPayload);
    
                FingaIntegrationController::apiLog(13, 1, $requestPayload ,$this->data['shipment_id']);
    
                if($response->successful()) { 
                    
                    $body = $response->getBody();
                    $body = json_decode($body);
                    
                    FinjaLogSettlementRecord::where('shipment_id', $this->data['shipment_id'])
                    ->update([
                        'logged_cod_charges' => $this->data['amount']
                    ]);
                    FingaIntegrationController::apiLog(14, 'success', $body ,$this->data['shipment_id']);
    
                } else {
                    $body = $response->getBody();
                    $body = json_decode($body);
                    FingaIntegrationController::apiLog(14, 'error', $body ,$this->data['shipment_id']);
                }
            }
        }  catch (\Throwable $th) {
            // Log exception details
            $errorBody = [
                'error' => $th->getMessage(),
                'code' => $th->getCode()
            ];
            FingaIntegrationController::apiLog(14, 'exception', $errorBody, $this->data['shipment_id']);
        }
        
    }
}
