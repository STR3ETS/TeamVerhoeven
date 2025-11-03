<?php

namespace App\Support;

use App\Models\User;

class Intake
{
    /**
     * Bepaalt of deze user nog verplicht intake moet afronden.
     * Pas aan op basis van welke velden jij op de user opslaat.
     */
    public static function requires(User $user): bool
    {
        // Dit is een voorbeeld; koppel dit aan de velden die je
        // vanuit de intake opslaat op de users-tabel.

        $missingStep0 = empty($user->name)
            || empty($user->email)
            || empty($user->phone)
            || empty($user->dob)
            || empty($user->gender)
            || empty($user->street)
            || empty($user->house_number)
            || empty($user->postcode);

        $missingCoach      = empty($user->preferred_coach);
        $missingAnthro     = empty($user->height_cm) || empty($user->weight_kg);
        $missingGoals      = empty($user->goals);
        $missingSessions   = empty($user->max_days_per_week) || empty($user->session_minutes);
        $missingRace       = empty($user->goal_distance) || empty($user->goal_time_hms) || empty($user->goal_ref_date);
        $missingRunTests   = empty($user->cooper_meters) || empty($user->test_5k_pace);
        $missingHR         = empty($user->hr_estimate_from_age) && empty($user->hr_max_bpm);
        // FTP is optioneel → telt niet mee

        return is_null($user->intake_completed_at ?? null)
            || $missingStep0
            || $missingCoach
            || $missingAnthro
            || $missingGoals
            || $missingSessions
            || $missingRace
            || $missingRunTests
            || $missingHR;
    }
}
