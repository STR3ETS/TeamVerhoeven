<?php

namespace App\Http\Controllers;

use App\Models\Intake;
use App\Models\WeeklyCheckin;
use App\Services\TrainingWeekService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WeeklyCheckinController extends Controller
{
    /**
     * Check of de client een check-in popup moet zien.
     * Alleen voor Elite (pakket_c) klanten.
     */
    public function check(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'client') {
            return response()->json(['show' => false]);
        }

        // Alleen Elite pakket
        $intake = Intake::where('client_id', $user->id)
            ->whereNotNull('start_date')
            ->orderByDesc('created_at')
            ->first();

        if (!$intake) {
            return response()->json(['show' => false]);
        }

        $package = $intake->payload['package'] ?? null;
        if ($package !== 'pakket_c') {
            return response()->json(['show' => false]);
        }

        // Bereken huidige trainingsweek
        $currentWeek = $this->getCurrentWeek($user, $intake);

        // Check-in gaat over de VORIGE week (je reflecteert op afgelopen week)
        $checkinWeek = $currentWeek - 1;
        if ($checkinWeek < 1) {
            return response()->json(['show' => false]);
        }

        // Check of al ingevuld voor vorige week
        $exists = WeeklyCheckin::where('user_id', $user->id)
            ->where('week_number', $checkinWeek)
            ->exists();

        // Bereken de datums van de check-in week zodat de modal ze kan tonen
        $trainingWeekService = app(TrainingWeekService::class);
        $startMonday = $trainingWeekService->normalizeStartMonday($intake->start_date);
        $periodSegments = $trainingWeekService->periodSegmentsForUser($user);
        [$weekStart, $weekEnd] = $trainingWeekService->getWeekDates($intake->start_date, $checkinWeek, $periodSegments);

        return response()->json([
            'show' => !$exists,
            'week_number' => $checkinWeek,
            'week_dates' => $weekStart->format('d-m') . ' t/m ' . $weekEnd->format('d-m'),
        ]);
    }

    /**
     * Sla de wekelijkse check-in op.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'week_number'        => ['required', 'integer', 'min:1'],
            'recovery'           => ['required', 'integer', 'min:1', 'max:10'],
            'recovery_note'      => ['nullable', 'string', 'max:100'],
            'training_feel'      => ['required', 'integer', 'min:1', 'max:10'],
            'training_feel_note' => ['nullable', 'string', 'max:100'],
            'sleep_quality'      => ['required', 'integer', 'min:1', 'max:10'],
            'sleep_quality_note' => ['nullable', 'string', 'max:100'],
            'stress'             => ['required', 'integer', 'min:1', 'max:10'],
            'stress_note'        => ['nullable', 'string', 'max:100'],
            'progression'        => ['required', 'integer', 'min:1', 'max:10'],
            'progression_note'   => ['nullable', 'string', 'max:100'],
            'soreness'           => ['required', 'integer', 'min:1', 'max:10'],
            'soreness_note'      => ['nullable', 'string', 'max:100'],
            'motivation'         => ['required', 'integer', 'min:1', 'max:10'],
            'motivation_note'    => ['nullable', 'string', 'max:100'],
        ]);

        $data['user_id'] = $user->id;

        WeeklyCheckin::updateOrCreate(
            ['user_id' => $user->id, 'week_number' => $data['week_number']],
            $data
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Bereken de huidige trainingsweek voor de klant.
     */
    private function getCurrentWeek($user, $intake): int
    {
        $trainingWeekService = app(TrainingWeekService::class);
        $startMonday = $trainingWeekService->normalizeStartMonday($intake->start_date);
        $now = Carbon::now()->startOfDay();

        if ($now->lt($startMonday)) {
            return 0;
        }

        $diffWeeks = $startMonday->diffInWeeks($now);
        $periodSegments = $trainingWeekService->periodSegmentsForUser($user);
        $totalWeeks = array_sum($periodSegments);

        // Account for gap weeks between segments
        $currentWeek = 0;
        $cumulativeReal = 0;
        $cumulativeTotal = 0;

        foreach ($periodSegments as $i => $segWeeks) {
            if ($i > 0) {
                $cumulativeTotal++; // gap week
            }
            for ($w = 1; $w <= $segWeeks; $w++) {
                if ($cumulativeTotal >= $diffWeeks) {
                    return $cumulativeReal + 1;
                }
                $cumulativeTotal++;
                $cumulativeReal++;
            }
        }

        // Voorbij de laatste week
        return min($cumulativeReal, $totalWeeks);
    }
}
