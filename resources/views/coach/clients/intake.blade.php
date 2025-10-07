@extends('layouts.app')
@section('title', 'Intakegegevens: ' . ($client->name ?? 'onbekend'))

@section('content')
@php
  $card  = 'p-5 bg-white rounded-3xl border border-gray-300';
  $muted = 'text-gray-500';

  // ---------- Helpers ----------
  $parseArr = function($v) {
      if (is_array($v)) return $v;
      if (is_string($v) && trim($v) !== '') {
          $d = json_decode($v, true);
          if (json_last_error() === JSON_ERROR_NONE && is_array($d)) return $d;
      }
      return [];
  };
  $isAssoc = function(array $arr) {
      return array_keys($arr) !== range(0, count($arr) - 1);
  };
  $fmtDec = function($v, $unit = '', $decimals = 1) {
      if ($v === null || $v === '') return '—';
      $n = (float) $v;
      $s = rtrim(rtrim(number_format($n, $decimals, ',', ''), '0'), ',');
      return $s . ($unit ? ' ' . $unit : '');
  };
  $kvList = function(array $arr) use ($muted) {
      if (empty($arr)) { echo '<div class="text-sm text-gray-600">—</div>'; return; }

      // Geneste helper voor één regel "label : value"
      $line = function ($label, $value) use ($muted) {
          if (is_bool($value))   $value = $value ? 'Ja' : 'Nee';
          if (is_array($value))  $value = implode(', ', array_map(fn($v) => is_scalar($v) ? (string)$v : json_encode($v, JSON_UNESCAPED_UNICODE), $value));
          if ($value === null || $value === '') $value = '—';
          $value = e((string)$value);
          echo "<div class=\"flex justify-between gap-4\"><dt class=\"{$muted}\">{$label}</dt><dd class=\"text-right\">{$value}</dd></div>";
      };

      // Assoc: toon key/value-rijen. Indexed: toon bullets.
      $isAssoc = array_keys($arr) !== range(0, count($arr) - 1);
      if (!$isAssoc) {
          echo '<ul class="space-y-1 italic">';
          foreach ($arr as $v) {
              if (is_array($v)) {
                  // nested array → compact tonen
                  echo '<li class="text-sm text-gray-700 font-semibold">';
                  echo implode(', ', array_map(fn($kv, $vv) => ucfirst(str_replace(['_','-'], ' ', (string)$kv)).': '.(is_scalar($vv)?$vv:json_encode($vv, JSON_UNESCAPED_UNICODE)), array_keys($v), $v));
                  echo '</li>';
              } else {
                  echo '<li class="text-sm text-gray-700 font-semibold">'.e((string)$v).'</li>';
              }
          }
          echo '</ul>';
          return;
      }

      echo '<dl class="space-y-2 text-sm text-gray-700 font-semibold">';
      foreach ($arr as $k => $v) {
          $label = ucfirst(str_replace(['_','-'], ' ', (string)$k));
          $line($label, $v);
      }
      echo '</dl>';
  };
  $fmtHms = function ($v): string {
      $sec = null;
      if (is_numeric($v)) {
          $sec = (int)$v;
      } elseif (is_string($v)) {
          if (preg_match('/^\s*(\d{1,2}):(\d{2}):(\d{2})\s*$/', $v, $m)) {
              $sec = $m[1]*3600 + $m[2]*60 + $m[3];
          } elseif (preg_match('/^\s*(\d{1,3}):(\d{2})\s*$/', $v, $m)) {
              $sec = $m[1]*60 + $m[2];
          }
      }
      if ($sec === null) return is_string($v) && $v !== '' ? $v : '—';
      $min = intdiv($sec, 60);
      $s   = $sec % 60;
      return sprintf('%02d:%02d', $min, $s);
  };
@endphp

<a href="{{ route('coach.clients.show', $client) }}" class="text-xs text-black font-semibold opacity-50 hover:opacity-100 transition duration-300">
  <i class="fa-solid fa-arrow-right-long fa-flip-horizontal fa-sm mr-2"></i> Terug naar cliënt
</a>

<h1 class="text-2xl font-bold mb-2 mt-1">Intakegegevens van {{ $client->name }}</h1>
<p class="text-sm text-black opacity-80 font-medium mb-8">Volledig overzicht van het profiel en de ingevulde intake.</p>

<div class="grid md:grid-cols-2 gap-6 mb-6">

  {{-- Persoon --}}
  <div class="{{ $card }}">
    <h2 class="text-sm text-black font-semibold opacity-50 mb-2">Persoon</h2>
    @php
      $address = $parseArr($profile->address);
    @endphp
    <dl class="space-y-2 text-sm text-gray-700 font-semibold">
      <div class="flex justify-between gap-4">
        <dt class="{{ $muted }}">Naam</dt><dd class="text-right">{{ $client->name }}</dd>
      </div>
      <div class="flex justify-between gap-4">
        <dt class="{{ $muted }}">E-mail</dt><dd class="text-right">{{ $client->email }}</dd>
      </div>
      <div class="flex justify-between gap-4">
        <dt class="{{ $muted }}">Telefoon</dt><dd class="text-right">{{ $profile->phone_e164 ?? '—' }}</dd>
      </div>
      <div class="flex justify-between gap-4">
        <dt class="{{ $muted }}">Geboortedatum</dt><dd class="text-right">{{ $birthdate ? $birthdate->format('Y-m-d') : '—' }}</dd>
      </div>
      <div class="flex justify-between gap-4">
        <dt class="{{ $muted }}">Leeftijd</dt><dd class="text-right">{{ $ageYears !== null ? $ageYears . ' jaar' : '—' }}</dd>
      </div>
      <div class="flex justify-between gap-4">
        <dt class="{{ $muted }}">Adres</dt>
        <dd class="text-right">
          {{ ($address['street'] ?? '') . ' ' . ($address['house_number'] ?? '') }}{{ isset($address['postcode']) ? ', ' . $address['postcode'] : '' }}
        </dd>
      </div>
      <div class="flex justify-between gap-4">
        <dt class="{{ $muted }}">Geslacht</dt><dd class="text-right">
          @php
            $g = $profile->gender ?? null;
            echo $g === 'm' ? 'Man' : ($g === 'f' ? 'Vrouw' : '—');
          @endphp
        </dd>
      </div>
    </dl>
  </div>

  {{-- Metingen & hartslag --}}
  <div class="grid grid-cols-1 gap-6">
    <div class="{{ $card }}">
        <h2 class="text-sm text-black font-semibold opacity-50 mb-2">Lengte & Gewicht</h2>
        @php
        $heartrate = $parseArr($profile->heartrate);
        $hrRest = $heartrate['resting'] ?? $heartrate['rest'] ?? $heartrate['hr_rest'] ?? null;
        $hrMax  = $heartrate['max'] ?? $heartrate['hr_max'] ?? null;
        // fallback kolommen als je ze hebt; anders blijven ze null en tonen we '—'
        $hrRest = $hrRest ?? ($profile->rest_hr_bpm ?? null);
        $hrMax  = $hrMax  ?? ($profile->hr_max_bpm ?? null);
        @endphp
        <dl class="space-y-2 text-sm text-gray-700 font-semibold">
        <div class="flex justify-between gap-4">
            <dt class="{{ $muted }}">Lengte</dt><dd class="text-right">{{ $profile->height_cm !== null ? (int) $profile->height_cm . ' cm' : '—' }}</dd>
        </div>
        <div class="flex justify-between gap-4">
            <dt class="{{ $muted }}">Gewicht</dt><dd class="text-right">{{ $fmtDec($profile->weight_kg, 'kg') }}</dd>
        </div>
        </dl>
    </div>
    <div class="{{ $card }}">
        <h2 class="text-sm text-black font-semibold opacity-50 mb-2">Hartslag</h2>
        @php
        $heartrate = $parseArr($profile->heartrate);
        $hrRest = $heartrate['resting'] ?? $heartrate['rest'] ?? $heartrate['hr_rest'] ?? null;
        $hrMax  = $heartrate['max'] ?? $heartrate['hr_max'] ?? null;
        // fallback kolommen als je ze hebt; anders blijven ze null en tonen we '—'
        $hrRest = $hrRest ?? ($profile->rest_hr_bpm ?? null);
        $hrMax  = $hrMax  ?? ($profile->hr_max_bpm ?? null);
        @endphp
        <dl class="space-y-2 text-sm text-gray-700 font-semibold">
        <div class="flex justify-between gap-4">
            <dt class="{{ $muted }}">Rustpols</dt><dd class="text-right">{{ $hrRest !== null ? $hrRest . ' bpm' : '—' }}</dd>
        </div>
        <div class="flex justify-between gap-4">
            <dt class="{{ $muted }}">HF-max</dt><dd class="text-right">{{ $hrMax !== null ? $hrMax . ' bpm' : '—' }}</dd>
        </div>
        </dl>
    </div>
  </div>

{{-- Testresultaten --}}
<div class="{{ $card }} md:col-span-2">
  <h2 class="text-sm text-black font-semibold opacity-50 mb-2">Testresultaten</h2>

  @php
    // Kleine, lokale helpers (vallen alleen binnen dit blok)
    $parse = function($v) {
      if (is_array($v)) return $v;
      if (is_string($v) && trim($v) !== '') {
        $d = json_decode($v, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($d)) return $d;
      }
      return [];
    };

    // "HH:MM:SS" / "MM:SS" / seconden -> "MM:SS"
    $fmtHms = function ($v): string {
      $sec = null;
      if (is_numeric($v)) {
        $sec = (int)$v;
      } elseif (is_string($v)) {
        if (preg_match('/^\s*(\d{1,2}):(\d{2}):(\d{2})\s*$/', $v, $m)) {
          $sec = $m[1]*3600 + $m[2]*60 + $m[3];
        } elseif (preg_match('/^\s*(\d{1,3}):(\d{2})\s*$/', $v, $m)) {
          $sec = $m[1]*60 + $m[2];
        }
      }
      if ($sec === null) return ($v === null || $v === '') ? '—' : (string)$v;
      $min = intdiv($sec, 60); $s = $sec % 60;
      return sprintf('%02d:%02d', $min, $s);
    };

    $timeFromTest = function(array $t) use ($fmtHms) {
      // combineer hours/minutes/seconds of pak time_hms/time/duration/result
      if (isset($t['hours']) || isset($t['minutes']) || isset($t['seconds'])) {
        $sec = (int)($t['hours'] ?? 0) * 3600 + (int)($t['minutes'] ?? 0) * 60 + (int)($t['seconds'] ?? 0);
        return $fmtHms($sec);
      }
      $cand = $t['time_hms'] ?? $t['time'] ?? $t['duration'] ?? $t['result'] ?? null;
      return $fmtHms($cand);
    };

    $metersFromTest = function(array $t) {
      if (isset($t['meters']))     return number_format((float)$t['meters'], 0, ',', '.') . ' m';
      if (isset($t['distance_m'])) return number_format((float)$t['distance_m'], 0, ',', '.') . ' m';
      if (isset($t['distance_km'])) return rtrim(rtrim(number_format((float)$t['distance_km']*1000, 0, ',', '.'), '0'), ',') . ' m';
      return '—';
    };

    // --- Parse ruwe kolommen naar arrays
    $t12  = $parse($profile->test_12min ?? null);
    $t5k  = $parse($profile->test_5k ?? null);
    $t10k = $parse($profile->test_10k ?? null);
    $t42k = $parse($profile->test_marathon ?? null);

    // --- Samengevatte waarden
    $cooperResult = $metersFromTest($t12);
    $time5k       = $timeFromTest($t5k);
    $time10k      = $timeFromTest($t10k);
    $time42k      = $timeFromTest($t42k);
  @endphp

  <dl class="space-y-2 text-sm text-gray-700 font-semibold">
    <div class="flex justify-between gap-4">
      <dt class="{{ $muted }}">Cooper test resultaat</dt>
      <dd class="text-right">{{ $cooperResult }}</dd>
    </div>
    <div class="flex justify-between gap-4">
      <dt class="{{ $muted }}">5 km test resultaat</dt>
      <dd class="text-right">{{ $time5k }}</dd>
    </div>
    <div class="flex justify-between gap-4">
      <dt class="{{ $muted }}">10 km test resultaat</dt>
      <dd class="text-right">{{ $time10k }}</dd>
    </div>
    <div class="flex justify-between gap-4">
      <dt class="{{ $muted }}">Marathon test resultaat</dt>
      <dd class="text-right">{{ $time42k }}</dd>
    </div>
  </dl>
</div>


@php
  $goals = $parseArr($profile->goals);   // vrije lijst (["Hyrox Nijmegen", "10 km sub 45"])
  $goal  = $parseArr($profile->goal);    // hoofddoel ({"date":"2026-07-26","distance":"HYROX_PRO","time_hms":"01:15:00", ...})

  $distanceMap = [
    'HYROX_PRO'  => 'HYROX Pro',
    'HYROX_OPEN' => 'HYROX Open',
    '5K'         => '5 km',
    '10K'        => '10 km',
    '21K'        => 'Halve marathon (21,1 km)',
    '42K'        => 'Marathon (42,2 km)',
  ];

  // Afgeleide/pretty waarden voor hoofddoel
  $goalDate = null; $goalDist = null; $goalTime = null; $goalEvent = null; $goalNote = null;

  if (!empty($goal)) {
    if (!empty($goal['date'])) {
      try { $goalDate = \Carbon\Carbon::parse($goal['date'])->format('d-m-Y'); }
      catch (\Throwable $e) { $goalDate = $goal['date']; }
    }
    if (!empty($goal['distance'])) {
      $goalDist = $distanceMap[$goal['distance']] ?? (string)$goal['distance'];
    }
    if (!empty($goal['time_hms'])) {
      // naar MM:SS (of laat HH:MM:SS staan als je dat liever hebt)
      $goalTime = $fmtHms($goal['time_hms']);
      // evt naar HH:MM:SS-look:
      if (preg_match('/^\d{2}:\d{2}$/', $goalTime)) $goalTime = '00:' . $goalTime;
    }
    $goalEvent = $goal['event']   ?? $goal['race'] ?? $goal['city'] ?? null;
    $goalNote  = $goal['note']    ?? $goal['notes'] ?? null;
  }
@endphp
{{-- Doelen --}}
<div class="{{ $card }}">
  <h2 class="text-sm text-black font-semibold opacity-50 mb-2">Doelen</h2>
  {{-- Opgegeven doelen (vrije lijst) --}}
  <div class="mb-3">
    @if (!empty($goals))
      <ul class="list-disc pl-5 space-y-1 text-sm text-gray-700 font-semibold">
        @foreach ($goals as $g)
          <li>{{ is_scalar($g) ? e($g) : e(json_encode($g, JSON_UNESCAPED_UNICODE)) }}</li>
        @endforeach
      </ul>
    @else
      <div class="text-sm text-gray-600">—</div>
    @endif
  </div>
</div>
<div class="{{ $card }}">
  <h2 class="text-sm text-black font-semibold opacity-50 mb-2">Doelwedstrijd</h2>
  {{-- Hoofddoel --}}
  <div>
    @if ($goalDate || $goalDist || $goalTime || $goalEvent || $goalNote)
      <dl class="space-y-2 text-sm text-gray-700 font-semibold">
        @if ($goalDate)
          <div class="flex justify-between gap-4">
            <dt class="{{ $muted }}">Datum</dt><dd class="text-right">{{ $goalDate }}</dd>
          </div>
        @endif
        @if ($goalDist)
          <div class="flex justify-between gap-4">
            <dt class="{{ $muted }}">Afstand</dt><dd class="text-right">{{ $goalDist }}</dd>
          </div>
        @endif
        @if ($goalTime)
          <div class="flex justify-between gap-4">
            <dt class="{{ $muted }}">Streeftijd</dt><dd class="text-right">{{ $goalTime }}</dd>
          </div>
        @endif
        @if ($goalEvent)
          <div class="flex justify-between gap-4">
            <dt class="{{ $muted }}">Event</dt><dd class="text-right">{{ e($goalEvent) }}</dd>
          </div>
        @endif
        @if ($goalNote)
          <div class="flex justify-between gap-4">
            <dt class="{{ $muted }}">Opmerking</dt><dd class="text-right">{{ e($goalNote) }}</dd>
          </div>
        @endif
      </dl>
    @else
      <div class="text-sm text-gray-600">—</div>
    @endif
  </div>
</div>

{{-- Blessures / Aandachtspunten --}}
<div class="{{ $card }}">
  <h2 class="text-sm text-black font-semibold opacity-50 mb-2">Blessures / Aandachtspunten</h2>
  @php $injuries = $parseArr($profile->injuries); @endphp
  @if (!empty($injuries))
    <ul class="list-disc pl-5 space-y-1 text-sm text-gray-700 font-semibold">
      @foreach ($injuries as $i)
        <li>{{ is_scalar($i) ? e($i) : e(json_encode($i, JSON_UNESCAPED_UNICODE)) }}</li>
      @endforeach
    </ul>
  @else
    <div class="text-sm text-gray-600">—</div>
  @endif
</div>

{{-- Frequentie & Periode --}}
<div class="{{ $card }}">
  <h2 class="text-sm text-black font-semibold opacity-50 mb-2">Frequentie & Periode</h2>
  @php
    $frequency = $parseArr($profile->frequency); // bijv: {"sessions_per_week":3,"minutes_per_session":45}
    $sessionsPerWeek   = $frequency['sessions_per_week'] ?? $frequency['per_week'] ?? null;
    $minutesPerSession = $frequency['minutes_per_session'] ?? $frequency['session_minutes'] ?? $frequency['minutes'] ?? null;
  @endphp

  <dl class="space-y-2 text-sm text-gray-700 font-semibold">
    <div class="flex justify-between gap-4">
      <dt class="{{ $muted }}">Periode</dt>
      <dd class="text-right">{{ $profile->period_weeks ?? '—' }} wkn</dd>
    </div>
    <div class="flex justify-between gap-4">
      <dt class="{{ $muted }}">Sessies</dt>
      <dd class="text-right">{{ $sessionsPerWeek ?? '—' }} p/w</dd>
    </div>
    <div class="flex justify-between gap-4">
      <dt class="{{ $muted }}">Minuten per sessie</dt>
      <dd class="text-right">
        {{ $minutesPerSession !== null && $minutesPerSession !== '' ? $minutesPerSession . ' min' : '—' }}
      </dd>
    </div>
  </dl>
</div>


  {{-- Achtergrond --}}
  <div class="{{ $card }}">
    <h2 class="text-sm text-black font-semibold opacity-50 mb-2">Achtergrond</h2>
    <div class="text-sm text-gray-700 font-semibold whitespace-pre-line">{{ $profile->background ?: '—' }}</div>
  </div>

  {{-- Faciliteiten & Materialen --}}
  <div class="{{ $card }}">
    <h2 class="text-sm text-black font-semibold opacity-50 mb-2">Faciliteiten & Materialen</h2>
    <div class="mb-3">
      <div class="{{ $muted }} text-xs font-semibold mb-1">Faciliteiten</div>
      <div class="text-sm text-gray-700 font-semibold whitespace-pre-line">{{ $profile->facilities ?: '—' }}</div>
    </div>
    <div>
      <div class="{{ $muted }} text-xs font-semibold mb-1">Materialen</div>
      <div class="text-sm text-gray-700 font-semibold whitespace-pre-line">{{ $profile->materials ?: '—' }}</div>
    </div>
  </div>

  {{-- Werkuren / Beschikbaarheid --}}
  <div class="{{ $card }}">
    <h2 class="text-sm text-black font-semibold opacity-50 mb-2">Werkuren / Beschikbaarheid</h2>
    <div class="text-sm text-gray-700 font-semibold whitespace-pre-line">{{ $profile->work_hours ?: '—' }}</div>
  </div>

{{-- FTP --}}
<div class="{{ $card }}">
  <h2 class="text-sm text-black font-semibold opacity-50 mb-2">FTP</h2>
  @php
    // Kan TEXT met plain waarde zijn, of JSON/array.
    $ftpRaw = $profile->ftp;
    $ftpArr = $parseArr($ftpRaw); // gebruikt je helper bovenin de view
  @endphp

  @if(!empty($ftpArr))
    {{-- Toon als bullets (of key-value als assoc array) --}}
    {!! $kvList($ftpArr) !!}
  @else
    <div class="text-sm text-gray-700 font-semibold">
      {{ is_string($ftpRaw) && trim($ftpRaw) !== '' ? $ftpRaw : '—' }}
    </div>
  @endif
</div>

  {{-- Coach voorkeur --}}
  <div class="{{ $card }}">
    <h2 class="text-sm text-black font-semibold opacity-50 mb-2">Coach voorkeur</h2>
    <div class="text-sm text-gray-700 font-semibold">
      @php
        $pref = $profile->coach_preference ?? 'none';
        echo $pref === 'none' ? 'Geen voorkeur' : ucfirst($pref);
      @endphp
    </div>
  </div>

</div>
@endsection
