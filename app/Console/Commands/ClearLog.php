<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearLog extends Command
{
    protected $signature = 'log:clear';
    protected $description = 'Clear Laravel log files';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $files = glob(storage_path('logs/*.log'));
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $this->info('Log files cleared successfully!');
    }
}
