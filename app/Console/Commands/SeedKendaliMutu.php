<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\KendaliMutuBiayaSeeder;

class SeedKendaliMutu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:kendali-mutu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed kendali mutu biaya data tanpa error log';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mulai proses seeding Kendali Mutu Biaya...');

        try {
            $seeder = new KendaliMutuBiayaSeeder();
            $seeder->setCommand($this);
            $seeder->run();

            $this->info('Seeding berhasil dilakukan!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Seeding gagal: ' . $e->getMessage());
            return 1;
        }
    }
}
