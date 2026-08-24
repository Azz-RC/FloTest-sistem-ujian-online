<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/buat-test', [TestController::class, 'store']);

Route::get('/test/kode/{kode}', [TestController::class, 'showByKode']);

Route::get('/test/{id}', [TestController::class, 'show']);

Route::post('/masuk-room', [TestController::class, 'masukRoom']);

Route::post('/kerjakan-test', [TestController::class, 'kerjakanTest']);

// Ambil riwayat / hasil test
Route::get('/riwayat-test/{id}', [TestController::class, 'riwayatTest']);