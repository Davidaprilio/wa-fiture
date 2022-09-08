<?php

namespace DavidArl\WaFiture\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class WaFitureCommand extends Command
{
    public $signature = 'wafiture:install';

    public $description = 'Untuk Pertama kali Install, jika dilakukan saat tahap development akan menyeberangkan error tak terduga';

    public function handle(): int
    {
        if (env('APP_ENV', 'production') == 'production' || env('APP_DEBUG', true) === false) {
            $this->error('Tidak bisa melakukan install pada environment production atau debug=false');
            $this->line('Jika benar ingin menginstall ubah .env ke:');
            $this->line('APP_ENV=development');
            $this->line('APP_DEBUG=true');

            return 1;
        }

        $confirm = $this->confirm('Aksi ini akan mempublish file config dan migration, dan mencoba menjalankan migrasi DB lanjutkan', true);
        if ($confirm === false) {
            $this->info('OK diabatalkan!');

            return 0;
        }

        Artisan::call('vendor:publish --tag="wa-fiture-config"');
        $this->line(Artisan::output());
        Artisan::call('vendor:publish --tag="wa-fiture-migrations"');
        $this->line(Artisan::output());

        $this->info('Running Migration');
        Artisan::call('migrate');
        $this->line(Artisan::output());

        $this->line('  WaFiture berhasil diinstall', 'info');
        $this->newLine(2);
        $this->line('~~~ SELAMAT MENGODING RIA ~~~');

        return self::SUCCESS;
    }
}
