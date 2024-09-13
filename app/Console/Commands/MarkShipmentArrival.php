<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\V2Pickup\V2AdminPickupsController;
use App\Http\Models\Admin\Admin;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarkShipmentArrival extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mark_arrival';

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
        $user = Admin::find(346); // Replace with actual user ID
        Auth::login($user); // Log in the user

        $request = new Request();
        $request->merge(['shipment_ids' => '42462651,42462652']);

        // Create an instance of the controller
        $controller = new V2AdminPickupsController();

        // Call the method with the constructed Request object
        $controller->individual_arrival_submit($request);

        // Optionally log out the user after the task
        Auth::logout();

    }
}
