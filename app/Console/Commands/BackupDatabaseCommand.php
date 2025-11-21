<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupDatabaseCommand extends Command
{
    /**
     * Nama dari command
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * Deskripsi command
     *
     * @var string
     */
    protected $description = 'Backup database ke storage/app/backups';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai backup database...');

        // Buat direktori backup
        $backupPath = storage_path('app/backups/database');

        // Pastikan direktori ada
        if (!file_exists($backupPath)) {
            if (!mkdir($backupPath, 0755, true)) {
                $this->error('Tidak dapat membuat direktori backup: ' . $backupPath);
                return 1;
            }
        }

        // Dapatkan konfigurasi database
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver !== 'mysql') {
            $this->error("Driver database {$driver} tidak didukung, hanya MySQL/MariaDB yang didukung.");
            return 1;
        }

        $host = config("database.connections.{$connection}.host");
        $port = config("database.connections.{$connection}.port");
        $database = config("database.connections.{$connection}.database");
        $username = config("database.connections.{$connection}.username");
        $password = config("database.connections.{$connection}.password");

        // Buat nama file dengan timestamp
        $timestamp = Carbon::now()->format('Ymd_His');
        $filename = "{$database}_{$timestamp}.sql.gz";
        $fullPath = $backupPath . '/' . $filename;

        // Buat command mysqldump
        $dumpCommand = sprintf(
            'mysqldump --host=%s --port=%s --user=%s %s --single-transaction --routines --triggers --events %s | gzip > %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $password ? '--password=' . escapeshellarg($password) : '',
            escapeshellarg($database),
            escapeshellarg($fullPath)
        );

        // Eksekusi perintah backup database
        $process = Process::fromShellCommandline($dumpCommand);
        $process->setTimeout(3600); // 1 jam timeout

        try {
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Cek apakah file backup sudah ada
            if (!file_exists($fullPath)) {
                $this->error("Backup gagal: File backup tidak ditemukan");
                return 1;
            }

            // Buat symlink ke backup terbaru
            $latestPath = $backupPath . '/' . $database . '_latest.sql.gz';
            if (file_exists($latestPath)) {
                unlink($latestPath);
            }
            symlink($fullPath, $latestPath);

            // Tampilkan pesan sukses
            $fileSize = $this->getHumanReadableSize(filesize($fullPath));
            $this->info("Backup berhasil dibuat: {$fullPath} ({$fileSize})");
            $this->info("Symlink dibuat: {$latestPath}");

            // Hapus backup lama (lebih dari 7 hari)
            $this->cleanOldBackups($backupPath, $database);

            return 0;
        } catch (\Exception $e) {
            $this->error("Backup gagal: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Hapus backup lama
     */
    protected function cleanOldBackups($backupPath, $database, $days = 7)
    {
        $this->info("Menghapus backup lama (>= {$days} hari)...");

        $cutoffDate = Carbon::now()->subDays($days);
        $deletedCount = 0;

        foreach (glob("{$backupPath}/{$database}_*.sql.gz") as $file) {
            // Jangan hapus symlink
            if (is_link($file)) {
                continue;
            }

            $fileDate = Carbon::createFromTimestamp(filemtime($file));
            if ($fileDate->lt($cutoffDate)) {
                if (unlink($file)) {
                    $deletedCount++;
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("{$deletedCount} backup lama telah dihapus");
        }
    }

    /**
     * Konversi ukuran file ke format yang mudah dibaca
     */
    protected function getHumanReadableSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
