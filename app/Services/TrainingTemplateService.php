<?php

namespace App\Services;

use App\Models\TrainingAssignment;
use App\Models\TrainingPlanTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TrainingTemplateService
{
    /**
     * Assign a standard training template to a user based on their intake data.
     *
     * Selectie-logica:
     * - injuries bevat "knie" → knie-template
     * - max_days_per_week >= 6 → 6-dagen template
     * - max_days_per_week <= 5 → 4-5 dagen template
     *
     * Assignments worden alleen aangemaakt als de user nog GEEN bestaande assignments heeft.
     */
    public static function assignForNewClient(User $user, array $intakePayload): bool
    {
        // Check of user al assignments heeft
        $existingCount = TrainingAssignment::where('user_id', $user->id)->count();
        if ($existingCount > 0) {
            Log::info('[training.template] user already has assignments, skipping', [
                'user_id' => $user->id,
                'existing_count' => $existingCount,
            ]);
            return false;
        }

        $injuries = $intakePayload['profile']['injuries'] ?? null;
        $maxDays = (int) ($intakePayload['profile']['max_days_per_week'] ?? 5);

        $template = TrainingPlanTemplate::findBestMatch($injuries, $maxDays);

        if (!$template) {
            Log::warning('[training.template] no template found', [
                'user_id' => $user->id,
                'injuries' => $injuries,
                'max_days' => $maxDays,
            ]);
            return false;
        }

        $items = $template->items;

        if ($items->isEmpty()) {
            Log::warning('[training.template] template has no items', [
                'user_id' => $user->id,
                'template_id' => $template->id,
                'template_slug' => $template->slug,
            ]);
            return false;
        }

        // Kopieer alle template items naar training_assignments
        $assignments = [];
        $now = now();

        foreach ($items as $item) {
            $assignments[] = [
                'user_id' => $user->id,
                'training_card_id' => $item->training_card_id,
                'week' => $item->week,
                'day' => $item->day,
                'sort_order' => $item->sort_order,
                'coach_notes' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Bulk insert voor performance
        foreach (array_chunk($assignments, 100) as $chunk) {
            TrainingAssignment::insert($chunk);
        }

        Log::info('[training.template] template assigned to user', [
            'user_id' => $user->id,
            'template_id' => $template->id,
            'template_slug' => $template->slug,
            'assignments_created' => count($assignments),
        ]);

        return true;
    }
}
