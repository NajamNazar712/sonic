<?php

namespace App\Console\Commands;

use App\Http\Controllers\Reports\AccountReconciliationController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PushBackupDatatoamazon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:amazon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Storage backup to s3';

    /**
     * Create a new command instance.
     *
     * @return void


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $files = File::glob(public_path() . '/sonic_storage_archieve/*.*');
        $now = Carbon::now();
        foreach ($files as $file) {
            $file_name = pathinfo($file);
            $stream = fopen($file, 'r');
            Storage::disk('s3')->put('sonic-archive/sonic_storage_archieve/' . $file_name['basename'], $stream);
            fclose($stream);
        }
    }

}
