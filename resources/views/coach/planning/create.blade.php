{{-- resources/views/coach/planning/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Bestaande gegevens – ' . ($client->name ?? 'Cliënt'))

@section('content')
<a href="{{ route('coach.clients.show', $client) }}"
   class="text-xs text-black font-semibold opacity-50 hover:opacity-100 transition duration-300">
  <i class="fa-solid fa-arrow-right-long fa-flip-horizontal fa-sm mr-2"></i>
  Terug naar cliënt
</a>

<h1 class="text-2xl font-bold mb-2 mt-1">Trainingsplan {{ $client->name }}</h1>
<p class="text-sm text-black opacity-80 font-medium mb-6">
  Je bent een trainingsplan aan het maken voor {{ $client->name }}.
</p>

{{-- Week selector --}}
<div class="flex flex-wrap gap-2 mb-4">
  @for($w=1; $w <= ($totalWeeks ?? 1); $w++)
    <a href="{{ route('coach.clients.trainingplan', [$client, 'week' => $w]) }}"
       class="px-3 py-1 rounded-full text-xs border
              {{ ($week ?? 1) === $w ? 'bg-black text-white border-black' : 'bg-white text-black/70 border-gray-300 hover:bg-gray-50' }}">
      Week {{ $w }}
    </a>
  @endfor
</div>

<div class="w-full grid grid-cols-2 gap-6">
  {{-- LINKERKOLOM: planning + dropzones --}}
  <div class="js-plan">
    <h2 class="text-lg font-bold mb-2">Trainingsplan</h2>
    <div class="p-5 bg-white rounded-3xl border border-gray-300">
      <div class="max-h-[60vh] overflow-y-auto">
        <h3 class="text-sm font-semibold opacity-50 mb-4">Week {{ $week ?? 1 }}</h3>

        @php
          $days = [
            'mon' => 'Maandag',
            'tue' => 'Dinsdag',
            'wed' => 'Woensdag',
            'thu' => 'Donderdag',
            'fri' => 'Vrijdag',
            'sat' => 'Zaterdag',
            'sun' => 'Zondag',
          ];
        @endphp

        @foreach($days as $key => $label)
          <div class="mb-2">
            <h3 class="text-sm font-semibold mb-2 opacity-50">{{ $label }}</h3>
            <div
              class="js-dropzone day-dropzone p-5 min-h-[150px] bg-gray-200 rounded-3xl border border-gray-300 flex flex-col gap-2"
              data-day="{{ $key }}">
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- RECHTERKOLOM: bibliotheek (draggable) --}}
  <div class="js-library">
    <h2 class="text-lg font-bold mb-2">Trainingen bibliotheek</h2>
    <div class="p-5 bg-white rounded-3xl border border-gray-300">
      <div class="flex flex-col gap-6 max-h-[60vh] overflow-y-auto">
        @foreach($sections as $section)
          <div>
            <h2 class="text-sm font-semibold mb-2 opacity-50">{{ $section->name }}</h2>
            <div class="flex flex-col gap-2">
              @foreach($section->cards as $card)
                @php $phaseAttr = \Illuminate\Support\Str::lower($section->name ?? ''); @endphp
                <div
                  class="p-5 bg-white rounded-3xl border border-gray-300 cursor-grab active:cursor-grabbing draggable-card js-card group"
                  draggable="true"
                  data-card-id="{{ $card->id }}"
                  data-card-title="{{ $card->title }}"
                  data-phase="{{ $phaseAttr }}"
                >
                  <h2 class="text-sm font-semibold mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-grip-lines text-gray-300"></i>
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
              @endforeach
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

{{-- Data uit controller --}}
<script>
  window.__assigned    = @json($assignments ?? []);
  window.__activeWeek  = {{ $week ?? 1 }};
</script>

{{-- === GSAP pinning (fix “witte ruimte onder”) === --}}
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  if (!window.gsap || !window.ScrollTrigger) return;
  gsap.registerPlugin(ScrollTrigger);

  const lib  = document.querySelector('.js-library');
  const plan = document.querySelector('.js-plan');
  if (!lib || !plan) return;

  // houd breedte vast tijdens pinnen
  ScrollTrigger.addEventListener('refreshInit', () => {
    lib.style.width = lib.offsetWidth + 'px';
  });

  const START_OFFSET = 16; // px

  const st = ScrollTrigger.create({
    trigger: plan,
    start: () => `top top+=${START_OFFSET}`,
    end: () => '+=' + Math.max(1, plan.offsetHeight - lib.offsetHeight),
    pin: lib,
    pinSpacing: true,
    anticipatePin: 1,
    invalidateOnRefresh: true,
    // markers: true,
  });

  window.addEventListener('resize', () => st.refresh());
  // helper die we ook vanuit DnD aanroepen na layout-wijziging
  window.__refreshPin = () => st.refresh();
});
</script>

{{-- === Drag & Drop + reorder === --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  // ==== Config ====
  const CSRF_TOKEN    = '{{ csrf_token() }}';
  const ASSIGN_URL    = "{{ route('coach.clients.planning.assign', $client) }}";
  const UNASSIGN_URL  = "{{ route('coach.clients.planning.unassign', $client) }}";
  const REORDER_URL   = "{{ route('coach.clients.planning.reorder', $client) }}";
  const ACTIVE_WEEK   = window.__activeWeek || 1;

  // ==== Helpers ====
  const $  = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  function removePlaceholder(zone) {
    const ph = zone.querySelector('.js-placeholder');
    if (ph) ph.remove();
  }
  function ensurePlaceholder(zone) {
    const hasAssigned = zone.querySelector('.js-assigned-card');
    if (!hasAssigned && !zone.querySelector('.js-placeholder')) {
      const ph = document.createElement('div');
      ph.className = 'js-placeholder text-xs text-gray-500 italic';
      ph.textContent = 'Sleep een training hierheen';
      zone.appendChild(ph);
    }
  }
  function zoneBusy(zone, busy = true) {
    zone.dataset.busy = busy ? '1' : '0';
    zone.classList.toggle('opacity-60', busy);
  }
  function dragVisual(zone, on) {
    zone.classList.toggle('border-sky-400', on);
    zone.classList.toggle('bg-sky-50', on);
  }

  // ====== Reorder helpers (binnen dezelfde dag) ======
  let draggingAssigned = null;

  function phaseRank(phase) {
    const p = (phase || '').toLowerCase();
    if (p.includes('warm')) return 0;                              // warming-up
    if (p.includes('activ') || p.includes('mobil')) return 1;      // activatie/mobilisatie
    if (p.includes('main') || p.includes('run') || p.includes('hyrox')
        || p.includes('streng') || p.includes('erg') || p.includes('core')) return 2; // hoofdwerk
    if (p.includes('finish')) return 3;                            // finisher
    if (p.includes('cool')) return 4;                              // cooling-down
    return 2; // default midden
  }

  function insertByPhase(zone, cardEl) {
    const rank = phaseRank(cardEl.dataset.phase);
    const siblings = [...zone.querySelectorAll('.js-assigned-card')];
    const after = siblings.find(s => phaseRank(s.dataset.phase) > rank);
    if (after) zone.insertBefore(cardEl, after);
    else zone.appendChild(cardEl);
  }

  function makeAssignedDraggable(cardEl) {
    cardEl.setAttribute('draggable', 'true');

    cardEl.addEventListener('dragstart', (e) => {
      draggingAssigned = cardEl;
      cardEl.classList.add('opacity-60', 'dragging');
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', 'reorder');
    });

    cardEl.addEventListener('dragend', () => {
      cardEl.classList.remove('opacity-60', 'dragging');
      const zone = cardEl.closest('.day-dropzone, .js-dropzone');
      if (zone) persistZoneOrder(zone);
      draggingAssigned = null;
      if (window.ScrollTrigger) (window.__refreshPin?.() ?? ScrollTrigger.refresh());
    });
  }

  function persistZoneOrder(zone) {
    const order = [...zone.querySelectorAll('.js-assigned-card')]
      .map(el => parseInt(el.dataset.assignmentId))
      .filter(Boolean);

    if (!order.length) { ensurePlaceholder(zone); return; }

    fetch(REORDER_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
      body: JSON.stringify({ day: zone.dataset.day, week: ACTIVE_WEEK, order })
    }).catch(console.error);
  }

  function handleReorderDragOver(zone, clientY) {
    const cards = [...zone.querySelectorAll('.js-assigned-card:not(.dragging)')];
    let insertBeforeEl = null;
    for (const card of cards) {
      const rect = card.getBoundingClientRect();
      const halfway = rect.top + rect.height / 2;
      if (clientY < halfway) { insertBeforeEl = card; break; }
    }
    if (insertBeforeEl) zone.insertBefore(draggingAssigned, insertBeforeEl);
    else zone.appendChild(draggingAssigned);
  }

  // ====== Assigned kaart renderen ======
  function addRemoveButton(cardEl, zone) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition text-gray-500 hover:text-red-600 bg-white/90 rounded-full w-6 h-6 flex items-center justify-center cursor-pointer';
    btn.setAttribute('aria-label', 'Verwijderen');
    btn.innerHTML = '<i class="fa-solid fa-minus text-red-500 fa-sm"></i>';

    btn.addEventListener('click', async () => {
      const assignmentId = cardEl.dataset.assignmentId || null;
      cardEl.remove();
      ensurePlaceholder(zone);

      if (assignmentId) {
        try {
          await fetch(UNASSIGN_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ assignment_id: assignmentId })
          });
          persistZoneOrder(zone);
        } catch (e) { console.error(e); }
      }
      if (window.ScrollTrigger) (window.__refreshPin?.() ?? ScrollTrigger.refresh());
    });

    if (!cardEl.classList.contains('relative')) cardEl.classList.add('relative');
    cardEl.appendChild(btn);
  }

  function addAssignedCard(zone, cardId, assignmentId = null) {
    removePlaceholder(zone);

    const src = document.querySelector(`.js-card[data-card-id="${cardId}"]`);
    const clone = src ? src.cloneNode(true) : document.createElement('div');

    clone.classList.add('js-assigned-card', 'relative', 'group');
    clone.classList.remove('js-card', 'draggable-card', 'cursor-grab', 'active:cursor-grabbing');
    clone.removeAttribute('draggable');

    if (!src) {
      clone.className = 'js-assigned-card p-5 bg-white rounded-3xl border border-gray-300';
      clone.innerHTML = `<div class="text-sm font-semibold mb-1">Training #${cardId}</div><div class="text-xs text-gray-500">Bronkaart niet gevonden</div>`;
    }

    const phase = src?.dataset.phase || '';
    clone.dataset.phase = phase;

    if (assignmentId) clone.dataset.assignmentId = assignmentId;

    addRemoveButton(clone, zone);
    makeAssignedDraggable(clone);

    insertByPhase(zone, clone);
    persistZoneOrder(zone);

    if (window.ScrollTrigger) (window.__refreshPin?.() ?? ScrollTrigger.refresh());
  }

  // ==== Drag sources (bibliotheek) ====
  const libCards = $$('.draggable-card, .js-card');
  libCards.forEach((cardEl) => {
    if (!cardEl.dataset.title) {
      cardEl.dataset.title = cardEl.dataset.cardTitle
        || (cardEl.querySelector('[data-card-title], h2, .card-title')?.textContent?.trim())
        || 'Training';
    }
    cardEl.setAttribute('draggable', 'true');

    cardEl.addEventListener('dragstart', (e) => {
      const payload = { card_id: cardEl.dataset.cardId, title: cardEl.dataset.title };
      e.dataTransfer.setData('application/json', JSON.stringify(payload));
      e.dataTransfer.effectAllowed = 'copy';
      cardEl.classList.add('opacity-60');
    });
    cardEl.addEventListener('dragend', () => cardEl.classList.remove('opacity-60'));
  });

  // ==== Dropzones (weekdagen) ====
  const dropzones = $$('.day-dropzone, .js-dropzone');
  dropzones.forEach((zone) => {
    ensurePlaceholder(zone);

    zone.addEventListener('dragover', (e) => {
      e.preventDefault();
      e.dataTransfer.dropEffect = draggingAssigned ? 'move' : 'copy';
      dragVisual(zone, true);
      if (draggingAssigned) handleReorderDragOver(zone, e.clientY);
    });

    zone.addEventListener('dragleave', () => dragVisual(zone, false));

    zone.addEventListener('drop', async (e) => {
      e.preventDefault();
      dragVisual(zone, false);
      if (zone.dataset.busy === '1') return;

      if (draggingAssigned) return; // dragend slaat volgorde op

      let payload = null;
      try { payload = JSON.parse(e.dataTransfer.getData('application/json') || '{}'); } catch (_) {}
      if (!payload || !payload.card_id) return;

      zoneBusy(zone, true);
      removePlaceholder(zone);
      const temp = document.createElement('div');
      temp.className = 'p-5 bg-white rounded-3xl border border-gray-300 shadow-sm';
      temp.innerHTML = `
        <div class="h-4 w-28 bg-gray-200 animate-pulse rounded mb-3"></div>
        <div class="space-y-3">
          <div class="h-3 w-3/4 bg-gray-100 animate-pulse rounded"></div>
          <div class="h-3 w-2/3  bg-gray-100 animate-pulse rounded"></div>
          <div class="h-3 w-4/5  bg-gray-100 animate-pulse rounded"></div>
        </div>`;
      zone.appendChild(temp);

      try {
        const res = await fetch(ASSIGN_URL, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
          body: JSON.stringify({ day: zone.dataset.day, card_id: payload.card_id, week: ACTIVE_WEEK })
        });
        const json = await res.json();
        temp.remove();
        addAssignedCard(zone, payload.card_id, json.assignment_id ?? null);
      } catch (err) {
        console.error(err);
        temp.remove();
        ensurePlaceholder(zone);
        alert('Opslaan mislukt. Probeer opnieuw.');
      } finally {
        zoneBusy(zone, false);
      }
    });
  });

  // ==== Bestaande planning renderen ====
  if (Array.isArray(window.__assigned)) {
    const byDay = window.__assigned.reduce((acc, a) => ((acc[a.day] ??= []).push(a), acc), {});
    Object.entries(byDay).forEach(([day, items]) => {
      const el = document.querySelector(`.day-dropzone[data-day="${day}"], .js-dropzone[data-day="${day}"]`);
      if (!el) return;
      items.sort((a,b) => (a.sort_order ?? 0) - (b.sort_order ?? 0));
      items.forEach((it) => addAssignedCard(el, it.training_card_id, it.assignment_id));
    });
  }

  if (window.ScrollTrigger) (window.__refreshPin?.() ?? ScrollTrigger.refresh());
});
</script>
@endsection
