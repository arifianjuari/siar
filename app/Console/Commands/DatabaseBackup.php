<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\File;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database ke storage/app/backups';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Pastikan direktori backup ada
        $backupDir = storage_path('app/backups');

        if (!File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        // Dapatkan konfigurasi database dari .env
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        // Nama file backup dengan format Y-m-d_His.sql
        $fileName = now()->format('Y-m-d_His') . '.sql';
        $backupPath = $backupDir . '/' . $fileName;

        // Buat command mysqldump
        $command = sprintf(
            'mysqldump -h %s -P %s -u %s %s %s > "%s"',
            $host,
            $port,
            $username,
            $password ? '-p' . $password : '',
            $database,
            $backupPath
        );

        // Jalankan proses backup
        $process = Process::fromShellCommandline($command);
        $process->setTimeout(3600); // Timeout 1 jam

        try {
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Log sukses backup
            $this->info('Database berhasil dibackup ke: ' . $backupPath);

            // Prune backup lama jika lebih dari 30 file
            $this->pruneOldBackups($backupDir);

            return 0;
        } catch (\Exception $e) {
            $this->error('Backup database gagal: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Hapus file backup yang lama (menyimpan maks 30 backup)
     */
    protected function pruneOldBackups($backupDir)
    {
        $files = File::glob($backupDir . '/*.sql');

        if (count($files) <= 30) {
            return;
        }

        // Urutkan file berdasarkan waktu modifikasi (terbaru dulu)
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        // Hapus file lama (lebih dari 30)
        $filesToDelete = array_slice($files, 30);
        foreach ($filesToDelete as $file) {
            File::delete($file);
            $this->info('Menghapus backup lama: ' . $file);
        }
    }
}
