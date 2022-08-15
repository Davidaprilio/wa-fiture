<?php

namespace DavidArl\WaFiture;

use DavidArl\WaFiture\Commands\WaFitureCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
