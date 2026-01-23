<?php

namespace Tests\Unit;

use App\Services\TrainingWeekService;
use Carbon\Carbon;
use Tests\TestCase;

class TrainingWeekServiceTest extends TestCase
{
    public function test_formats_week_header_with_gap_week(): void
    {
        $service = new TrainingWeekService();
        $startDate = Carbon::parse('2024-02-19');
        $segments = [12, 12];

        $header = $service->formatWeekHeader($startDate, 13, $segments);

        $this->assertSame('Week 13 · KW 21 · 20-05 t/m 26-05-2024', $header);
    }
}
