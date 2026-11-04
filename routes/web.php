<?php

use App\Http\Controllers\QrController;
use Illuminate\Support\Facades\Route;

Route::get('/', [QrController::class, 'index'])->name('qr.index');
Route::post('/qr', [QrController::class, 'generar'])->name('qr.generar');
