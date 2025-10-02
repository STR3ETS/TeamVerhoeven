<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckoutController;



Route::get('/', function () { return view('welcome'); });



Route::get('/intake', function () {
    return view('intake.index');
})->name('intake.index');

// ========== Stripe Checkout ========== 
Route::post('/intake/checkout', [CheckoutController::class, 'create'])->name('intake.checkout');
