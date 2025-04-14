<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Traits\FinSurgentLogTrait;

class WalletLogDispatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, FinSurgentLogTrait;
    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->queue = 'wallet_log_dispatch_log';
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $chunks = array_chunk($this->data, 50);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $item) {
                $shipmentId = $item['shipmentId'];
                unset($item['shipmentId']);
                $this->arrival_shipment_logs($item, $shipmentId);
            }
            sleep(20);
        }

    }
}
