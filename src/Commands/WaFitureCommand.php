<?php

namespace DavidArl\WaFiture\Commands;

use Illuminate\Console\Command;

class WaFitureCommand extends Command
{
    public $signature = 'wa-fiture';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
