<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ClientThreadController;
use App\Http\Controllers\CoachClientController;
use App\Http\Controllers\CoachClientTodoController;
use App\Http\Controllers\CoachThreadController;
use App\Http\Controllers\MagicLoginController;
use App\Http\Controllers\CoachPlanningController;
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
        Route::get('/threads',                             [CoachThreadController::class, 'index'])->name('threads.index');
        Route::get('/threads/{thread}',                    [CoachThreadController::class, 'show'])->name('threads.show');
        Route::post('/threads/{thread}/msg',               [CoachThreadController::class, 'storeMessage'])->name('threads.messages.store');
        Route::get('/clients',                             [CoachClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/{client}',                    [CoachClientController::class, 'show'])->name('clients.show');
        Route::get('/clients/{client}/intake',             [CoachClientController::class, 'intake'])->name('clients.intake');
        Route::get('/clients/{client}/planning/create',    [CoachPlanningController::class, 'create'])->name('clients.planning.create')->whereNumber('client');
        Route::post('/clients/{client}/planning/generate', [CoachPlanningController::class, 'generate'])->name('clients.planning.generate')->whereNumber('client');
        Route::get('/claim-clients',                       [CoachClientController::class, 'claim'])->name('clients.claim');
        Route::post('/claim-clients/{profile}',            [CoachClientController::class, 'claimStore'])->name('clients.claim.store');

        Route::prefix('clients/{client}/todos')->name('clients.todos.')->group(function () {
            Route::post('/',            [CoachClientTodoController::class, 'store'])->name('store');           // taak toevoegen
            Route::patch('/{todo}/toggle', [CoachClientTodoController::class, 'toggle'])->name('toggle');     // afvinken/undo
            Route::delete('/{todo}',    [CoachClientTodoController::class, 'destroy'])->name('destroy');       // verwijderen
            Route::patch('/reorder',    [CoachClientTodoController::class, 'reorder'])->name('reorder');       // sorteren
            Route::patch('/{todo}', [CoachClientTodoController::class, 'update'])->name('update');
        });
    });
    Route::prefix('client')->name('client.')->middleware('role:client')->group(function () {
        Route::get('/', fn () => view('client.index'))->name('index');
        Route::get('/threads',                [ClientThreadController::class, 'index'])->name('threads.index');
        Route::get('/threads/create',         [ClientThreadController::class, 'create'])->name('threads.create');
        Route::post('/threads',               [ClientThreadController::class, 'store'])->name('threads.store');
        Route::get('/threads/{thread}',       [ClientThreadController::class, 'show'])->name('threads.show');
        Route::post('/threads/{thread}/msg',  [ClientThreadController::class, 'storeMessage'])->name('threads.messages.store');
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
