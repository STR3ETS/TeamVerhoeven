<?php

namespace App\Support;

use App\Models\User;

class Intake
{
    /**
     * Bepaalt of deze user nog verplicht intake moet afronden.
     * Checkt of er een voltooide intake in de intakes tabel bestaat.
     */
    public static function requires(User $user): bool
    {
        // Check of de user een voltooide intake heeft in de intakes tabel
        $hasCompletedIntake = \App\Models\Intake::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->exists();
        
        // Als er geen voltooide intake is, moet de user de intake nog doen
        if (!$hasCompletedIntake) {
            return true;
        }

        // Extra checks op basis van client profile data
        $profile = $user->clientProfile;
        if (!$profile) {
            return true;
        }

        $missingStep0 = empty($user->name)
            || empty($user->email)
            || empty($profile->phone_e164);

        $missingCoach      = empty($profile->coach_id);
        $missingAnthro     = empty($profile->height_cm) || empty($profile->weight_kg);
        $missingGoals      = empty($profile->goals);
        $missingSessions   = empty($profile->max_days_per_week) || empty($profile->session_minutes);

        return $missingStep0
            || $missingCoach
            || $missingAnthro
            || $missingGoals
            || $missingSessions;
    }
}
