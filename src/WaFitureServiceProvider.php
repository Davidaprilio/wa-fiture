<?php

namespace DavidArl\WaFiture;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use DavidArl\WaFiture\Commands\WaFitureCommand;

class WaFitureServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('wa-fiture')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_wa-fiture_table')
            ->hasCommand(WaFitureCommand::class);
    }
}
