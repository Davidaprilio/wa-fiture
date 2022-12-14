<?php

use Quods\Whatsapp\Http\Controllers\WaServerController;
use Illuminate\Support\Facades\Route;

Route::prefix('whatsapp')->name('wa')->middleware(['web'])->group(function () {
    Route::prefix('/servers')->group(function () {
        Route::get('/', [WaServerController::class, 'index'])->name('.servers');
        Route::get('/{waServer:id}', [WaServerController::class, 'show'])->name('.server.show');
        Route::post('/save', [WaServerController::class, 'save'])->name('.server.save');
        Route::post('/toggle-status', [WaServerController::class, 'toggle_status'])->name('.servers.status');
    });
});
