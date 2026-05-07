<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingPlanTemplate extends Model
{
    protected $fillable = ['name', 'slug', 'level', 'injury_type', 'max_days', 'weeks'];

    public function items()
    {
        return $this->hasMany(TrainingPlanTemplateItem::class, 'template_id')
            ->orderBy('week')
            ->orderBy('day')
            ->orderBy('sort_order');
    }

    /**
     * Find the best matching template based on level, injury type and days per week.
     *
     * Selectie-logica:
     * - beginner + knie-blessure → knee templates
     * - beginner + geen knie     → general templates
     * - gevorderd                → gevorderd templates (geen knie-variant)
     * - expert                   → expert templates (geen knie-variant)
     * Elk met 4-5 dagen of 6 dagen variant.
     */
    public static function findBestMatch(?string $level, ?string $injuries, int $maxDaysPerWeek): ?self
    {
        $level = $level ?: 'beginner';

        if ($level === 'beginner') {
            $hasKneeInjury = $injuries && str_contains(mb_strtolower($injuries), 'knie');
            $injuryType = $hasKneeInjury ? 'knee' : null;
        } else {
            // Gevorderd/Expert hebben geen knie-variant
            $injuryType = null;
        }

        $maxDays = $maxDaysPerWeek >= 6 ? 6 : 5;

        // Gevorderd/Expert: max_days 5 → 4 (die templates gebruiken 4 i.p.v. 5)
        if (in_array($level, ['gevorderd', 'expert']) && $maxDays === 5) {
            $maxDays = 4;
        }

        // Try exact match first
        $template = self::where('level', $level)
            ->where('injury_type', $injuryType)
            ->where('max_days', $maxDays)
            ->first();

        if ($template) {
            return $template;
        }

        // Fallback: match on level + injury_type only (other max_days variant)
        return self::where('level', $level)
            ->where('injury_type', $injuryType)
            ->first();
    }
}
