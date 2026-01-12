<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\SubscriptionStatusService;
use App\Models\TrainingAssignment;
use App\Models\ClientProfile;
use App\Models\Intake;

use App\Models\User;

class CheckSubscriptionStatus
{
    /**
     * Handle an incoming request.
     * 
     * This middleware checks if a client's subscription has expired past the grace period
     * and automatically deletes their account if so.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'client') {
            return $next($request);
        }

        $status = SubscriptionStatusService::getStatus($user);

        if (!$status) {
            return $next($request);
        }

        // If subscription is past grace period, auto-delete the account
        if ($status['status'] === 'should_delete') {
            $userId = $user->id;
            
            // Eerst uitloggen
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            // Dan user verwijderen
            $this->deleteUserAccount($userId);
            
            return redirect('/')->with('status', 'Je account is automatisch verwijderd omdat je abonnement niet is verlengd.');
        }

        return $next($request);
    }

    /**
     * Delete the user account and all related data.
     */
    protected function deleteUserAccount(int $userId): void
    {
        DB::transaction(function () use ($userId) {
            // Delete training assignments (uses 'user_id')
            TrainingAssignment::where('user_id', $userId)->delete();
            
            // Delete intakes
            Intake::where('client_id', $userId)->delete();
            
            // Delete threads and messages (uses 'client_user_id')
            if (class_exists(\App\Models\Thread::class)) {
                $threads = \App\Models\Thread::where('client_user_id', $userId)->get();
                foreach ($threads as $thread) {
                    \App\Models\Message::where('thread_id', $thread->id)->delete();
                    $thread->delete();
                }
            }
            
            // Delete todo items (uses 'client_user_id')
            if (class_exists(\App\Models\ClientTodoItem::class)) {
                \App\Models\ClientTodoItem::where('client_user_id', $userId)->delete();
            }
            
            // Delete orders
            if (class_exists(\App\Models\Order::class)) {
                \App\Models\Order::where('client_id', $userId)->delete();
            }
            
            // Delete profile
            ClientProfile::where('user_id', $userId)->delete();
            
            // Finally delete user
            User::where('id', $userId)->delete();
        });
    }
}
