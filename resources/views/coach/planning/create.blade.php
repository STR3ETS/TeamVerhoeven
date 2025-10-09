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

{{-- ====== BASIS-INSTELLINGEN ====== --}}
<div class="p-5 bg-white rounded-3xl border border-gray-300">
  <h2 class="text-sm text-black font-semibold opacity-50 mb-3">Instellingen</h2>

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
      <p class="text-[11px] text-gray-500 mt-1">Klik <em>Canvas opbouwen</em> en genereer daarna per week.</p>
    </div>
  </div>
</div>

{{-- ========= HANDMATIGE PLANNER + MEDIATHEEK ========= --}}
<div class="grid grid-cols-12 gap-4 mt-6" id="manual-builder">
  {{-- Canvas links --}}
  <div class="col-span-12 lg:col-span-9">
    <div class="p-5 bg-white rounded-3xl border border-gray-300">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-sm text-black font-semibold opacity-50">Handmatig plannen</h2>
        <div class="flex items-center gap-2">
          <button id="build-canvas"
            class="px-3 py-2 rounded-xl text-xs font-medium bg-gray-900 text-white hover:bg-black transition">
            Canvas opbouwen
          </button>
          <button id="clear-canvas"
            class="px-3 py-2 rounded-xl text-xs font-medium bg-gray-100 hover:bg-gray-200 transition">
            Leegmaken
          </button>
          <button id="export-json"
            class="px-3 py-2 rounded-xl text-xs font-medium bg-[#c8ab7a] text-white hover:bg-[#a38b62] transition">
            Exporteer JSON
          </button>
        </div>
      </div>

      <p class="text-xs text-gray-600 mb-3">
        We maken een leeg canvas (ma–zo per week). Sleep trainingen uit de mediatheek naar een dag of
        klik bij een week op <em>Genereer week</em> om alléén die week automatisch te vullen.
      </p>

      <div id="schedule-canvas" class="space-y-4">
        <div class="text-sm text-gray-500">Nog geen canvas. Klik <em>Canvas opbouwen</em> om te starten.</div>
      </div>

      {{-- JSON-uitvoer (handmatig plan) --}}
      <div class="mt-4">
        <label class="text-xs font-semibold text-gray-600">JSON (handmatig plan)</label>
        <textarea id="manual-json" class="mt-1 w-full rounded-xl border border-gray-300 p-3 text-xs h-40" readonly></textarea>
      </div>
    </div>
  </div>

  {{-- Mediatheek rechts --}}
  <div class="col-span-12 lg:col-span-3">
    <div class="p-5 bg-white rounded-3xl border border-gray-300 sticky top-4">
      <h2 class="text-sm text-black font-semibold opacity-50 mb-3">Mediatheek (uit TXT)</h2>

      <input id="lib-search" type="text" placeholder="Zoek op titel/type…"
        class="w-full rounded-xl border border-gray-300 p-2 text-sm mb-3">

      <div class="flex flex-wrap gap-2 mb-3 text-xs">
        <button data-filter="all" class="lib-filter px-2 py-1 rounded-full border border-gray-300">Alles</button>
        <button data-filter="run" class="lib-filter px-2 py-1 rounded-full border border-gray-300">Run</button>
        <button data-filter="strength" class="lib-filter px-2 py-1 rounded-full border border-gray-300">Strength</button>
        <button data-filter="erg" class="lib-filter px-2 py-1 rounded-full border border-gray-300">Erg</button>
        <button data-filter="core" class="lib-filter px-2 py-1 rounded-full border border-gray-300">Core</button>
        <button data-filter="recovery" class="lib-filter px-2 py-1 rounded-full border border-gray-300">Herstel</button>
        <button data-filter="hyrox" class="lib-filter px-2 py-1 rounded-full border border-gray-300">Hyrox</button>
        <button data-filter="misc" class="lib-filter px-2 py-1 rounded-full border border-gray-300">Overig</button>
      </div>

      <div id="library-list" class="space-y-2 max-h-[60vh] overflow-auto pr-1">
        {{-- JS render items --}}
      </div>
    </div>
  </div>
</div>

{{-- ---------- Startdatum-guard ---------- --}}
<script>
  (function () {
    const input = document.getElementById('start-date');
    if (!input) return;
    const minDate = new Date(input.min + 'T00:00:00');
    function toMondayOnOrAfter(date){const d=new Date(date.getTime());const day=d.getDay();const add=(day===1)?0:((8-day)%7);d.setDate(d.getDate()+add);return d;}
    function ymd(d){const m=String(d.getMonth()+1).padStart(2,'0');const day=String(d.getDate()).padStart(2,'0');return d.getFullYear()+'-'+m+'-'+day;}
    function sanitize(){
      if(!input.value) return;
      const typed=new Date(input.value+'T00:00:00');
      if(typed<minDate){input.value=input.min;return;}
      if(typed.getDay()!==1){input.value=ymd(toMondayOnOrAfter(typed));}
    }
    input.addEventListener('change', sanitize);
    input.addEventListener('blur', sanitize);
  })();
</script>

{{-- ============ Data van server ============ --}}
<script>
  window.TRAINING_LIBRARY = @json($library ?? []);
</script>

{{-- =========================================================
  Planner + Mediatheek + PER-WEEK-AI (DOM-safe)
  ========================================================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  (function () {
    /* ---------- Routes / csrf ---------- */
    const csrf        = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const generateUrl = @json(route('coach.clients.planning.generate', $client)); // PER WEEK

    /* ---------- DOM refs ---------- */
    const canvas   = document.getElementById('schedule-canvas');
    const txtOut   = document.getElementById('manual-json');
    const btnBuild = document.getElementById('build-canvas');
    const btnClear = document.getElementById('clear-canvas');
    const btnExport= document.getElementById('export-json');
    const libList  = document.getElementById('library-list');
    const libSearch= document.getElementById('lib-search');
    const libFilters = document.querySelectorAll('.lib-filter');

    /* ---------- Consts ---------- */
    const dayLabels = ['ma','di','wo','do','vr','za','zo'];
    const dayKey    = ['mon','tue','wed','thu','fri','sat','sun'];

    /* ---------- Mediatheek (gevuld vanuit TXT) ---------- */
    const LIBRARY = Array.isArray(window.TRAINING_LIBRARY) ? window.TRAINING_LIBRARY : [];

    /* ---------- State ---------- */
    let manual = {
      meta: {
        client_name: @json($client->name),
        period_weeks: parseInt(document.querySelector('input[name="period_weeks"]')?.value || '12', 10),
        start_date: document.getElementById('start-date')?.value || '',
        sessions_per_week: 4,
        notes: ''
      },
      weeks: [] // [{week_number,start,focus,sessions:{mon:[],...}}]
    };

    /* ---------- Helpers ---------- */
    function ymd(d){const m=String(d.getMonth()+1).padStart(2,'0');const day=String(d.getDate()).padStart(2,'0');return d.getFullYear()+'-'+m+'-'+day;}
    function mondayOf(dateStr){const d=new Date(dateStr+'T00:00:00');const day=d.getDay();const add=(day===1)?0:((8-day)%7);d.setDate(d.getDate()+add);return d;}
    function esc(s){return String(s??'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]))}
    function renderOutput(){ txtOut.value = JSON.stringify(manual, null, 2); }

    /* ---------- Canvas ---------- */
    function ensureCanvas(periodWeeks, startDate) {
      manual.meta.start_date = startDate;
      manual.meta.period_weeks = periodWeeks;

      const monday = mondayOf(startDate);
      manual.weeks = [];
      for (let i = 0; i < periodWeeks; i++) {
        const wStart = new Date(monday.getTime());
        wStart.setDate(wStart.getDate() + (i * 7));
        manual.weeks.push({
          week_number: i + 1,
          start: ymd(wStart),
          focus: '',
          sessions: {mon:[],tue:[],wed:[],thu:[],fri:[],sat:[],sun:[]}
        });
      }
    }

    function makeSessionCard(s, weekIndex, dayIdx, sessIdx) {
      const blocks = Array.isArray(s.blocks) ? s.blocks : [];
      const blocksHtml = blocks.length
        ? `<div class="mt-2 space-y-1">
            ${blocks.map(b => `
              <div class="text-[12px] leading-4">
                <span class="inline-block px-1 rounded bg-gray-100 border border-gray-200 mr-1">${esc(b.phase)}</span>
                <span class="font-medium">${esc(b.title)}</span>
                <span class="text-gray-500">• ${esc(b.duration_min)} min</span>
                ${b.notes ? `<span class="text-gray-600"> — ${esc(b.notes)}</span>` : ``}
              </div>
            `).join('')}
          </div>` : '';

      return `
        <div class="session-card p-2 rounded-lg border border-gray-200 bg-white flex items-start gap-2"
             draggable="true"
             data-week="${weekIndex}" data-day="${dayIdx}" data-index="${sessIdx}">
          <div class="flex-1">
            <div class="text-[13px] font-semibold leading-5 editable-title" contenteditable="true">${esc(s.title)}</div>
            <div class="text-[12px] text-gray-600">${esc(s.type)} • ${esc(s.duration_min)} min</div>
            ${s.notes ? `<div class="text-[12px] text-gray-700 mt-0.5 editable-notes" contenteditable="true">${esc(s.notes)}</div>` : ''}
            ${blocksHtml}
          </div>
          <button class="remove-session text-xs text-gray-500 hover:text-red-600">✕</button>
        </div>
      `;
    }

    function makeDayColumn(weekIndex, dayIdx) {
      const key = dayKey[dayIdx];
      const sessions = manual.weeks[weekIndex].sessions[key] || [];
      const items = sessions.map((s, i) => makeSessionCard(s, weekIndex, dayIdx, i)).join('');
      return `
        <div class="day-col rounded-2xl border border-gray-200 bg-gray-50 p-3"
             data-week="${weekIndex}" data-day="${dayIdx}"
             ondragover="event.preventDefault()">
          <div class="text-xs text-gray-500 mb-2 uppercase">${dayLabels[dayIdx]}</div>
          <div class="day-drop space-y-2 min-h-[20px]">${items}</div>
        </div>
      `;
    }

    function buildWeek(weekIndex) {
      const w = manual.weeks[weekIndex];
      return `
        <div class="rounded-3xl border border-gray-200 p-4">
          <div class="flex items-center justify-between mb-3">
            <div class="font-semibold text-sm">Week ${w.week_number} • ${w.start}</div>
            <div class="flex items-center gap-2">
              <input class="text-xs border rounded px-2 py-1 focus:outline-none"
                     value="${esc(w.focus || '')}"
                     data-week="${weekIndex}"
                     placeholder="Weekfocus (optioneel)">
              <button class="btn-gen-week px-2 py-1 text-xs rounded bg-[#c8ab7a] text-white hover:bg-[#a38b62]"
                      data-week="${weekIndex}">
                Genereer week
              </button>
            </div>
          </div>
          <div class="grid md:grid-cols-2 gap-2">
            ${Array.from({length:7}, (_,d)=>makeDayColumn(weekIndex,d)).join('')}
          </div>
        </div>
      `;
    }

    function renderCanvas() {
      if (!manual.weeks.length) {
        canvas.innerHTML = `<div class="text-sm text-gray-500">Nog geen canvas. Klik <em>Canvas opbouwen</em> om te starten.</div>`;
        renderOutput(); return;
      }
      canvas.innerHTML = manual.weeks.map((_, i) => buildWeek(i)).join('');
      hookDayDrops(); hookRemoveAndEdit();
      renderOutput();
    }

    function hookDayDrops() {
      canvas.querySelectorAll('.day-col').forEach(col => {
        col.addEventListener('drop', (e) => {
          e.preventDefault();
          const payload = e.dataTransfer.getData('application/json');
          if (!payload) return;
          const data = JSON.parse(payload);

          const wIdx = parseInt(col.dataset.week, 10);
          const dIdx = parseInt(col.dataset.day, 10);
          const key = dayKey[dIdx];

          manual.weeks[wIdx].sessions[key] = manual.weeks[wIdx].sessions[key] || [];
          manual.weeks[wIdx].sessions[key].push({
            day: key,
            type: data.type,
            title: data.title,
            duration_min: data.duration_min,
            notes: data.notes || '',
            lib_id: data.id || null
          });
          renderCanvas();
        });
      });
    }

    function hookRemoveAndEdit() {
      canvas.querySelectorAll('.remove-session').forEach(btn => {
        btn.addEventListener('click', (e) => {
          const card = e.currentTarget.closest('.session-card');
          const w = parseInt(card.dataset.week, 10);
          const d = parseInt(card.dataset.day, 10);
          const i = parseInt(card.dataset.index, 10);
          const key = dayKey[d];
          manual.weeks[w].sessions[key].splice(i, 1);
          renderCanvas();
        });
      });
      canvas.querySelectorAll('.editable-title').forEach(el => {
        el.addEventListener('blur', (e) => {
          const card = e.currentTarget.closest('.session-card');
          const w = parseInt(card.dataset.week, 10);
          const d = parseInt(card.dataset.day, 10);
          const i = parseInt(card.dataset.index, 10);
          const key = dayKey[d];
          manual.weeks[w].sessions[key][i].title = e.currentTarget.textContent.trim();
          renderOutput();
        });
      });
      canvas.querySelectorAll('.editable-notes').forEach(el => {
        el.addEventListener('blur', (e) => {
          const card = e.currentTarget.closest('.session-card');
          const w = parseInt(card.dataset.week, 10);
          const d = parseInt(card.dataset.day, 10);
          const i = parseInt(card.dataset.index, 10);
          const key = dayKey[d];
          manual.weeks[w].sessions[key][i].notes = e.currentTarget.textContent.trim();
          renderOutput();
        });
      });
      canvas.querySelectorAll('input[data-week]').forEach(inp => {
        inp.addEventListener('input', (e) => {
          const w = parseInt(e.currentTarget.dataset.week, 10);
          manual.weeks[w].focus = e.currentTarget.value;
          renderOutput();
        });
      });
    }

    /* ---------- Per-week genereren ---------- */
    canvas.addEventListener('click', (e) => {
      const btn = e.target.closest('.btn-gen-week');
      if (!btn) return;
      const weekIndex = parseInt(btn.dataset.week, 10);
      generateOneWeek(weekIndex, btn);
    });

    async function generateOneWeek(weekIndex, btnEl) {
      const week = manual.weeks[weekIndex];
      if (!week) { alert('Canvas nog niet opgebouwd.'); return; }

      const notes = document.querySelector('textarea[name="extra_input_bot"]')?.value || '';

      const oldTxt = btnEl.textContent;
      btnEl.textContent = 'Bezig…';
      btnEl.classList.add('opacity-70','pointer-events-none');

      try {
        const fd = new FormData();
        fd.append('week_number', week.week_number);
        fd.append('week_start', week.start);
        fd.append('extra_input_bot', notes);

        const res = await fetch(generateUrl, {
          method: 'POST',
          headers: {'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':csrf},
          body: fd,
          credentials: 'same-origin',
        });

        if (!res.ok) throw new Error(await res.text() || 'Request mislukte');
        const data = await res.json();
        if (!data.ok || !data.week) throw new Error('Geen week ontvangen');

        const w = data.week;
        // reset en vul de week
        manual.weeks[weekIndex].focus = w.focus || '';
        manual.weeks[weekIndex].sessions = {mon:[],tue:[],wed:[],thu:[],fri:[],sat:[],sun:[]};

        const validDay = new Set(['mon','tue','wed','thu','fri','sat','sun']);
        (w.sessions || []).forEach(s => {
          const d = (s.day || '').toLowerCase();
          if (!validDay.has(d)) return;
          const blocks = Array.isArray(s.blocks) ? s.blocks : [];
          const dur = blocks.reduce((acc,b)=>acc+(parseInt(b.duration_min,10)||0),0) || s.duration_min;

          manual.weeks[weekIndex].sessions[d].push({
            day:d, type:s.type, title:s.title, duration_min:dur, notes:s.notes || '', blocks,
            lib_id: s.lib_id || null
          });
        });

        renderCanvas();
        canvas.querySelector(`[data-week="${weekIndex}"]`)?.scrollIntoView({behavior:'smooth', block:'center'});
      } catch (err) {
        alert('Fout: ' + (err.message || err));
      } finally {
        btnEl.textContent = oldTxt;
        btnEl.classList.remove('opacity-70','pointer-events-none');
      }
    }

    /* ---------- Mediatheek UI ---------- */
    function libraryItemTemplate(item) {
      return `
        <div class="lib-item border border-gray-200 rounded-xl p-3 hover:bg-gray-50 cursor-grab active:cursor-grabbing"
             draggable="true"
             data-id="${esc(item.id)}">
          <div class="text-[13px] font-semibold">${esc(item.title)}</div>
          <div class="text-[12px] text-gray-600">${esc(item.group)} • ${esc(item.type)} • ${esc(item.duration_min)} min</div>
          ${item.notes ? `<div class="text-[12px] text-gray-700 mt-0.5">${esc(item.notes)}</div>` : ''}
        </div>
      `;
    }
    function renderLibrary(list) {
      libList.innerHTML = list.map(libraryItemTemplate).join('');
      libList.querySelectorAll('.lib-item').forEach(el => {
        el.addEventListener('dragstart', (e) => {
          const id = e.currentTarget.dataset.id;
          const item = LIBRARY.find(x => x.id === id);
          e.dataTransfer.setData('application/json', JSON.stringify(item));
        });
      });
    }
    libFilters.forEach(b => {
      b.addEventListener('click', () => {
        const f = b.dataset.filter;
        const q = libSearch?.value.trim().toLowerCase() || '';
        const filtered = LIBRARY.filter(it => {
          const okFilter = (f === 'all') || it.group === f;
          const okSearch = !q || it.title.toLowerCase().includes(q) || it.type.toLowerCase().includes(q);
          return okFilter && okSearch;
        });
        renderLibrary(filtered);
      });
    });
    libSearch?.addEventListener('input', () => {
      const q = libSearch.value.trim().toLowerCase();
      const filtered = LIBRARY.filter(it =>
        !q || it.title.toLowerCase().includes(q) || it.type.toLowerCase().includes(q)
      );
      renderLibrary(filtered);
    });

    /* ---------- Buttons: build/clear/export ---------- */
    btnBuild?.addEventListener('click', () => {
      const start = document.getElementById('start-date')?.value;
      const weeks = parseInt(document.querySelector('input[name="period_weeks"]')?.value || '12', 10);
      if (!start) { alert('Kies eerst een startdatum (maandag).'); return; }

      ensureCanvas(weeks, start);
      renderCanvas();
      renderLibrary(LIBRARY);
    });

    btnClear?.addEventListener('click', () => {
      manual.weeks = [];
      renderCanvas();
    });

    btnExport?.addEventListener('click', () => {
      const blob = new Blob([JSON.stringify(manual, null, 2)], {type: 'application/json'});
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `plan_handmatig_{{ $client->id }}.json`;
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
    });

    /* ---------- Init ---------- */
    renderLibrary(LIBRARY);
    renderOutput();
  })();
});
</script>
@endsection
