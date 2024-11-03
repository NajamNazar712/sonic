<?php

namespace App\Jobs;

use App\Http\Traits\RvTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class BotCallDispatchThird implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RvTrait;

    protected $shipmentId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->queue = 'bot_call_shipment_third';
        $this->shipmentId = $data;

    }

    /**
     * Execute the job.
     *
     * Method : handle
     * Parameter: Shipment ID
     * Usage: Bot call implementation through a zong service specifiy for the some parameter using  api 
     * @return void
     */
    public function handle()
    {

        //
        try {
            $botRecordData = $this->botCallingDataSet($this->shipmentId,3);
            return $botRecordData;
        } catch (\Throwable $th) {
            // Log::channel('botCallJobLog')->info(' Unresponsive Count ');

            $this->createRvCronLog($th->getMessage() . ' Unresponsive Count ');
        }
       
    }
}
