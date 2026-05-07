<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingPlanTemplate extends Model
{
    protected $fillable = ['name', 'slug', 'injury_type', 'max_days', 'weeks'];

    public function items()
    {
        return $this->hasMany(TrainingPlanTemplateItem::class, 'template_id')
            ->orderBy('week')
            ->orderBy('day')
            ->orderBy('sort_order');
    }

    /**
     * Find the best matching template based on injury type and days per week.
     */
    public static function findBestMatch(?string $injuries, int $maxDaysPerWeek): ?self
    {
        $hasKneeInjury = $injuries && str_contains(mb_strtolower($injuries), 'knie');
        $injuryType = $hasKneeInjury ? 'knee' : null;
        $maxDays = $maxDaysPerWeek >= 6 ? 6 : 5;

        // Try exact match first
        $template = self::where('injury_type', $injuryType)
            ->where('max_days', $maxDays)
            ->first();

        if ($template) {
            return $template;
        }

        // Fallback: match on injury_type only (other max_days variant)
        return self::where('injury_type', $injuryType)->first();
    }
}
