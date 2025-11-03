<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\MagicLoginController;

class EnsureIntakeComplete
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user) {
            // Hergebruik de helper in je MagicLoginController
            $ctrl = app(MagicLoginController::class);

            // LET OP: requiresIntake MOET public zijn (zie stap 2)
            if (method_exists($ctrl, 'requiresIntake') && $ctrl->requiresIntake($user)) {
                // Intake-pagina zelf doorlaten om loops te voorkomen
                if (
                    !$request->routeIs('intake.index') &&
                    !$request->is('intake/*')
                ) {
                    return redirect()
                        ->route('intake.index')
                        ->with('fill_missing', true);
                }
            }
        }

        return $next($request);
    }
}
