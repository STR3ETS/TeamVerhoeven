<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckoutController;



Route::get('/', fn () => view('welcome'));

Route::get('/intake', fn () => view('intake.index'))->name('intake.index');

// Stripe Checkout
Route::post('/intake/checkout', [CheckoutController::class, 'create'])->name('intake.checkout');
Route::get('/intake/success',   [CheckoutController::class, 'success'])->name('intake.success');
Route::get('/intake/cancel',    [CheckoutController::class, 'cancel'])->name('intake.cancel');