<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\IntakeAccessController;
use App\Http\Controllers\MagicLoginController;
use Illuminate\Http\Request;
use App\Models\AccessKey;

Route::get('/', function () { return view('welcome'); });

Route::middleware('guest')->group(function () {
    Route::get('/login', [MagicLoginController::class, 'show'])->name('login.show');
    Route::post('/login/request', [MagicLoginController::class, 'requestCode'])
        ->middleware('throttle:5,1')->name('login.request');
    Route::post('/login/verify', [MagicLoginController::class, 'verifyCode'])
        ->middleware('throttle:10,1')->name('login.verify');
});
Route::middleware(['auth'])->group(function () {
    Route::prefix('coach')->name('coach.')->middleware('role:coach')->group(function () {
        Route::get('/', fn () => view('coach.index'))->name('index');
    });
    Route::prefix('client')->name('client.')->middleware('role:client')->group(function () {
        Route::get('/', fn () => view('client.index'))->name('index');
    });
    Route::post('/logout', [MagicLoginController::class, 'logout'])->name('logout');
});

Route::get('/intake', function (Request $request) {
    if ($request->has('key')) {
        $raw = (string) $request->query('key', '');
        if ($raw === '') {
            // expliciet leeg → sessie wissen
            session()->forget('ak');
        } else {
            $ak = AccessKey::where('key', $raw)->first();
            if ($ak && $ak->isUsable()) {
                session([
                    'ak' => [
                        'id'       => $ak->id,
                        'key'      => $ak->key,
                        'package'  => $ak->package,
                        'duration' => $ak->duration_weeks,
                    ],
                ]);
            } else {
                session()->forget('ak');
            }
        }
    } else {
        // ⚠️ Belangrijk: bezoek zonder ?key wist de eerder ingestelde key
        session()->forget('ak');
    }

    return view('intake.index');
})->name('intake.index');
Route::post('/intake/checkout', [CheckoutController::class, 'create'])->name('intake.checkout');
Route::post('/intake/checkout/confirm', [CheckoutController::class, 'confirm'])->name('intake.checkout.confirm');
Route::post('/intake/progress', [CheckoutController::class, 'progress'])->name('intake.progress');
