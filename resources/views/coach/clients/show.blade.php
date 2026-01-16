@extends('layouts.app')
@section('title', 'Cliënt: ' . ($client->name ?? 'onbekend'))

@section('content')
@php
  $card  = 'p-5 bg-white rounded-3xl border border-gray-300';
  $muted = 'text-gray-500';
@endphp
  <a href="{{ route('coach.clients.index') }}" class="text-xs text-black font-semibold opacity-50 hover:opacity-100 transition duration-300"><i class="fa-solid fa-arrow-right-long fa-flip-horizontal fa-sm mr-2"></i> Terug naar overzicht</a>
  <h1 class="text-2xl font-bold mb-2 mt-1">Gegevens van {{ $client->name }}</h1>
  <p class="text-sm text-black opacity-80 font-medium mb-10">Bekijk de gegevens van {{ $client->name }}.</p>
  {{-- Bestaande top-kaarten --}}
  <div class="grid md:grid-cols-2 gap-6 mb-6">
    <div class="{{ $card }}">
      <h2 class="text-sm text-black font-semibold opacity-50 mb-2">Persoon</h2>
      <dl class="space-y-2">
        @php
          $address = $client->clientProfile->address
              ? (is_array($client->clientProfile->address)
                  ? $client->clientProfile->address
                  : json_decode($client->clientProfile->address, true))
              : null;
        @endphp
        <div class="flex justify-between gap-4 text-sm text-gray-600 font-semibold"><dt class="{{ $muted }}">Naam</dt><dd class="text-right">{{ $client->name }}</dd></div>
        <div class="flex justify-between gap-4 text-sm text-gray-600 font-semibold"><dt class="{{ $muted }}">E-mail</dt><dd class="text-right">{{ $client->email }}</dd></div>
        <div class="flex justify-between gap-4 text-sm text-gray-600 font-semibold"><dt class="{{ $muted }}">Telefoonnummer</dt><dd class="text-right">{{ $client->clientProfile->phone_e164 ?? '—' }}</dd></div>
        <div class="flex justify-between gap-4 text-sm text-gray-600 font-semibold"><dt class="{{ $muted }}">Geboortedatum</dt><dd class="text-right">{{ $birthdate ? $birthdate->format('Y-m-d') : 'Onbekend' }}</dd></div>
        <div class="flex justify-between gap-4 text-sm text-gray-600 font-semibold"><dt class="{{ $muted }}">Leeftijd</dt><dd class="text-right">{{ $ageYears !== null ? $ageYears . ' jaar' : 'Onbekend' }}</dd></div>
        <div class="flex justify-between gap-4 text-sm text-gray-600 font-semibold"><dt class="{{ $muted }}">Adres</dt><dd class="text-right">{{ $address['street'] ?? '' }} {{ $address['house_number'] ?? '' }}, {{ $address['postcode'] ?? '' }}</dd></div>
      </dl>
    </div>
    
    <div class="{{ $card }}">
      <h2 class="text-sm text-black font-semibold opacity-50 mb-2">Gegevens uit intakeformulier</h2>
      <dl class="space-y-2">
        <div class="flex justify-between gap-4 text-sm text-gray-600 font-semibold"><dt class="{{ $muted }}">Lengte</dt><dd class="text-right">{{ $client->clientProfile->height_cm ? (int) $client->clientProfile->height_cm . ' cm' : '—' }}</dd></div>
        <div class="flex justify-between gap-4 text-sm text-gray-600 font-semibold"><dt class="{{ $muted }}">Gewicht</dt><dd class="text-right">{{ $client->clientProfile->weight_kg ?? '—' }} kg</dd></div>
        <div class="flex justify-between gap-4 text-sm text-gray-600 font-semibold"><dt class="{{ $muted }}">Cooper test resultaat</dt><dd class="text-right">
          @if($distance12min) {{ number_format($distance12min, 0, ',', '.') }} m @else Niet ingevuld @endif
        </dd></div>
        <div class="flex justify-between gap-4 text-sm text-gray-600 font-semibold"><dt class="{{ $muted }}">Rustpols</dt><dd class="text-right">
          @if(!is_null($hrRest)) {{ $hrRest }} bpm @else Niet ingevuld @endif
        </dd></div>
        <div class="flex justify-between gap-4 text-sm text-gray-600 font-semibold"><dt class="{{ $muted }}">HF-max</dt><dd class="text-right">
          @if(!is_null($hrMax)) {{ $hrMax }} bpm @else Niet ingevuld @endif
        </dd></div>
        <a href="{{ route('coach.clients.intake', $client) }}" class="text-xs text-black font-semibold opacity-50 hover:opacity-100 transition duration-300">Alle intakegegevens inzien <i class="fa-solid fa-arrow-right-long fa-sm ml-2"></i></a>
      </dl>
    </div>
  </div>

  {{-- ===== HIER BEGINT JOUW NIEUWE MODULE (styling ongemoeid gelaten) ===== --}}
  @php
    use App\Models\ClientProfile;

    // Gebruik het $profile dat de controller al meegeeft (NIET auth()->id()).
    // Robuuste parsing van heartrate (array, json-string of "max|rest" string).
    $hrMaxCalc = null;
    $hrRestCalc = null;

    $raw = $profile?->heartrate ?? null;

    if (is_array($raw)) {
        $hrMaxCalc  = $raw['max']      ?? $raw['hr_max']  ?? null;
        $hrRestCalc = $raw['resting']  ?? $raw['rest']    ?? $raw['hr_rest'] ?? null;
    } elseif (is_string($raw)) {
        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $hrMaxCalc  = $decoded['max']      ?? $decoded['hr_max']  ?? null;
            $hrRestCalc = $decoded['resting']  ?? $decoded['rest']    ?? $decoded['hr_rest'] ?? null;
        } elseif (preg_match('/^\s*(\d{2,3})\s*[\|\/,;\-]\s*(\d{2,3})\s*$/', $raw, $m)) {
            $hrMaxCalc  = (int) $m[1];
            $hrRestCalc = (int) $m[2];
        }
    }

    $hrMaxCalc  = $hrMaxCalc  ?? ($profile->hr_max_bpm  ?? null);
    $hrRestCalc = $hrRestCalc ?? ($profile->rest_hr_bpm ?? null);

    $hrMaxCalc  = $hrMaxCalc  !== null ? (int) $hrMaxCalc  : null;
    $hrRestCalc = $hrRestCalc !== null ? (int) $hrRestCalc : null;

    $hrRes = ($hrMaxCalc !== null && $hrRestCalc !== null) ? ($hrMaxCalc - $hrRestCalc) : null;

    $percentages = range(50, 100, 5);
    $zones = [];
    if ($hrRes !== null) {
        foreach ($percentages as $p) {
            $zones[$p] = (int) round($hrRestCalc + $hrRes * ($p / 100));
        }
    }

    // ==== Cooper uit test_12min (JSON)  ====================================
    // Verwacht vorm: {"meters": 2800}
    $cooperMeters = null;
    $rawCooper = $profile->test_12min ?? null;

    if (is_array($rawCooper)) {
        $cooperMeters = $rawCooper['meters'] ?? null;
    } elseif (is_string($rawCooper) && trim($rawCooper) !== '') {
        $dec = json_decode($rawCooper, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($dec)) {
            $cooperMeters = $dec['meters'] ?? null;
        }
    }

    // Fallback zodat UI altijd iets toont (optioneel)
    if (!$cooperMeters || $cooperMeters < 1000) {
        $cooperMeters = 3000;
    }

    // ==== Pace en factoren ==================================================
    $paceSecPerKm = (12 * 60) / ($cooperMeters / 1000); // sec/km uit 12-min test

    $distances = [200, 400, 600, 800, 1000, 1200];
    // Excel-factoren
    $baseFactors = [];
    foreach ($distances as $d) {
        $baseFactors[$d] = 0.207 * ($d / 200);   // 200m=0.207, 400m=0.414, 600m=0.621, 800m=0.828, 1000m=1.035, 1200m=1.242
    }

    // Bandpercentages voor VAN→TOT (zoals je sheet)
    // intensiteit 4: 100% → 91%   | intensiteit 5: 91% → 86.5%
    $bands = [1.00, 0.91, 0.865];

    // Formatter 00:mm:ss
    $fmt = function (float $seconds): string {
        $seconds = (int) round($seconds);
        $m = intdiv($seconds, 60); $s = $seconds % 60;
        return sprintf('00:%02d:%02d', $m, $s);
    };

    $intervalRows = [
        4 => [ 'label' => 'Extensive Interval Training', 'bandFrom' => 0, 'bandTo' => 1 ],
        5 => [ 'label' => 'Intensive Interval Training', 'bandFrom' => 1, 'bandTo' => 2 ],
    ];

    // Splits per intensiteit + afstand
    $tijden = [];
    foreach ($intervalRows as $intensity => $cfg) {
        foreach ($distances as $d) {
            $van = $paceSecPerKm * $baseFactors[$d] * $bands[$cfg['bandFrom']];
            $tot = $paceSecPerKm * $baseFactors[$d] * $bands[$cfg['bandTo']];
            $tijden[$intensity][$d] = ['van'=>$fmt($van), 'tot'=>$fmt($tot)];
        }
    }

    // Polsslag-ranges op basis van eerder berekende $zones
    $hrRange = [
        4 => ['van' => $zones[85] ?? null, 'tot' => $zones[95]  ?? null],
        5 => ['van' => $zones[95] ?? null, 'tot' => $zones[100] ?? null],
    ];

    // Mapping zoals in je sheet
    $trainingRows = [
        ['name' => 'Recovery',     'int' => 1, 'from' => 60, 'to' => 70,  'breath' => 'Nauwelijks versneld',                       'rating' => 'Licht',     'tempo' => 'Rustig'],
        ['name' => 'LSD',          'int' => 2, 'from' => 70, 'to' => 80,  'breath' => 'Licht versneld, kan nog makkelijk praten',  'rating' => 'Normaal',   'tempo' => 'Gemiddeld'],
        ['name' => 'Pace/Tempo',   'int' => 3, 'from' => 80, 'to' => 90,  'breath' => 'Versneld, praten wordt moeilijk',           'rating' => 'Zwaar',     'tempo' => 'Vlot'],
        ['name' => 'Interval',     'int' => 4, 'from' => 85, 'to' => 95,  'breath' => 'Hijgen, praten zeer moeilijk',              'rating' => 'Zeer Zwaar','tempo' => 'Snel'],
        ['name' => 'High Intens',  'int' => 5, 'from' => 95, 'to' => 100, 'breath' => 'Zwaar hijgen, praten onmogelijk',           'rating' => 'Maximaal',  'tempo' => 'Hard'],
    ];
  @endphp

  <h2 class="text-lg font-bold mb-2">Takenlijst</h2>
  @include('coach.clients.partials.todos', ['client' => $client])

  @php
    use Carbon\Carbon;

    // Week-setup (één keer centraal)
    $profile    = $client->clientProfile;
    $totalWeeks = max(1, (int)($profile->period_weeks ?? 1));
    $week       = (int) request('week', 1);
    if ($week < 1 || $week > $totalWeeks) { $week = 1; }

    // Start van het trainingsplan:
    // 1) als je ooit een plan_start_date-kolom toevoegt aan client_profiles → eerst daarop
    // 2) anders, pak de laatste intake.start_date (als die gevuld is)
    // 3) fallback: huidige week maandag
    if (!empty($profile?->plan_start_date)) {
        $planStart = Carbon::parse($profile->plan_start_date)->startOfWeek(Carbon::MONDAY);
    } else {
        $latestIntake = $client->intakes()->orderByDesc('start_date')->first();
        if ($latestIntake && $latestIntake->start_date) {
            $planStart = Carbon::parse($latestIntake->start_date)->startOfWeek(Carbon::MONDAY);
        } else {
            $planStart = Carbon::now()->startOfWeek(Carbon::MONDAY);
        }
    }

    // Huidige trainingsweek uitrekenen
    $currentStart = $planStart->copy()->addWeeks($week - 1);
    $currentEnd   = $currentStart->copy()->endOfWeek(Carbon::SUNDAY);
    $currentCalW  = $currentStart->isoWeek; // kalenderweek
  @endphp
  <h2 class="text-lg font-bold mb-1">Planning</h2>
  <p class="text-xs text-black opacity-60 font-semibold mb-3">
    Week {{ $week }}
    · KW {{ $currentCalW }}
    · {{ $currentStart->format('d-m') }} t/m {{ $currentEnd->format('d-m-Y') }}
  </p>
<div id="planning-root" data-current-week="{{ $week }}">
  @php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Schema;

    // Heeft assignments-tabel een 'week' kolom?
    $hasWeekCol  = Schema::hasColumn('training_assignments', 'week');

    // Day labels + volgorde
    $daysMap = [
      'mon' => 'Maandag','tue' => 'Dinsdag','wed' => 'Woensdag',
      'thu' => 'Donderdag','fri' => 'Vrijdag','sat' => 'Zaterdag','sun' => 'Zondag',
    ];

    // Phase-rank zoals in je planner (server-side)
    $phaseRank = function (?string $phaseLike) {
        $p = Str::lower($phaseLike ?? '');
        if (Str::contains($p, 'warm')) return 0;
        if (Str::contains($p, ['activ','mobil'])) return 1;
        if (Str::contains($p, ['main','run','hyrox','streng','erg','core'])) return 2;
        if (Str::contains($p, 'finish')) return 3;
        if (Str::contains($p, 'cool')) return 4;
        return 2;
    };

    // Laad assignments incl. volledige kaartstructuur (voor identieke render)
    $assignments = \App\Models\TrainingAssignment::query()
        ->where('user_id', $client->id)
        ->when($hasWeekCol, fn($q) => $q->where('week', $week))
        ->with(['card.section','card.blocks.items'])
        ->orderByRaw("FIELD(day,'mon','tue','wed','thu','fri','sat','sun')")
        ->orderBy('sort_order')
        ->get();

    // Groepeer per dag
    $byDay = $assignments->groupBy('day');

    // Helper: gesorteerde items per dag (phase-rank -> sort_order -> id)
    $sortedForDay = function ($key) use ($byDay, $phaseRank) {
        return ($byDay[$key] ?? collect())->sort(function($a, $b) use ($phaseRank) {
            $aPhase = optional($a->card?->section)->name;
            $bPhase = optional($b->card?->section)->name;
            $ra = $phaseRank($aPhase);
            $rb = $phaseRank($bPhase);

            // tuple-vergelijk: phase rank, sort_order, id
            return [$ra, (int)($a->sort_order ?? 0), (int)$a->id]
                <=> [$rb, (int)($b->sort_order ?? 0), (int)$b->id];
        });
    };
  @endphp

  {{-- Week selector - scrollbaar bij veel weken --}}
  <div class="mb-3 max-h-32 overflow-y-auto">
    <div class="flex flex-wrap gap-2">
    @for($w=1; $w <= $totalWeeks; $w++)
      <a href="{{ route('coach.clients.show', [$client, 'week' => $w]) }}"
        class="px-3 py-1 rounded-full text-xs border {{ $week === $w ? 'bg-black text-white border-black' : 'bg-white text-black/70 border-gray-300 hover:bg-gray-50' }}"
        data-week-link
        data-week="{{ $w }}">
        Week {{ $w }}
      </a>
    @endfor
    </div>
  </div>

  @if($assignments->isEmpty())
    {{-- Lege staat voor geselecteerde week --}}
    <div class="p-5 bg-white rounded-3xl border border-gray-300 mb-6">
      <div class="flex flex-col gap-4 items-start">
        <p class="text-sm text-gray-500">
          Nog geen planning gemaakt voor
          {{ $totalWeeks > 1 ? 'week ' . $week : '' }} <span class="block md:hidden font-semibold"><br>Trainingen kunnen alleen gemaakt worden via de computer</span>
        </p>
        <a href="{{ route('coach.clients.trainingplan', [$client, 'week' => $week]) }}"
          class="hidden md:block w-full sm:w-auto px-4 py-3 bg-[#c8ab7a] hover:bg-[#a38b62] transition duration-300 text-white font-medium text-sm rounded">
          Planning maken
        </a>
      </div>
    </div>
  @else
    {{-- Schema met identieke kaart-opmaak als bibliotheek + server-side sorting --}}
    <div class="p-5 bg-white rounded-3xl border border-gray-300 mb-6">
      <div class="flex items-start justify-between gap-4 mb-4">
        <div class="text-sm text-black font-semibold opacity-50">
          Huidig schema (week {{ $week }}) <span class="block md:hidden font-semibold"><br>Trainingen kunnen alleen bewerkt worden via de computer</span>
        </div>
        <a href="{{ route('coach.clients.trainingplan', [$client, 'week' => $week]) }}"
          class="hidden md:block w-full sm:w-auto px-4 py-3 bg-[#c8ab7a] hover:bg-[#a38b62] transition duration-300 text-white font-medium text-sm rounded">
          Bewerken
        </a>
      </div>

      {{-- Scrollbare container voor de dagplanning --}}
      <div class="max-h-[60vh] overflow-y-auto">
        @foreach($daysMap as $key => $label)
          <div class="mb-4">
            <h3 class="text-sm font-semibold mb-2 opacity-50">{{ $label }}</h3>
            <div class="flex flex-col gap-2">
              @forelse($sortedForDay($key) as $a)
                @php $card = $a->card; @endphp

                @if($card)
                  {{-- IDENTIEK aan bibliotheekkaart (zonder drag) --}}
                  <div class="p-5 bg-white rounded-3xl border border-gray-300 group">
                    <h2 class="text-sm font-semibold mb-3 flex items-center gap-2">
                      {{ $card->title }}
                    </h2>

                    @foreach($card->blocks as $block)
                      <div class="{{ !$loop->first ? 'mt-4' : '' }}">
                        <span class="text-[10px] {{ $block->badge_classes }} font-semibold px-2 py-1 rounded">
                          {{ $block->label }}
                        </span>
                        <ul class="text-xs text-black font-medium mt-2 flex flex-col gap-2">
                          @foreach($block->items as $item)
                            <li class="flex items-center justify-between">
                              <span class="max-w-[50%]">{!! $item->left_html !!}</span>
                              @if(!empty($item->right_text))
                                <span class="text-gray-500 font-semibold">{{ $item->right_text }}</span>
                              @endif
                            </li>
                          @endforeach
                        </ul>
                      </div>
                    @endforeach
                  </div>
                @else
                  {{-- Fallback als kaart ontbreekt --}}
                  <div class="p-5 bg-white rounded-3xl border border-gray-300">
                    <div class="text-sm font-semibold mb-1">Training #{{ $a->training_card_id }}</div>
                    <div class="text-xs text-gray-500">Kaart niet gevonden</div>
                  </div>
                @endif
              @empty
                <div class="p-5 bg-gray-100 rounded-3xl border border-gray-200 text-xs text-gray-500 italic">
                  Geen training
                </div>
              @endforelse
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif
</div>

    <h2 class="text-lg font-bold mb-2">Informatie</h2>
  <section class="grid gap-4 grid-cols-1 mb-8">
    <div class="p-5 bg-white rounded-3xl border border-gray-300">
      <div class="text-sm text-black font-semibold opacity-50 mb-2">Overzicht te realiseren tijden bij intervaltraining</div>
      <div class="overflow-x-auto rounded-2xl border border-gray-200">
        <table class="min-w-[2000px] w-full text-sm">
          <thead class="bg-gray-50 text-gray-600">
            <tr>
              <th class="px-3 py-2 text-left">Soort</th>
              <th class="px-3 py-2 text-center">Intensiteit</th>
              <th class="px-3 py-2 text-center" colspan="2">Polsslag</th>
              @foreach ($distances as $d)
              <th class="px-3 py-2 text-center" colspan="2">{{ $d }} m</th>
              @endforeach
              <th class="px-3 py-2 text-left"></th>
            </tr>
            <tr class="text-xs">
              <th class="px-3 py-1"></th>
              <th class="px-3 py-1"></th>
              <th class="px-3 py-1">Van</th>
              <th class="px-3 py-1">Tot</th>
              @foreach ($distances as $d)
              <th class="px-3 py-1">Van</th>
              <th class="px-3 py-1">Tot</th>
              @endforeach
              <th class="px-3 py-1"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @foreach ([4,5] as $i)
            <tr>
              <td class="px-3 py-2 font-medium whitespace-nowrap">{{ $intervalRows[$i]['label'] }}</td>
              <td class="px-3 py-2 flex justify-center">
                <span class="min-w-5 min-h-5 flex items-center justify-center rounded-full text-gray-600 font-bold bg-gray-300">
                  {{ $i }}
                </span>
              </td>
              <td class="px-3 py-2 text-[#c8ab7a] font-medium">{{ $hrRange[$i]['van'] ?? '—' }}</td>
              <td class="px-3 py-2 text-[#c8ab7a] font-medium">{{ $hrRange[$i]['tot'] ?? '—' }}</td>
              
              @foreach ($distances as $d)
              <td class="px-3 py-2 font-semibold tabular-nums">{{ $tijden[$i][$d]['van'] }}</td>
              <td class="px-3 py-2 font-semibold tabular-nums">{{ $tijden[$i][$d]['tot'] }}</td>
              @endforeach
              <td class="px-3 py-2">sec</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    
    <div class="p-5 bg-white rounded-3xl border border-gray-300">
      <div class="text-sm text-black font-semibold opacity-50 mb-2">Hartslagzones</div>
      <div class="overflow-x-auto rounded-2xl border border-gray-200 mb-4">
        <table class="min-w-[780px] w-full text-sm">
          <thead class="bg-gray-50 text-gray-700">
            <tr>
              <th class="px-3 py-2 text-left">Training</th>
              <th class="px-3 py-2 text-center">Intensiteit</th>
              <th class="px-3 py-2 text-right">Van</th>
              <th class="px-3 py-2 text-left">Tot</th>
              <th class="px-3 py-2 w-10"></th>
              <th class="px-3 py-2 text-left">Ademhaling</th>
              <th class="px-3 py-2 text-left">Beoordeling</th>
              <th class="px-3 py-2 text-left">Tempo</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @foreach ($trainingRows as $row)
            @php
            $fromPct = $row['from'];
            $toPct   = $row['to'];
            $fromHr  = $zones[$fromPct] ?? '—';
            $toHr    = $zones[$toPct]   ?? '—';
            @endphp
            <tr>
              <td class="px-3 py-2 font-medium">{{ $row['name'] }}</td>
              <td class="px-3 py-2 flex justify-center items-center">
                <span class="min-w-5 min-h-5 flex items-center justify-center rounded-full text-gray-600 font-bold bg-gray-300 pr-0.25">{{ $row['int'] }}</span>
              </td>
              
              <td class="px-3 py-2 text-[#c8ab7a] font-medium text-right">{{ $fromHr }}</td>
              <td class="px-3 py-2 text-[#c8ab7a] font-medium">{{ $toHr }}</td>

              <td class="px-3 py-2 text-gray-400"></td>
              
              <td class="px-3 py-2 text-black/80">{{ $row['breath'] }}</td>
              <td class="px-3 py-2 text-black/80">{{ $row['rating'] }}</td>
              <td class="px-3 py-2 text-black/80">{{ $row['tempo'] }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      @php
      $n = count($percentages);
      $lastIndex = max(1, $n - 1);
      @endphp
      
      <div class="col-span-11 relative">
        <div
        class="h-4 rounded-full"
        style="
            background: linear-gradient(
              to right,
              #15803d 0%,
              #16a34a 9.09%,
              #22c55e 18.18%,
              #4ade80 27.27%,
              #facc15 36.36%,
              #eab308 45.45%,
              #f97316 54.54%,
              #ea580c 63.63%,
              #ef4444 72.72%,
              #dc2626 81.81%,
              #dc2626 100%
              );
              "
        ></div>
        
@foreach ($percentages as $i => $p)
    @php
        $left = ($i / $lastIndex) * 100;
        // transform + align
        $transform = 'translateX(-50%)';
        $align     = 'text-center';
        if ($i === 0) { $transform = 'translateX(0)'; $align = 'text-left'; }
        if ($i === $lastIndex) { $transform = 'translateX(-100%)'; $align = 'text-right'; }

        // Verberg op mobiel om de 5% (alle “oneven” indexen) -> effectief 10%-stappen op mobiel
        $hideOnMobile = ($i % 2) === 1; // 50,55,60,... -> index 0,1,2,... verberg index 1,3,5,...
        $tickVisibilityClass  = $hideOnMobile ? 'hidden sm:block' : ''; // streepje zichtbaarheid
        $labelVisibilityClass = $hideOnMobile ? 'hidden sm:block' : ''; // label zichtbaarheid
    @endphp

    {{-- streepje --}}
    <span
        class="absolute top-2 -translate-y-1/2 w-px h-3 bg-white/50 {{ $tickVisibilityClass }}"
        style="left: {{ $left }}%"
        aria-hidden="true"
    ></span>

    {{-- label --}}
    <div
        class="absolute top-4 mt-2 {{ $align }} leading-tight {{ $labelVisibilityClass }}"
        style="left: {{ $left }}%; transform: {{ $transform }};"
    >
        <div class="text-black text-[13px] font-semibold">
            {{ $p }}<span class="pl-0.5">%</span>
        </div>
        <div class="text-[#c8ab7a] text-[13px] font-medium">
            {{ $zones[$p] ?? '—' }}
        </div>
    </div>
@endforeach

      
      <div class="h-12"></div>
    </div>
  </div>
</section>

@push('scripts')
@verbatim
<script>
(function () {
  const rootId = 'planning-root';

  function bindWeekLinks(scope) {
    const container = scope || document;
    container.querySelectorAll('a[data-week-link]').forEach(a => {
      a.addEventListener('click', function (e) {
        // Ctrl/cmd-click of middelklik laat nieuwe tab gewoon toe
        if (e.ctrlKey || e.metaKey || e.shiftKey || e.button === 1) return;
        e.preventDefault();
        loadPlanning(a.href, { push: true });
      }, { once: true }); // na replace binden we opnieuw, dus once voorkomt dubbele handlers
    });
  }

  async function loadPlanning(url, { push = false, replace = false } = {}) {
    const current = document.getElementById(rootId);
    if (!current) { window.location.href = url; return; }

    // kleine visual cue (optional maar fijn)
    current.style.opacity = '0.6';

    try {
      const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (!res.ok) throw new Error('Network response was not ok');
      const html = await res.text();
      const doc  = new DOMParser().parseFromString(html, 'text/html');
      const fresh = doc.getElementById(rootId);
      if (!fresh) { window.location.href = url; return; }

      // vervang uitsluitend het planning-deel
      current.replaceWith(fresh);

      // url & history bijwerken
      if (push)      history.pushState({}, '', url);
      else if (replace) history.replaceState({}, '', url);

      // week-links opnieuw binden binnen het nieuwe fragment
      bindWeekLinks(fresh);

      // scrollpositie behouden: geen scroll-to-top
    } catch (err) {
      // failover: doe gewone navigatie
      window.location.href = url;
    } finally {
      const node = document.getElementById(rootId);
      if (node) node.style.opacity = '1';
    }
  }

  // init: week-links binden
  bindWeekLinks(document);

  // back/forward ondersteunt
  window.addEventListener('popstate', function () {
    loadPlanning(location.href, { push: false, replace: true });
  });
})();
</script>
@endverbatim
@endpush
@endsection
