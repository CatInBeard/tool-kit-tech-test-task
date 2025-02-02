<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuestionaryController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::delete('auth', [AuthController::class, 'delete'])->name('auth.delete');
    Route::get('users/me', [UserController::class, "showMe"])->name('users.me');
    Route::get('users/me/questionary', [UserController::class, "questionary"])->name('users.questionary.index');
    Route::apiResource('users', UserController::class)->except('store');
    Route::apiResource('questionary', QuestionaryController::class)->except('store');
    Route::post('questionary/{id}/confirm', [QuestionaryController::class, 'confirm'])->name('questionary.confirm');
});

Route::middleware('throttle:10,1')->group(function () {
    Route::post('auth', [AuthController::class, 'store'])->name('auth.store');
});

Route::middleware('throttle:10,3600')->group(function () {
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::post('questionary', [QuestionaryController::class, 'store'])->name('questionary.store');
});
