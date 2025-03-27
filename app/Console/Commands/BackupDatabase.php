<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database ke storage/app/backups dengan format Y-m-d_His.sql';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses backup database...');

        // Dapatkan konfigurasi database saat ini
        $connection = Config::get('database.default');
        $driver = Config::get("database.connections.{$connection}.driver");

        // Format nama file dengan tanggal dan waktu
        $backupFileName = date('Y-m-d_His') . '.sql';
        $backupPath = 'backups/' . $backupFileName;
        $fullPath = storage_path('app/' . $backupPath);

        // Pastikan direktori backups ada
        if (!Storage::exists('backups')) {
            Storage::makeDirectory('backups');
        }

        // Jalankan perintah backup sesuai dengan driver database
        switch ($driver) {
            case 'mysql':
                $this->backupMysql($fullPath);
                break;
            case 'sqlite':
                $this->backupSqlite($fullPath);
                break;
            default:
                $this->error("Driver database {$driver} tidak didukung untuk backup otomatis.");
                return 1;
        }

        // Tampilkan lokasi backup
        if (Storage::exists($backupPath)) {
            $this->info("Backup database berhasil disimpan di: {$fullPath}");

            // Hapus backup lama jika terlalu banyak (simpan 10 backup terakhir)
            $this->cleanOldBackups();
            return 0;
        } else {
            $this->error('Gagal melakukan backup database!');
            return 1;
        }
    }

    /**
     * Backup database MySQL menggunakan mysqldump
     */
    private function backupMysql($fullPath)
    {
        // Dapatkan konfigurasi MySQL
        $host = Config::get('database.connections.mysql.host');
        $port = Config::get('database.connections.mysql.port');
        $database = Config::get('database.connections.mysql.database');
        $username = Config::get('database.connections.mysql.username');
        $password = Config::get('database.connections.mysql.password');

        // Buat command untuk mysqldump
        $command = [
            'mysqldump',
            '--host=' . $host,
            '--port=' . $port,
            '--user=' . $username,
        ];

        // Tambahkan password jika ada
        if ($password) {
            $command[] = '--password=' . $password;
        }

        // Tambahkan opsi lain
        $command = array_merge($command, [
            '--skip-comments',
            '--no-tablespaces',
            '--single-transaction',
            '--quick',
            $database,
        ]);

        // Jalankan proses
        $process = new Process($command);
        $process->setOutput($fullPath);

        try {
            $process->mustRun();
            $this->info('Proses mysqldump selesai.');
            return true;
        } catch (ProcessFailedException $exception) {
            $this->error('Proses mysqldump gagal: ' . $exception->getMessage());
            return false;
        }
    }

    /**
     * Backup database SQLite
     */
    private function backupSqlite($fullPath)
    {
        $databasePath = Config::get('database.connections.sqlite.database');

        // Pastikan file database ada
        if (!file_exists($databasePath)) {
            $this->error("File database SQLite tidak ditemukan di: {$databasePath}");
            return false;
        }

        // Copy file database ke lokasi backup
        try {
            copy($databasePath, $fullPath);
            $this->info('Database SQLite berhasil dicopy.');
            return true;
        } catch (\Exception $e) {
            $this->error('Proses backup SQLite gagal: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Hapus backup lama (simpan 10 backup terakhir)
     */
    private function cleanOldBackups()
    {
        $backups = Storage::files('backups');

        // Urutkan berdasarkan tanggal (terbaru di atas)
        usort($backups, function ($a, $b) {
            return strcmp($b, $a);
        });

        // Simpan 10 backup terakhir, hapus sisanya
        if (count($backups) > 10) {
            $oldBackups = array_slice($backups, 10);

            foreach ($oldBackups as $backup) {
                Storage::delete($backup);
                $this->info("Menghapus backup lama: {$backup}");
            }
        }
    }
}
