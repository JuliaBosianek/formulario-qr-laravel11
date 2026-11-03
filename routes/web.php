<?php

use App\Http\Controllers\QRController;
use Illuminate\Support\Facades\Route;

Route::get('/', [QrController::class, 'index'])->name('qr.index');
Route::post('/generar', [QrController::class, 'generar'])->name('qr.generar');
