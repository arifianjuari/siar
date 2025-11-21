<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CleanSessions extends Command
{
    protected $signature = 'session:clean';
    protected $description = 'Membersihkan file session yang kadaluarsa';

    public function handle()
    {
        $sessionPath = storage_path('framework/sessions');
        $files = File::files($sessionPath);
        $count = 0;

        foreach ($files as $file) {
            if (time() - $file->getMTime() > config('session.lifetime') * 60) {
                File::delete($file);
                $count++;
            }
        }

        Log::info("Session cleanup completed. Removed {$count} expired session files.");
        $this->info("Session cleanup completed. Removed {$count} expired session files.");
    }
}
