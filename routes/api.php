<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::delete('auth', [AuthController::class, 'delete'])->name('auth.delete');
});

Route::middleware('throttle:10,3600')->group(function () {
    Route::post('auth', [AuthController::class, 'store'])->name('auth.store');
});

