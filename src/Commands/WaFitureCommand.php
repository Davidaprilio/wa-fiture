<?php

namespace Quods\Whatsapp\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class WaFitureCommand extends Command
{
    public $signature = 'wafiture:install {force?} {--migrate=yes}';

    public $description = 'Untuk Pertama kali Install, jika dilakukan saat tahap development akan menyeberangkan error tak terduga';

    public function handle(): int
    {
        $force = $this->argument('force');
        $runmigrate = $this->option('migrate') == 'yes';

        if (!is_null($force)) {
            if ($force === 'force') {
                $force = true;
            } else {
                $this->line("tidak menerima argument '{$force}' hanya menerima argument 'force'");
                return false;
            }
        }

        if (env('APP_ENV', 'production') == 'production') {
            $this->error('Tidak bisa melakukan install pada environment production');
            $this->line('Jika benar ingin menginstall, ubah dulu .env ke:');
            $this->line('APP_ENV=development');

            return 1;
        }

        $confirm = $this->confirm("Aksi ini akan mempublish file config dan migration, dan mencoba menjalankan migrasi DB lanjutkan", false);
        if ($confirm === false) {
            $this->info('OK diabatalkan!');

            return 0;
        }

        if ($force) {
            Artisan::call('vendor:publish --tag="wa-fiture-config" --force');
        } else {
            Artisan::call('vendor:publish --tag="wa-fiture-config"');
        }
        $this->line(Artisan::output());
        if ($force) {
            Artisan::call('vendor:publish --tag="wa-fiture-migrations" --force');
        } else {
            Artisan::call('vendor:publish --tag="wa-fiture-migrations"');
        }
        $this->line(Artisan::output());

        if ($runmigrate) {
            $this->info('Running Migration');
            Artisan::call('migrate');
            $this->line(Artisan::output());
        }

        $this->line('  WaFiture berhasil diinstall', 'info');
        $this->newLine(2);
        $this->line('~~~ SELAMAT MENGODING RIA ~~~');
        Artisan::call('inspire');
        $this->line(Artisan::output());

        return self::SUCCESS;
    }
}
