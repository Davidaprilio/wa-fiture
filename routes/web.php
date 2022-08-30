<?php

use DavidArl\WaFiture\Http\Controllers\DeviceController;
use DavidArl\WaFiture\Http\Controllers\WaServerController;
use DavidArl\WaFiture\Models\Device;
use Illuminate\Support\Facades\Route;

Route::prefix('whatsapp')->name('wa')->middleware(['web'])->group(function () {

    Route::prefix('/devices')->group(function () {
        Route::post('/{device:id}/restart', [DeviceController::class, 'start'])->name('.device.restart');
        Route::post('/{device:id}/logout', [DeviceController::class, 'logout'])->name('.device.logout');
        Route::post('/{device:id}/send-test', [DeviceController::class, 'send_test'])->name('.device.test');
    });

    Route::prefix('/servers')->group(function () {
        Route::get('/', [WaServerController::class, 'index'])->name('.servers');
        Route::get('/{waServer:id}', [WaServerController::class, 'show'])->name('.server.show');
        Route::post('/save', [WaServerController::class, 'save'])->name('.server.save');
        Route::post('/toggle-status', [WaServerController::class, 'toggle_status'])->name('.servers.status');
    });


    Route::get('/', function () {
        dd(Device::new('device test', 1, 1));
    });
});
