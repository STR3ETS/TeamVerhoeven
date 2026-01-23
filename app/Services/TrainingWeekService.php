<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;

class TrainingWeekService
{
    /**
     * Normaliseer de startdatum naar de eerstvolgende maandag.
     */
    public function normalizeStartMonday(Carbon|string $startDate): Carbon
    {
        $start = $startDate instanceof Carbon
            ? $startDate->copy()
            : Carbon::parse($startDate);

        if ($start->isMonday()) {
            return $start->startOfDay();
        }

        return $start->next(Carbon::MONDAY)->startOfDay();
    }

    /**
     * Bepaal period segments (12/24) in chronologische volgorde.
     */
    public function periodSegmentsForUser(User $user): array
    {
        $segments = Order::query()
            ->where('client_id', $user->id)
            ->where('status', 'paid')
            ->orderBy('paid_at')
            ->orderBy('created_at')
            ->pluck('period_weeks')
            ->map(fn ($weeks) => (int) $weeks)
            ->filter(fn ($weeks) => $weeks > 0)
            ->values()
            ->all();

        if (empty($segments)) {
            $fallback = (int) ($user->clientProfile?->period_weeks ?? 12);
            return [$fallback > 0 ? $fallback : 12];
        }

        return $segments;
    }

    /**
     * Aantal gap-weken vóór de gekozen UI-week.
     */
    public function calculateGapWeeks(int $selectedWeek, array $periodSegments): int
    {
        $gapWeeks = 0;
        $totalWeeks = 0;

        foreach ($periodSegments as $segmentWeeks) {
            $totalWeeks += (int) $segmentWeeks;
            if ($selectedWeek > $totalWeeks) {
                $gapWeeks++;
                continue;
            }
            break;
        }

        return $gapWeeks;
    }

    /**
     * Bereken start- en einddatum voor een UI-week met gap-weken.
     *
     * @return array{0: Carbon, 1: Carbon, 2: int}
     */
    public function getWeekDates(Carbon|string $subscriptionStartDate, int $selectedWeek, array $periodSegments): array
    {
        $startMonday = $this->normalizeStartMonday($subscriptionStartDate);
        $gapWeeks = $this->calculateGapWeeks($selectedWeek, $periodSegments);

        $weekStart = $startMonday->copy()->addWeeks($selectedWeek - 1 + $gapWeeks);
        $weekEnd = $weekStart->copy()->addDays(6);

        return [$weekStart, $weekEnd, $gapWeeks];
    }

    /**
     * Formatteer de week header voor de UI.
     */
    public function formatWeekHeader(Carbon|string $subscriptionStartDate, int $selectedWeek, array $periodSegments): string
    {
        [$weekStart, $weekEnd] = $this->getWeekDates($subscriptionStartDate, $selectedWeek, $periodSegments);
        $calWeek = $weekStart->isoWeek();

        return sprintf(
            'Week %d · KW %d · %s t/m %s',
            $selectedWeek,
            $calWeek,
            $weekStart->format('d-m'),
            $weekEnd->format('d-m-Y')
        );
    }
}
