@extends('layouts.app')
@section('title', 'Trainingsbibliotheek')

@section('content')

@php
    // Alle kleurcombinaties die NU in de live DB staan (teamverhoeven.sql)
    $badgePresets = [
        [
            'label'   => 'Oranje – Raise / Activatie / Potentiate',
            'classes' => 'text-orange-500 bg-orange-100 border border-orange-200',
        ],
        [
            'label'   => 'Groen – Zone 1 (herstel / easy)',
            'classes' => 'text-green-700 bg-green-100 border border-green-200',
        ],
        [
            'label'   => 'Lime – Zone 2 / Strength (Endurance)',
            'classes' => 'text-lime-700 bg-lime-100 border border-lime-200',
        ],
        [
            'label'   => 'Geel – Zone 3',
            'classes' => 'text-amber-700 bg-amber-100 border border-amber-200',
        ],
        [
            'label'   => 'Donker oranje – Zone 4 / 4-5',
            'classes' => 'text-orange-700 bg-orange-100 border border-orange-200',
        ],
        [
            'label'   => 'Rood – Zone 5 (zwaar)',
            'classes' => 'text-red-700 bg-red-100 border border-red-200',
        ],
        [
            'label'   => 'Blauw – Cooling down',
            'classes' => 'text-sky-700 bg-sky-100 border border-sky-200',
        ],
        [
            'label'   => 'Paars – Structuur / kopjes (Doel, Hoofddeel, Benodigd)',
            'classes' => 'text-violet-700 bg-violet-100 border border-violet-200',
        ],
    ];

    // Dropdown-opties op basis van bovenstaande presets
    // KEY = exacte classes uit DB → 100% backwards compatible
    $badgeOptions = ['' => 'Geen kleur / standaard'];

    foreach ($badgePresets as $preset) {
        $badgeOptions[$preset['classes']] = $preset['label'];
    }
@endphp

<a href="{{ route('coach.index') }}"
   class="text-xs text-black font-semibold opacity-50 hover:opacity-100 transition duration-300">
    <i class="fa-solid fa-arrow-right-long fa-flip-horizontal fa-sm mr-2"></i>
    Terug naar overzicht
</a>

<h1 class="text-2xl font-bold mb-2 mt-1">Trainingsbibliotheek</h1>
<p class="text-sm text-black opacity-80 font-medium mb-4">
    Beheer hier alle trainingssecties, trainingen, blokken en items die je in de planner kunt gebruiken.
</p>

@if (session('status'))
    <div class="mb-4 text-xs font-semibold px-3 py-2 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200">
        {{ session('status') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-[0.9fr_1.1fr] gap-6">
    {{-- LINKERKOLOM: secties + trainingen --}}
    <div>
        <div class="bg-white rounded-3xl border border-gray-300 p-5">
            <h2 class="text-sm font-semibold uppercase tracking-wide opacity-70 mb-3">
                Secties &amp; trainingen
            </h2>

            <p class="text-[11px] text-black/60 mb-4">
                Kies een sectie en maak daaronder trainingen aan. Secties zelf zijn vast
                (bijv. Warming-up, Interval, Cooling Down).
            </p>

            <div class="space-y-4">
                @foreach($sections as $section)
                    <div class="border border-gray-200 rounded-2xl p-3 bg-gray-50/60">
                        {{-- Titel sectie (alleen lezen) --}}
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="text-xs font-semibold text-black">
                                {{ $section->name }}
                            </span>
                            <span class="text-[10px] text-gray-400">
                                #{{ $section->sort_order }}
                            </span>
                        </div>

                        {{-- Trainingen binnen sectie --}}
                        <ul class="space-y-1">
                            @foreach($section->cards as $card)
                                <li class="flex items-center justify-between gap-2 text-xs">
                                    <a href="{{ route('coach.training-library.index', ['card' => $card->id]) }}"
                                       class="flex-1 truncate {{ optional($currentCard)->id === $card->id ? 'font-semibold text-black' : 'text-black/70 hover:text-black' }}">
                                        {{ $card->title }}
                                    </a>
                                    <span class="text-[10px] text-gray-400">
                                        #{{ $card->sort_order }}
                                    </span>

                                    {{-- Verwijder training --}}
                                    <form action="{{ route('coach.training-library.cards.destroy', $card) }}"
                                          method="POST"
                                          onsubmit="return confirm('Training verwijderen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-[11px] text-red-500 hover:underline">
                                            X
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>

                        {{-- Nieuwe training in deze sectie --}}
                        <form action="{{ route('coach.training-library.cards.store') }}" method="POST"
                              class="mt-3 flex flex-col gap-2 md:flex-row md:items-center">
                            @csrf
                            <input type="hidden" name="training_section_id" value="{{ $section->id }}">

                            <input type="text" name="title" required
                                   class="flex-1 text-xs px-2.5 py-1.5 rounded-2xl border border-gray-300 bg-white"
                                   placeholder="Nieuwe training in {{ $section->name }}">
                            <input type="number" name="sort_order"
                                   class="w-16 text-xs px-2 py-1.5 rounded-2xl border border-gray-300 bg-white"
                                   placeholder="#">

                            <button type="submit"
                                    class="text-[11px] px-3 py-1.5 rounded-2xl bg-black text-white font-semibold">
                                + Training
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- RECHTERKOLOM: geselecteerde training (blocks + items) --}}
    <div>
        @if($currentCard)
            <div class="bg-white rounded-3xl border border-gray-300 p-5 flex flex-col gap-5">
                {{-- 1. Titel + sort_order van de training --}}
                <form action="{{ route('coach.training-library.cards.update', $currentCard) }}"
                    method="POST"
                    class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    @csrf
                    @method('PATCH')

                    {{-- Nodig omdat updateCard dit required valideert --}}
                    <input type="hidden" name="training_section_id" value="{{ $currentCard->training_section_id }}">

                    <div class="flex-1">
                        <label class="block text-[11px] font-semibold text-black/60 mb-1">
                            Titel van training
                        </label>
                        <input type="text" name="title"
                               value="{{ old('title', $currentCard->title) }}"
                               class="w-full px-3 py-2 rounded-2xl border border-gray-300 text-sm"
                               placeholder="Bijv. Warming-up Hardlopen / Cardio">
                    </div>

                    <div class="w-24">
                        <label class="block text-[11px] font-semibold text-black/60 mb-1">
                            Sortering
                        </label>
                        <input type="number" name="sort_order"
                               value="{{ old('sort_order', $currentCard->sort_order) }}"
                               class="w-full px-2 py-2 rounded-2xl border border-gray-300 text-sm text-center"
                               placeholder="#">
                    </div>

                    <div class="mt-2 md:mt-6 flex items-center gap-2">
                        <button type="submit"
                                class="px-4 py-2 rounded-2xl bg-black text-white text-xs font-semibold">
                            Training opslaan
                        </button>
                    </div>
                </form>

                {{-- Losse delete-knop voor de training --}}
                <div class="flex justify-end -mt-2">
                    <form action="{{ route('coach.training-library.cards.destroy', $currentCard) }}"
                          method="POST"
                          onsubmit="return confirm('Weet je zeker dat je deze training + alle onderdelen wilt verwijderen?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-3 py-2 rounded-2xl border border-red-300 text-[11px] text-red-600 font-semibold">
                            Verwijder training
                        </button>
                    </form>
                </div>

                <hr class="border-dashed border-gray-200">

                {{-- 2. Onderdelen (blocks) binnen deze training --}}
                <div class="space-y-4">
                    @foreach($currentCard->blocks as $block)
                        <div class="border border-gray-200 rounded-2xl p-3 bg-gray-50/60">
                            {{-- Block header (Raise / Activation etc) --}}
                            <form action="{{ route('coach.training-library.blocks.update', $block) }}"
                                method="POST"
                                class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                                @csrf
                                @method('PATCH')

                                <div class="flex-1">
                                    <label class="block text-[11px] font-semibold text-black/60 mb-1">
                                        Onderdeel titel
                                    </label>
                                    <input type="text" name="label"
                                           value="{{ old('label', $block->label) }}"
                                           class="w-full px-3 py-1.5 rounded-2xl border border-gray-300 text-xs"
                                           placeholder="Bijv. Raise, Activation / Mobilisation">
                                </div>

                                <div class="flex-1 md:max-w-[240px]">
                                    <label class="block text-[11px] font-semibold text-black/60 mb-1">
                                        Badge kleur
                                    </label>
                                    <select name="badge_classes"
                                            class="w-full px-3 py-1.5 rounded-2xl border border-gray-300 text-[11px] bg-white">
                                        @foreach($badgeOptions as $value => $label)
                                            <option value="{{ $value }}"
                                                @selected(old('badge_classes', $block->badge_classes) === $value)>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-[10px] text-black/45">
                                        Dit bepaalt alleen de kleur van het label (Raise / Zone 1–5 / Cooling down / enz.).
                                    </p>
                                </div>

                                <div class="w-20">
                                    <label class="block text-[11px] font-semibold text-black/60 mb-1">
                                        Volgorde
                                    </label>
                                    <input type="number" name="sort_order"
                                           value="{{ old('sort_order', $block->sort_order) }}"
                                           class="w-full px-2 py-1.5 rounded-2xl border border-gray-300 text-xs text-center">
                                </div>

                                <div class="flex items-center gap-2">
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded-2xl bg-black text-white text-[11px] font-semibold mt-1 md:mt-0">
                                        Opslaan
                                    </button>
                                </div>
                            </form>

                            <div class="flex justify-end mt-1">
                                <form action="{{ route('coach.training-library.blocks.destroy', $block) }}"
                                      method="POST"
                                      onsubmit="return confirm('Dit onderdeel + alle oefeningen verwijderen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-2.5 py-1.5 rounded-2xl border border-red-300 text-[11px] text-red-600 font-semibold">
                                        Onderdeel verwijderen
                                    </button>
                                </form>
                            </div>

                            {{-- Items (oefeningen) binnen dit onderdeel --}}
                            <div class="mt-3 space-y-2">
                                @foreach($block->items as $item)
                                    <form action="{{ route('coach.training-library.items.update', $item) }}"
                                        method="POST"
                                        class="grid grid-cols-[minmax(0,1.5fr)_minmax(0,0.7fr)_auto] gap-2 items-start">
                                        @csrf
                                        @method('PATCH')

                                        <div>
                                            <label class="block text-[10px] font-semibold text-black/55 mb-1">
                                                Oefening (linkerzijde)
                                            </label>
                                            <input type="text" name="left_html"
                                                   value="{{ old('left_html', $item->left_html) }}"
                                                   class="w-full px-3 py-1.5 rounded-2xl border border-gray-300 text-xs"
                                                   placeholder="Bijv. Hardlopen zone 1">
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-semibold text-black/55 mb-1">
                                                Duur / herhalingen (rechts)
                                            </label>
                                            <input type="text" name="right_text"
                                                   value="{{ old('right_text', $item->right_text) }}"
                                                   class="w-full px-3 py-1.5 rounded-2xl border border-gray-300 text-xs"
                                                   placeholder="Bijv. 1 kilometer, 3 × 30 sec">
                                        </div>

                                        <div class="flex items-center gap-1 pt-5">
                                            <button type="submit"
                                                    class="px-3 py-1.5 rounded-2xl bg-black text-white text-[11px] font-semibold">
                                                ✔
                                            </button>

                                            <form action="{{ route('coach.training-library.items.destroy', $item) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Oefening verwijderen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-2.5 py-1.5 rounded-2xl border border-red-300 text-[11px] text-red-600 font-semibold">
                                                    X
                                                </button>
                                            </form>
                                        </div>
                                    </form>
                                @endforeach

                                {{-- Nieuwe oefening toevoegen aan dit onderdeel --}}
                                <form action="{{ route('coach.training-library.items.store') }}"
                                    method="POST"
                                    class="grid grid-cols-[minmax(0,1.5fr)_minmax(0,0.7fr)_auto] gap-2 items-start mt-3">
                                    @csrf

                                    {{-- Koppel deze oefening aan het juiste onderdeel --}}
                                    <input type="hidden" name="training_block_id" value="{{ $block->id }}">

                                    <div>
                                        <label class="block text-[10px] font-semibold text-black/55 mb-1">
                                            Nieuwe oefening
                                        </label>
                                        <input type="text" name="left_html" required
                                               class="w-full px-3 py-1.5 rounded-2xl border border-dashed border-gray-300 text-xs"
                                               placeholder="Bijv. Skipping hoog tempo">
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-semibold text-black/55 mb-1">
                                            Duur / herhalingen
                                        </label>
                                        <input type="text" name="right_text"
                                               class="w-full px-3 py-1.5 rounded-2xl border border-dashed border-gray-300 text-xs"
                                               placeholder="Bijv. 10 meter, 6× herhalen">
                                    </div>

                                    <div class="flex items-center pt-6">
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-2xl border border-gray-400 text-[11px] font-semibold">
                                            + Regel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Nieuw onderdeel toevoegen --}}
                <div class="mt-4 border-t border-dashed border-gray-200 pt-4">
                    <form action="{{ route('coach.training-library.blocks.store') }}"
                        method="POST"
                        class="flex flex-col gap-2">
                        @csrf

                        {{-- BELANGRIJK: koppel dit onderdeel aan de huidige training --}}
                        <input type="hidden" name="training_card_id" value="{{ $currentCard->id }}">

                        <input type="text" name="label" required
                            class="w-full px-3 py-1.5 rounded-2xl border border-gray-300 text-xs"
                            placeholder="Nieuwe onderdeel titel (bijv. Cooling-down)">
                            <select name="badge_classes"
                                    class="w-full px-3 py-1.5 rounded-2xl border border-gray-300 text-[11px] bg-white">
                                @foreach($badgeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        <input type="number" name="sort_order"
                            class="w-full px-2 py-1.5 rounded-2xl border border-gray-300 text-xs text-center"
                            placeholder="#">

                        <button type="submit"
                                class="px-4 py-1.5 rounded-2xl bg-black text-white text-[11px] font-semibold">
                            + Onderdeel
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="bg-white rounded-3xl border border-gray-300 p-8 text-sm text-black/60">
                Kies links een training of maak er één aan om onderdelen en oefeningen toe te voegen.
            </div>
        @endif
    </div>
</div>
@once
    {{-- Seed alle badge-kleur combinaties zodat Tailwind de classes genereert --}}
    <div class="hidden">
        @foreach($badgePresets as $preset)
            <span class="inline-flex px-2 py-1 rounded-full text-[11px] {{ $preset['classes'] }}">
                Seed
            </span>
        @endforeach
    </div>
@endonce
@endsection
