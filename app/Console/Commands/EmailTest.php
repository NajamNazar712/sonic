<?php

namespace App\Console\Commands;

// use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\TempRiderDelivery;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EmailTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test';

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
        // $array = ['added_at' => '2024-01-23 14:17:31', 'delivery_note_id' => 1905813, 'shipment_id' => 33066163, 'rider_id' => 2624, 'start_location_latitude' => '31.474250', 'start_location_longitude' => 74.332263, 'actual_location_latitude' => '31.472445', 'actual_location_longitude' => '74.332773', 'cnic' => '35202-8866544-4', 'rider_status_id' => 14, 'rider_reason_id' => 12, 'image' => 'i'];

        // $md5 = md5(json_encode($array));

        // $loop = 10;

        // for($i = 0; $i < $loop; $i++){
        //     if(!TempRiderDelivery::where('payload', $md5)->exists()){
        //         $temprd = new TempRiderDelivery();
        //         $temprd->payload = $md5;
        //         $temprd->save();

        //         // $temprd->delete();
        //     }
        //     else{
        //         echo "$i : Unable to insert, already exists";
        //         echo "\n";
        //     }
            
        // }

        // Log::channel('code_test_log')->info('Noman bhai ka log in logs!');
        echo "Noman bhai ka log!";
//        $ref = 'nothing';
//        NotificationsController::send(219, $ref);
    }
}
