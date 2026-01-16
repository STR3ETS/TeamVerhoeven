<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Intake;
use App\Models\ClientProfile;
use App\Models\TrainingAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Show renewal page - redirects to intake with renewal mode.
     * User keeps: login credentials, coach preference
     * User resets: training plan, profile data (except coach_id, coach_preference)
     * 
     * Supports access key: /subscription/renew?key=ABC123
     */
    public function renew(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'client') {
            return redirect('/');
        }

        // Store renewal flag in session
        session(['renewal_mode' => true]);
        
        // Reset the popup dismissed flag (user is now actively renewing)
        session()->forget('renewal_popup_dismissed');

        // Check for access key and forward it
        $params = ['renewal' => 1];
        if ($request->has('key')) {
            $params['key'] = $request->input('key');
        }

        // Redirect to intake - the intake will skip personal info and coach choice
        return redirect()->route('intake.index', $params);
    }

    /**
     * Dismiss the renewal popup for this session.
     * The popup won't show again until the user logs out and back in.
     */
    public function dismissPopup(Request $request)
    {
        session(['renewal_popup_dismissed' => true]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Process the renewal after intake completion.
     * This updates subscription dates but preserves the training plan.
     */
    public function processRenewal(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'client') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        DB::transaction(function () use ($user) {
            // Training assignments worden NIET verwijderd bij renewal
            // De coach behoudt het bestaande trainingschema
            // De intake process zal de profile updaten met nieuwe data
            // en period_weeks worden opgeteld (niet vervangen)
        });

        // Clear all renewal-related session flags
        session()->forget('renewal_mode');
        session()->forget('renewal_popup_dismissed');
        session()->forget('ak');
        session()->forget('draft_intake_id');

        // Return JSON for AJAX calls
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Abonnement succesvol verlengd']);
        }

        return redirect()->route('client.index')->with('status', 'Je abonnement is succesvol verlengd!');
    }

    /**
     * Cancel subscription and delete account.
     */
    public function cancel(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'client') {
            return redirect('/');
        }

        $userId = $user->id;

        // Eerst uitloggen VOORDAT we data verwijderen
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Nu kunnen we veilig de user en alle gerelateerde data verwijderen
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

        return redirect('/')->with('status', 'Je account is verwijderd. Bedankt voor je deelname aan Team Verhoeven!');
    }
}
