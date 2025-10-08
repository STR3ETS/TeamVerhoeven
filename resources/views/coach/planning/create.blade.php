@extends('layouts.app')
@section('title', 'Bestaande gegevens – ' . ($client->name ?? 'Cliënt'))

@section('content')
<a href="{{ route('coach.clients.show', $client) }}"
   class="text-xs text-black font-semibold opacity-50 hover:opacity-100 transition duration-300">
  <i class="fa-solid fa-arrow-right-long fa-flip-horizontal fa-sm mr-2"></i>
  Terug naar cliënt
</a>

<h1 class="text-2xl font-bold mb-2 mt-1">Bekende gegevens van {{ $client->name }}</h1>
<p class="text-sm text-black opacity-80 font-medium mb-6">
  Hieronder vind je alle reeds bekende gegevens uit het intake- en profielbestand van deze cliënt.
</p>

<!-- @php
    $p = $client->clientProfile ?? null;
    $rows = [];

    $decode = function ($val) {
        if (is_array($val)) return $val;
        if (is_string($val) && trim($val) !== '') {
            $decoded = json_decode($val, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }
        return null;
    };

    if ($p) {
        // Persoonlijke gegevens
        if ($p->birthdate) $rows['Geboortedatum'] = \Carbon\Carbon::parse($p->birthdate)->format('d-m-Y');
        if ($p->gender) $rows['Geslacht'] = $p->gender === 'm' ? 'Man' : ($p->gender === 'f' ? 'Vrouw' : $p->gender);

        $address = $decode($p->address);
        if ($address) {
            $rows['Adres'] = trim(($address['street'] ?? '').' '.($address['house_number'] ?? '').', '.($address['postcode'] ?? ''));
        }

        if ($p->phone_e164) $rows['Telefoon'] = $p->phone_e164;

        // Fysieke gegevens
        if ($p->height_cm) $rows['Lengte'] = (int)$p->height_cm . ' cm';
        if ($p->weight_kg) $rows['Gewicht'] = (int)$p->weight_kg . ' kg';

        // Trainingsparameters
        if ($p->period_weeks) $rows['Periode'] = $p->period_weeks . ' weken';

        $freq = $decode($p->frequency);
        if ($freq) {
            $rows['Frequentie'] = ($freq['sessions_per_week'] ?? '—') . '×/week • ' .
                                  ($freq['minutes_per_session'] ?? '—') . ' min/sessie';
        }

        // Doelen
        $goals = $decode($p->goals);
        if ($goals && count($goals)) $rows['Doelen'] = implode(', ', $goals);

        $goal = $decode($p->goal);
        if ($goal) {
            $tmp = [];
            if (!empty($goal['distance'])) $tmp[] = $goal['distance'];
            if (!empty($goal['date'])) $tmp[] = $goal['date'];
            if (!empty($goal['time_hms'])) $tmp[] = $goal['time_hms'];
            if ($tmp) $rows['HYROX-target'] = implode(' • ', $tmp);
        }

        // Prestaties
        $ftp = $decode($p->ftp);
        if ($ftp) {
            $rows['FTP'] = ($ftp['wkg'] ?? '—').' w/kg • '.($ftp['watt'] ?? '—').' watt';
        }

        $hr = $decode($p->heartrate);
        if ($hr) {
            $rows['Hartslag'] = 'Max '.$hr['max'].' bpm • Rust '.$hr['resting'].' bpm';
        }

        $cooper = $decode($p->test_12min);
        if ($cooper) $rows['Cooper-test'] = ($cooper['meters'] ?? '—').' meter';

        $t5k = $decode($p->test_5k);
        if ($t5k) {
            $rows['5K-test'] = ($t5k['minutes'] ?? '—').':'.($t5k['seconds'] ?? '—');
        }

        // Overige context
        if ($p->background) $rows['Achtergrond'] = $p->background;
        if ($p->facilities) $rows['Faciliteiten'] = $p->facilities;
        if ($p->materials) $rows['Materiaal'] = $p->materials;

        $inj = $decode($p->injuries);
        if ($inj && count($inj)) $rows['Blessures'] = implode(', ', $inj);
    }
@endphp

@if ($p)
    <div class="p-5 bg-white rounded-3xl border border-gray-300">
        <h2 class="text-sm text-black font-semibold opacity-50 mb-2">Bestaande gegevens</h2>
        <dl class="grid grid-cols-2 gap-x-18">
            @foreach ($rows as $label => $value)
                <div class="flex justify-between py-2 text-sm">
                    <dt class="text-sm text-gray-600 font-semibold">{{ $label }}</dt>
                    <dd class="text-sm text-gray-500 font-medium">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
@else
    <div class="p-5 bg-white rounded-3xl border border-gray-300 text-sm text-gray-500">
        Geen profielgegevens gevonden voor deze cliënt.
    </div>
@endif -->

<div class="p-5 bg-white rounded-3xl border border-gray-300">
  <h2 class="text-sm text-black font-semibold opacity-50 mb-3">Planning genereren</h2>

  @php
      $periodWeeks = $client->clientProfile->period_weeks ?? 12;

      $todayObj = \Illuminate\Support\Carbon::today();
      $firstAllowed = $todayObj->isMonday()
          ? $todayObj->toDateString()
          : $todayObj->next(1)->toDateString(); // eerstvolgende maandag
  @endphp

    <div class="grid sm:grid-cols-6 gap-4">
        {{-- Extra instructies voor de bot --}}
        <div class="sm:col-span-6">
            <label class="text-sm font-medium text-black mb-1 block">Extra instructies voor de bot</label>
            <textarea name="extra_input_bot" rows="3"
                class="w-full rounded-xl border border-gray-300 hover:border-gray-400 transition p-3 text-sm focus:outline-none focus:border-[#c8ab7a]"
                placeholder="Bijv: Week 4 en 5 focus op endurance; extra aandacht voor sled push techniek."></textarea>
        </div>
        {{-- Periode (read-only) --}}
        <div class="col-span-2">
            <label class="text-sm font-medium text-black mb-1 block">Periode uit keuze pakket</label>
            <input type="number" value="{{ $periodWeeks }}" disabled
                    class="w-full bg-gray-50 text-gray-700 rounded-xl border border-gray-200 p-3 text-sm cursor-not-allowed select-none">
            <input type="hidden" name="period_weeks" value="{{ $periodWeeks }}">
        </div>
        {{-- Startdatum: alleen maandagen en nooit vóór vandaag --}}
        <div class="col-span-2">
            <label class="text-sm font-medium text-black mb-1 block">Startdatum</label>
            <input
                id="start-date"
                type="date"
                name="start_date"
                value="{{ $firstAllowed }}"
                min="{{ $firstAllowed }}"
                step="7"
                class="w-full rounded-xl border border-gray-300 hover:border-gray-400 transition p-3 text-sm focus:outline-none focus:border-[#c8ab7a]"
            >
        </div>
        <a href="{{ route('coach.clients.planning.create', $client) }}" class="col-span-2 mt-auto h-fit sm:w-auto px-4 py-3 bg-[#c8ab7a] hover:bg-[#a38b62] transition duration-300 text-white font-medium text-sm rounded text-center mb-[0.1rem]">
            Genereren
        </a>
    </div>
</div>

<script>
  // Extra guard: als iemand handmatig typt, corrigeer naar dichtstbijzijnde geldige maandag ≥ min
  (function () {
    const input = document.getElementById('start-date');
    if (!input) return;

    const minDate = new Date(input.min + 'T00:00:00');

    function toMondayOnOrAfter(date) {
      const d = new Date(date.getTime());
      const day = d.getDay(); // 0=zo,1=ma,...6=za
      const add = (day === 1) ? 0 : ( (8 - day) % 7 );
      d.setDate(d.getDate() + add);
      return d;
    }

    function ymd(d) {
      const m = String(d.getMonth() + 1).padStart(2, '0');
      const day = String(d.getDate()).padStart(2, '0');
      return d.getFullYear() + '-' + m + '-' + day;
    }

    function sanitize() {
      if (!input.value) return;
      const typed = new Date(input.value + 'T00:00:00');

      // onder min? -> set naar min (die al een maandag is)
      if (typed < minDate) {
        input.value = input.min;
        return;
      }

      // geen maandag? -> corrigeer naar eerstvolgende maandag (kan dezelfde dag zijn)
      if (typed.getDay() !== 1) {
        const corrected = toMondayOnOrAfter(typed);
        input.value = ymd(corrected);
      }
    }

    input.addEventListener('change', sanitize);
    input.addEventListener('blur', sanitize);
  })();
</script>

@endsection
