<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class RestartSupervisordProcesses extends Command
{
    protected $signature = 'supervisord:restart';
    protected $description = 'Restart Supervisord processes that are not running';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $command = "supervisorctl status | grep -v RUNNING | awk '{print \$1}' | while read -r process_name; do supervisorctl restart \"\$process_name\"; done";
        $process = Process::fromShellCommandline($command, '/var/www/html/sonic');
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error($process->getErrorOutput());
            return 1;
        }

        $this->info($process->getOutput());
        return 0;
    }

}
