<?php

namespace App\Services;

use App\Models\User;
use App\Models\Intake;
use Carbon\Carbon;

class SubscriptionStatusService
{
    /**
     * Get subscription status for a client user.
     * 
     * Returns an array with:
     * - 'status': 'active' | 'expiring_soon' | 'expired' | 'should_delete'
     * - 'ends_at': Carbon date when subscription ends
     * - 'days_remaining': int days until expiry (can be negative if expired)
     * - 'delete_at': Carbon date when account will be deleted (1 day after expiry)
     * - 'period_weeks': int the subscription period
     * 
     * Timeline:
     * - 7 days before ends_at: expiring_soon (non-blocking popup)
     * - On ends_at and 1 day after: expired (blocking popup)
     * - After 1 day past ends_at: should_delete (auto delete)
     */
    public static function getStatus(User $user): ?array
    {
        if ($user->role !== 'client') {
            return null;
        }

        // Get the latest intake with start_date
        $intake = Intake::where('client_id', $user->id)
            ->whereNotNull('start_date')
            ->orderByDesc('created_at')
            ->first();

        if (!$intake || !$intake->start_date) {
            return null;
        }

        // BELANGRIJK: Bij renewals worden weken opgeteld bij het profiel
        // Daarom halen we period_weeks ALTIJD uit het profiel (dat is de totale periode)
        // De intake payload bevat alleen de laatst gekozen periode, niet het totaal
        $profile = $user->clientProfile;
        $periodWeeks = $profile?->period_weeks ?? 12;

        $startDate = Carbon::parse($intake->start_date)->startOfDay();
        $endsAt = $startDate->copy()->addWeeks($periodWeeks)->endOfDay(); // End of the last day
        $deleteAt = $endsAt->copy()->addDay()->endOfDay(); // 1 day after expiry (end of that day)
        $now = Carbon::now();
        
        // Calculate days remaining (negative if past)
        $daysRemaining = (int) $now->startOfDay()->diffInDays($endsAt->copy()->startOfDay(), false);
        
        // Warning starts 7 days before end date
        $warningStartDate = $endsAt->copy()->subDays(7)->startOfDay();
        
        // Determine status based on new rules:
        // - expiring_soon: 7 days before ends_at until ends_at (non-blocking popup)
        // - expired: on ends_at day and 1 day after (blocking popup)
        // - should_delete: more than 1 day after ends_at (auto delete)
        
        if ($now->gt($deleteAt)) {
            $status = 'should_delete';
        } elseif ($now->gte($endsAt->copy()->startOfDay())) {
            // On or after the end date, but within grace period
            $status = 'expired';
        } elseif ($now->gte($warningStartDate)) {
            // Within 7 days of end date
            $status = 'expiring_soon';
        } else {
            $status = 'active';
        }

        return [
            'status' => $status,
            'starts_at' => $startDate,
            'ends_at' => $endsAt,
            'days_remaining' => $daysRemaining,
            'delete_at' => $deleteAt,
            'period_weeks' => (int) $periodWeeks,
            'warning_start' => $warningStartDate,
        ];
    }

    /**
     * Check if user needs to see the renewal popup.
     */
    public static function needsRenewalPopup(User $user): bool
    {
        $status = self::getStatus($user);
        
        if (!$status) {
            return false;
        }

        return in_array($status['status'], ['expiring_soon', 'expired']);
    }

    /**
     * Check if user's subscription has expired and popup should be blocking.
     */
    public static function isExpiredAndBlocking(User $user): bool
    {
        $status = self::getStatus($user);
        
        if (!$status) {
            return false;
        }

        return $status['status'] === 'expired';
    }

    /**
     * Check if user should be deleted (grace period passed without renewal).
     */
    public static function shouldDeleteAccount(User $user): bool
    {
        $status = self::getStatus($user);
        
        if (!$status) {
            return false;
        }

        return $status['status'] === 'should_delete';
    }
}
