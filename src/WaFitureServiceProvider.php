<?php

namespace DavidArl\WaFiture;

use DavidArl\WaFiture\Commands\WaFitureCommand;
use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class WaFitureServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('wa-fiture')
            ->hasConfigFile()
            ->hasViews('wafiture')
            ->hasRoute('web');

        // Migrations
        $package
            ->hasMigration('create_wa_servers_table')
            ->hasMigration('create_devices_table')
            ->hasMigration('create_contacts_table')
            ->hasMigration('create_notifications_table')
            ->hasMigration('create_messages_table');

        // Commands
        $package
            ->hasCommand(WaFitureCommand::class);
    }

    public function packageBooted()
    {
        $this->loadAllComponent();
    }

    protected function loadAllComponent()
    {
        $this->registerComponent('layout');
        $this->registerComponent('script');

        $this->registerComponent('server.table');
        $this->registerComponent('server.form-save');

        $this->registerComponent('device.table');
        $this->registerComponent('device.btn-restart');
        $this->registerComponent('device.btn-logout');
        $this->registerComponent('device.scan-box');
    }

    /**
     * Register the given component.
     *
     * @param  string  $component
     * @return void
     */
    protected function registerComponent(string $component)
    {
        Blade::component("wafiture::components.{$component}", "wa-{$component}");
    }
}
