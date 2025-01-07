<?php

use App\Http\Controllers\UrlController;

Route::get('/', [UrlController::class, 'index'])->name('home');
Route::post('/urls', [UrlController::class, 'store'])->name('url.store');
Route::get('/~{shortCode}', [UrlController::class, 'redirect'])->name('url.redirect'); 