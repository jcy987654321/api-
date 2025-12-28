<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LogClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:clear {--all : Clear all logs, not just old ones}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear log files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logPath = storage_path('logs');
        $files = File::files($logPath);
        $deletedCount = 0;

        foreach ($files as $file) {
            if ($file->getExtension() === 'log' && $file->getFilename() !== '.gitignore') {
                if ($this->option('all')) {
                    File::delete($file->getPathname());
                    $deletedCount++;
                } else {
                    // Default to clearing logs older than 30 days if not --all
                    $lastModified = \Carbon\Carbon::createFromTimestamp($file->getMTime());
                    if ($lastModified->lt(\Carbon\Carbon::now()->subDays(30))) {
                        File::delete($file->getPathname());
                        $deletedCount++;
                    }
                }
            }
        }

        $this->info("Cleared {$deletedCount} log files.");
    }
}
