@auth
@if(auth()->user()->role === 'client')
{{-- Weekly Check-in Modal (alleen Elite / pakket_c) --}}
<div id="checkin-overlay" class="hidden fixed inset-0 z-[60] flex items-center justify-center">
  <div class="absolute inset-0 bg-black/50"></div>
  <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto p-6">
    <h2 class="text-lg font-bold mb-1">Wekelijkse check-in</h2>
    <p class="text-xs text-gray-500 mb-5">Check-in voor vorige week — Week <span id="checkin-week"></span> (<span id="checkin-week-dates"></span>)</p>

    <form id="checkin-form" class="flex flex-col gap-4">
      <input type="hidden" name="week_number" id="checkin-week-input">

      @php
        $questions = [
          ['key' => 'recovery',       'label' => 'Herstel / energie',       'low' => 'Volledig uitgeput',          'high' => 'Volledig hersteld'],
          ['key' => 'training_feel',  'label' => 'Trainingsgevoel',         'low' => 'Zwaar / moeizaam',           'high' => 'Sterk / soepel'],
          ['key' => 'sleep_quality',  'label' => 'Slaapkwaliteit',          'low' => 'Slecht geslapen',            'high' => 'Diepe, goede slaap'],
          ['key' => 'stress',         'label' => 'Stress / mentale belasting', 'low' => 'Extreem veel stress',     'high' => 'Volledig ontspannen'],
          ['key' => 'progression',    'label' => 'Progressiegevoel',        'low' => 'Totaal niet',                'high' => 'Duidelijke progressie'],
          ['key' => 'soreness',       'label' => 'Spierpijn / pijntjes',    'low' => 'Veel pijn',                  'high' => 'Geen klachten'],
          ['key' => 'motivation',     'label' => 'Motivatie',               'low' => 'Geen motivatie',             'high' => 'Extreem gemotiveerd'],
        ];
      @endphp

      @foreach($questions as $q)
        <div class="border border-gray-200 rounded-2xl p-3">
          <label class="block text-sm font-semibold mb-2">{{ $q['label'] }}</label>
          <div class="flex items-center gap-2 mb-1">
            <span class="text-[10px] text-gray-400 w-20 text-right shrink-0">{{ $q['low'] }}</span>
            <input type="range" name="{{ $q['key'] }}" min="1" max="10" value="5"
                   class="flex-1 accent-[#c8ab7a] h-2 checkin-slider" data-key="{{ $q['key'] }}">
            <span class="text-[10px] text-gray-400 w-20 shrink-0">{{ $q['high'] }}</span>
            <span class="text-sm font-bold text-[#c8ab7a] w-6 text-center checkin-value" data-for="{{ $q['key'] }}">5</span>
          </div>
          <input type="text" name="{{ $q['key'] }}_note" maxlength="100"
                 class="w-full mt-1 px-3 py-1.5 text-xs border border-gray-200 rounded-xl placeholder-gray-400 focus:outline-none focus:border-[#c8ab7a]"
                 placeholder="Korte toelichting (optioneel)">
        </div>
      @endforeach

      <button type="submit" id="checkin-submit"
              class="cursor-pointer w-full py-3 bg-[#c8ab7a] hover:bg-[#a38b62] transition duration-300 text-white font-semibold text-sm rounded-xl">
        Check-in versturen
      </button>
      <div id="checkin-status" class="text-xs text-center h-4"></div>
    </form>
  </div>
</div>

<script>
(function() {
  const overlay = document.getElementById('checkin-overlay');
  if (!overlay) return;

  // Slider value display
  document.querySelectorAll('.checkin-slider').forEach(slider => {
    const display = document.querySelector(`.checkin-value[data-for="${slider.dataset.key}"]`);
    if (display) {
      slider.addEventListener('input', () => display.textContent = slider.value);
    }
  });

  // Check of check-in getoond moet worden
  fetch('{{ route("client.checkin.check") }}')
    .then(r => r.json())
    .then(data => {
      if (!data.show) return;
      document.getElementById('checkin-week').textContent = data.week_number;
      document.getElementById('checkin-week-dates').textContent = data.week_dates || '';
      document.getElementById('checkin-week-input').value = data.week_number;
      overlay.classList.remove('hidden');
    })
    .catch(() => {});

  // Submit
  document.getElementById('checkin-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('checkin-submit');
    const status = document.getElementById('checkin-status');
    btn.disabled = true;
    btn.textContent = 'Versturen...';

    const fd = new FormData(e.target);
    const body = {};
    fd.forEach((v, k) => { body[k] = v; });

    // Parse integers
    ['week_number','recovery','training_feel','sleep_quality','stress','progression','soreness','motivation'].forEach(k => {
      if (body[k]) body[k] = parseInt(body[k]);
    });

    try {
      const res = await fetch('{{ route("client.checkin.store") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify(body),
      });

      if (res.ok) {
        status.textContent = 'Bedankt voor je check-in!';
        status.className = 'text-xs text-center h-4 text-green-600 font-semibold';
        setTimeout(() => overlay.classList.add('hidden'), 1500);
      } else {
        const err = await res.json().catch(() => ({}));
        const msg = err.errors ? Object.values(err.errors).flat()[0] : 'Er ging iets mis.';
        status.textContent = msg;
        status.className = 'text-xs text-center h-4 text-red-600';
        btn.disabled = false;
        btn.textContent = 'Check-in versturen';
      }
    } catch {
      status.textContent = 'Netwerkfout. Probeer opnieuw.';
      status.className = 'text-xs text-center h-4 text-red-600';
      btn.disabled = false;
      btn.textContent = 'Check-in versturen';
    }
  });
})();
</script>
@endif
@endauth
