<?php

namespace App\Services;

/**
 * UHV berekening op basis van:
 * - 12-min loop afstand in meters
 * - Rusthartslag (HFrest)
 * - Maximale hartslag (HFmax)
 *
 * Zones volgens jouw Excel:
 * Recovery: 50% - 65%
 * LSD: 70% - 75%
 * Pace/Tempo: 80% - 85%
 * Interval: 90%
 * HIT: 95%
 *
 * Formule: Karvonen
 * Doel-HF = HFrest + p * (HFmax - HFrest)
 */
class UhvCalculator
{
    /** @var int Afstand in meters in 12 minuten */
    protected int $distance12min_m;

    /** @var int Rusthartslag in bpm */
    protected int $hrRest;

    /** @var int Maximale hartslag in bpm */
    protected int $hrMax;

    /** @var int Hartslagreserve */
    protected int $hrReserve;

    public function __construct(int $distance12min_m, int $hrRest, int $hrMax)
    {
        $this->distance12min_m = max(0, $distance12min_m);
        $this->hrRest          = max(0, $hrRest);
        $this->hrMax           = max($this->hrRest, $hrMax);
        $this->hrReserve       = max(0, $this->hrMax - $this->hrRest);
    }

    public static function make(int $distance12min_m, int $hrRest, int $hrMax): self
    {
        return new self($distance12min_m, $hrRest, $hrMax);
    }

    /**
     * Bereken een bereik op basis van twee percentages van de HR-reserve.
     * @return array{min_bpm:int,max_bpm:int,pct_from:float,pct_to:float}
     */
    protected function calcRange(float $pctFrom, float $pctTo): array
    {
        $min = $this->hrRest + ($pctFrom * $this->hrReserve);
        $max = $this->hrRest + ($pctTo   * $this->hrReserve);

        return [
            'min_bpm'  => (int) round($min),
            'max_bpm'  => (int) round($max),
            'pct_from' => $pctFrom,
            'pct_to'   => $pctTo,
        ];
    }

    /**
     * Bereken een enkel doelpunt.
     * @return array{bpm:int,pct:float}
     */
    protected function calcPoint(float $pct): array
    {
        $val = $this->hrRest + ($pct * $this->hrReserve);

        return [
            'bpm' => (int) round($val),
            'pct' => $pct,
        ];
    }

    /**
     * Tabel met zones en placeholders voor tempo-tabellen.
     * De tijden voor 200 m, 400 m, etc. blijven leeg tot jij de formules geeft.
     */
    public function toArray(): array
    {
        $zones = [
            'recovery' => $this->calcRange(0.50, 0.65),
            'lsd'      => $this->calcRange(0.70, 0.75),
            'pace'     => $this->calcRange(0.80, 0.85),
            'interval' => $this->calcPoint(0.90),
            'hit'      => $this->calcPoint(0.95),
        ];

        // Labels en korte uitleg onder de zone, zoals in je Excel
        $zoneDescriptions = [
            'recovery' => 'Zeer rustig. Je kunt makkelijk praten. Focus op herstel.',
            'lsd'      => 'Lange rustige duur. Gesprek kan, ademhaling verhoogd.',
            'pace'     => 'Tempo. Kortere blokken, je kunt nog net zinnen maken.',
            'interval' => 'Krachtig interval. Weinig praten mogelijk.',
            'hit'      => 'Zeer intensief. Korte pieken.',
        ];

        // Placeholder voor tempo-tabellen per afstand
        $paceTables = [
            '200m'  => ['from_sec' => null, 'to_sec' => null],
            '400m'  => ['from_sec' => null, 'to_sec' => null],
            '600m'  => ['from_sec' => null, 'to_sec' => null],
            '800m'  => ['from_sec' => null, 'to_sec' => null],
            '1000m' => ['from_sec' => null, 'to_sec' => null],
        ];

        return [
            'inputs' => [
                'distance12min_m' => $this->distance12min_m,
                'hr_rest'         => $this->hrRest,
                'hr_max'          => $this->hrMax,
                'hr_reserve'      => $this->hrReserve,
            ],
            'zones' => [
                'recovery' => [
                    'min_bpm' => $zones['recovery']['min_bpm'],
                    'max_bpm' => $zones['recovery']['max_bpm'],
                    'label'   => $zoneDescriptions['recovery'],
                    'range'   => '50% - 65%',
                ],
                'lsd' => [
                    'min_bpm' => $zones['lsd']['min_bpm'],
                    'max_bpm' => $zones['lsd']['max_bpm'],
                    'label'   => $zoneDescriptions['lsd'],
                    'range'   => '70% - 75%',
                ],
                'pace' => [
                    'min_bpm' => $zones['pace']['min_bpm'],
                    'max_bpm' => $zones['pace']['max_bpm'],
                    'label'   => $zoneDescriptions['pace'],
                    'range'   => '80% - 85%',
                ],
                'interval' => [
                    'bpm'   => $zones['interval']['bpm'],
                    'label' => $zoneDescriptions['interval'],
                    'pct'   => '90%',
                ],
                'hit' => [
                    'bpm'   => $zones['hit']['bpm'],
                    'label' => $zoneDescriptions['hit'],
                    'pct'   => '95%',
                ],
            ],
            'pace_tables' => $paceTables,
        ];
    }
}
