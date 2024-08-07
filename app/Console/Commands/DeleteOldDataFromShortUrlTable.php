<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Models\ShortUrl;
use Carbon\Carbon;


class DeleteOldDataFromShortUrlTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:short-url-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command will delete the data of delivered and returned shipments older than 1 month';

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
        
        $date = Carbon::now()->subMonth(1)->format('Y-m-d');
        ShortUrl::where('created_at' ,'<', $date)->delete();
    }
}
