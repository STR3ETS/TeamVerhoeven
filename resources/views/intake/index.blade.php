@extends('layouts.app')
@section('title','Team Verhoeven')

@section('content')
<style>
  .iti { width: 100% !important; } /* intl-tel-input wrapper full width */

  /* Swiper helpers */
  .packages-swiper .swiper-wrapper { align-items: stretch; }
  .packages-swiper .swiper-slide > .relative { height: 100%; }
</style>

@php
  $cardClass  = 'p-5 bg-white rounded-3xl border border-gray-300';
  $btnPrimary = 'cursor-pointer px-6 py-3 bg-[#c8ab7a] hover:bg-[#a38b62] transition duration-300 text-white font-medium text-sm rounded';
  $btnGhost   = 'text-xs cursor-pointer opacity-50 hover:opacity-100 transition duration-300 font-semibold';
@endphp

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<div class="max-w-3xl mx-auto" x-data="intakeWizard()" x-init="init()">
  <h1 class="text-2xl font-bold mb-2 flex items-center">
    <div class="flex">
      <div class="w-10 h-10 border-2 border-[#f9f6f1] rounded-full bg-black bg-cover bg-top relative bg-[url(https://cdn6.site-media.eu/images/640%2C1160x772%2B130%2B112/18694492/coachNicky-MjbAPBl6Pr1a23o9d6zbqA.webp)]"></div>
      <div class="-left-3 w-10 h-10 border-2 border-[#f9f6f1] rounded-full bg-black bg-cover bg-top relative bg-[url(https://cdn6.site-media.eu/images/576%2C1160x772%2B150%2B121/18694504/coachEline-DVsTZnUZ-eQ_EWm1zNyfww.webp)]"></div>
      <div class="-left-6 w-10 h-10 border-2 border-[#f9f6f1] rounded-full bg-black bg-cover bg-top relative bg-[url(https://cdn6.site-media.eu/images/576%2C1160x772%2B134%2B41/18694509/coachRoy-LCXiB9ufGNk2uXEnykijBA.webp)]"></div>
    </div>
    <div class="bg-white h-9 px-4 rounded-xl flex items-center relative -ml-2">
      <div class="w-4 h-4 rotate-[45deg] rounded-sm absolute -left-1 bg-white"></div>
      <p class="italic text-[10px] leading-[1] md:leading-tighter font-semibold">"Leuk je te zien! Klaar om te knallen? 🔥"</p>
    </div>
  </h1>

  <h2 class="text-xl font-bold mb-2">Intakeformulier</h2>
  <p class="text-sm font-medium text-black/60 mb-8">
    Goed om je te zien en welkom bij 2BeFit Coaching X Team Verhoeven<br>
    Laten we even je persoonlijke profiel samenstellen. Op basis hiervan worden de trainingen samengesteld.
  </p>

  <div class="{{ $cardClass }}">
    <!-- Generieke foutmelding -->
    <div x-show="Object.keys(errors).length"
         class="mb-4 rounded-xl border border-red-200 bg-red-50 text-red-700 p-3 text-sm"
         x-cloak>
      <span x-text="Object.values(errors)[0] || 'Niet alle velden zijn correct ingevuld. Controleer de rood gemarkeerde velden.'"></span>
    </div>

    <!-- STEP 0: Persoonlijke gegevens -->
    <template x-if="step === 0">
      <div id="stap-1">
        <h3 class="text-md font-semibold mb-4">Persoonlijke gegevens</h3>

        <form @submit.prevent class="flex flex-col gap-4 mb-8" novalidate>
          <!-- Naam -->
          <div>
            <p class="text-sm font-medium text-black mb-1">Wat is je volledige naam?</p>
            <input id="name" type="text" name="name" x-model="form.name" required
                   class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm
                          border-gray-300 hover:border-[#c7c7c7]"
                   :class="errors.name ? 'border-red-500 focus:border-red-500' : ''">
          </div>

          <!-- E-mail + Telefoon -->
          <div class="flex gap-4">
            <div class="w-3/5 min-w-0">
              <p class="text-sm font-medium text-black mb-1">Wat is je emailadres?</p>
              <input id="email" type="email" name="email" x-model="form.email" required
                     class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm
                            border-gray-300 hover:border-[#c7c7c7]"
                     :class="errors.email ? 'border-red-500 focus:border-red-500' : ''">
            </div>

            <div class="w-2/5 min-w-0">
              <p class="text-sm font-medium text-black mb-1">Wat is je telefoonnummer?</p>
              <div class="w-full">
                <input id="phone" type="tel" name="phone" required
                       class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm
                              border-gray-300 hover:border-[#c7c7c7]"
                       :class="errors.phone ? 'border-red-500 focus:border-red-500' : ''">
              </div>
            </div>
          </div>

          <!-- Geboortedatum -->
          <div>
            <p class="text-sm font-medium text-black mb-1">Wat is je geboortedatum?</p>
            <input id="dob" type="date" name="dob" x-model="form.dob" required
                   class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm
                          border-gray-300 hover:border-[#c7c7c7]"
                   :class="errors.dob ? 'border-red-500 focus:border-red-500' : ''">
          </div>

          <!-- Adres -->
          <div>
            <p class="text-sm font-medium text-black mb-2">Wat is je adres?</p>
            <div class="flex gap-4">
              <div class="flex-1 min-w-0">
                <input id="street" type="text" name="street" x-model="form.street" placeholder="Straatnaam"
                       class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm
                              border-gray-300 hover:border-[#c7c7c7]"
                       :class="errors.street ? 'border-red-500 focus:border-red-500' : ''">
              </div>
              <div class="w-34 min-w-0">
                <input id="house_number" type="text" name="house_number" x-model="form.house_number" placeholder="Nr."
                       class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm
                              border-gray-300 hover:border-[#c7c7c7]"
                       :class="errors.house_number ? 'border-red-500 focus:border-red-500' : ''">
              </div>
              <div class="w-44 min-w-0">
                <input id="postcode" type="text" name="postcode" x-model="form.postcode" placeholder="Postcode"
                       class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm
                              border-gray-300 hover:border-[#c7c7c7]"
                       :class="errors.postcode ? 'border-red-500 focus:border-red-500' : ''">
              </div>
            </div>
          </div>

          <!-- Geslacht (custom radios) -->
          <div>
            <p class="text-sm font-medium text-black mb-2">Wat is je geslacht?</p>
            <div class="flex items-center gap-6">
              <!-- Man -->
              <label class="inline-flex items-center gap-3 cursor-pointer select-none"
                     :class="errors.gender ? 'text-red-600' : ''">
                <input type="radio" name="gender" value="man" x-model="form.gender" class="sr-only peer" />
                <span
                  class="w-4 h-4 rounded-full border inline-flex items-center justify-center transition
                         bg-gray-200 border-gray-300
                         peer-checked:bg-[#c8ab7a] peer-checked:border-[#c8ab7a]
                         peer-focus-visible:ring-2 peer-focus-visible:ring-[#c8ab7a] peer-focus-visible:ring-offset-2"
                  :class="errors.gender ? 'border-red-500' : ''"
                  aria-hidden="true">
                  <svg class="w-3 h-3 text-white opacity-0 transition-opacity peer-checked:opacity-100" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 0 1 0 1.414l-7.364 7.364a1 1 0 0 1-1.414 0L3.293 9.435a1 1 0 1 1 1.414-1.414l3.05 3.05 6.657-6.657a1 1 0 0 1 1.414 0z" clip-rule="evenodd"/>
                  </svg>
                </span>
                <span class="text-sm">Man</span>
              </label>

              <!-- Vrouw -->
              <label class="inline-flex items-center gap-3 cursor-pointer select-none"
                     :class="errors.gender ? 'text-red-600' : ''">
                <input type="radio" name="gender" value="vrouw" x-model="form.gender" class="sr-only peer" />
                <span
                  class="w-4 h-4 rounded-full border inline-flex items-center justify-center transition
                         bg-gray-200 border-gray-300
                         peer-checked:bg-[#c8ab7a] peer-checked:border-[#c8ab7a]
                         peer-focus-visible:ring-2 peer-focus-visible:ring-[#c8ab7a] peer-focus-visible:ring-offset-2"
                  :class="errors.gender ? 'border-red-500' : ''"
                  aria-hidden="true">
                  <svg class="w-3 h-3 text-white opacity-0 transition-opacity peer-checked:opacity-100" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 0 1 0 1.414l-7.364 7.364a1 1 0 0 1-1.414 0L3.293 9.435a1 1 0 1 1 1.414-1.414l3.05 3.05 6.657-6.657a1 1 0 0 1 1.414 0z" clip-rule="evenodd"/>
                  </svg>
                </span>
                <span class="text-sm">Vrouw</span>
              </label>
            </div>
          </div>
        </form>

        <div class="flex items-center gap-2">
          <button type="button" class="{{ $btnPrimary }}" @click="next()">Volgende stap</button>
        </div>
      </div>
    </template>

    <!-- STEP 1: Pakket kiezen -->
    <template x-if="step === 1">
      <div>
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-md font-semibold">Kies je pakket</h3>

          <!-- Adaptieve navigatieknop -->
          <button type="button"
                  class="w-8 h-8 cursor-pointer rounded-lg border border-gray-300 text-sm hover:bg-gray-50 transition flex items-center justify-between"
                  x-show="swiperReady && (swiperAtStart || swiperAtEnd)"
                  x-transition
                  @click="swiperAtStart ? swiperNext() : swiperPrev()">
            <i x-show="swiperAtStart" class="fa-solid fa-right-long fa-sm pl-2.25 opacity-25"></i>
            <i x-show="swiperAtEnd" class="fa-solid fa-right-long fa-sm pr-2 opacity-25 fa-flip-horizontal"></i>
          </button>
        </div>

        <div class="relative mb-6">
          <div class="swiper packages-swiper px-6">
            <div class="swiper-wrapper">
@php
  $packages = [
    [
      'key' => 'pakket_c',
      'title' => 'Elite Hyrox Pakket',
      'price' => ['label' => 'Vanaf 120,-', 'suffix' => '/ per 4 weken', 'total' => '≈ €360 totaal bij 12 weken'],
      'badge' => '10,- korting per 4 weken bij 24 weken traject!',
      'cta' => ['discount' => '10,- Korting'],
      'feature_groups' => [
        ['title' => 'Dashboard','items' => [
          ['text' => 'Toegang tot jouw persoonlijke omgeving', 'on' => true],
          ['text' => '30 min call met coach', 'on' => true],
        ]],
        ['title' => 'Gratis intake','items' => [
          ['text' => 'Vragenlijst voor jouw persoonlijke trainingsschema', 'on' => true],
        ]],
        ['title' => 'Intesten','items' => [
          ['text' => 'Afstemmen trainingsprogramma', 'on' => true],
        ]],
        ['title' => 'Trainingsschema','items' => [
          ['text' => 'Persoonlijk afgestemd', 'on' => true],
          ['text' => 'Tot 7 dagen per week gevuld', 'on' => true],
          ['text' => 'Na 11 weken check-up, bij 24 weken na 11 weken een tussenmeting', 'on' => true],
          ['text' => 'Mogelijkheid tot aanpassen trainingsplan, indien nodig maandelijks', 'on' => true],
          ['text' => '1x per maand live video-call van 20-30 min voor vragen en uitleg', 'on' => true],
        ]],
        ['title' => 'Begeleiding Trainingsschema','items' => [
          ['text' => 'Inzage techniek en filmpjes', 'on' => true],
          ['text' => 'Optie tot vragen stellen via chat', 'on' => true],
          ['text' => 'Maandelijkse check-up trainingsplan', 'on' => true],
          ['text' => 'Updates via de app', 'on' => true],
          ['text' => 'Mogelijkheid tot aanpassen trainingsplan 1x per 12 weken', 'on' => true],
        ]],
        ['title' => 'Eindtest','items' => [
          ['text' => 'Progressie vaststellen', 'on' => true],
          ['text' => 'E.v.t. vervolg trainingsschema, volgende trasiningsdoelen stellen', 'on' => true],
          ['text' => '30 min call met coach t.b.v. evaluatie trainingsplan', 'on' => true],
        ]],
        ['title' => 'Kortingen + Prijzen','items' => [
          ['text' => '10% militair / veteraan korting', 'on' => true],
          ['text' => '15% 2BeFit Supplements korting', 'on' => true],
          ['text' => '15% PT 2BeFit korting', 'on' => true],
          ['text' => '25% Duo PT 2BeFit korting', 'on' => true],
          ['text' => 'Prijs per 4 weken', 'on' => true],
        ]],
      ],
    ],
    [
      'key' => 'pakket_b',
      'title' => 'Chasing Goals Pakket',
      'price' => ['label' => 'Vanaf 75,-', 'suffix' => '/ per 4 weken', 'total' => '≈ €225 totaal bij 12 weken'],
      'badge' => '5,- korting per 4 weken bij 24 weken traject!',
      'cta' => ['discount' => '5,- Korting'],
      'feature_groups' => [
        ['title' => 'Dashboard','items' => [
          ['text' => 'Toegang tot jouw persoonlijke omgeving', 'on' => true],
          ['text' => '30 min call met coach', 'on' => true],
        ]],
        ['title' => 'Gratis intake','items' => [
          ['text' => 'Vragenlijst voor jouw persoonlijke trainingsschema', 'on' => true],
        ]],
        ['title' => 'Intesten','items' => [
          ['text' => 'Afstemmen trainingsprogramma', 'on' => true],
        ]],
        ['title' => 'Trainingsschema','items' => [
          ['text' => 'Persoonlijk afgestemd', 'on' => true],
          ['text' => 'Tot 6 dagen per week gevuld', 'on' => true],
          ['text' => 'Na 11 weken check-up, bij 24 weken na 11 weken een tussenmeting', 'on' => true],
          ['text' => 'Mogelijkheid tot aanpassen trainingsplan 1x per 12 weken indien nodig', 'on' => true],
          ['text' => '1x per maand live video-call van 20-30 min voor vragen en uitleg', 'on' => false],
        ]],
        ['title' => 'Begeleiding Trainingsschema','items' => [
          ['text' => 'Inzage techniek en filmpjes', 'on' => true],
          ['text' => 'Optie tot vragen stellen via chat', 'on' => true],
          ['text' => 'Maandelijkse check-up trainingsplan', 'on' => true],
          ['text' => 'Updates via de app', 'on' => true],
          ['text' => 'Mogelijkheid tot aanpassen trainingsplan 1x per 12 weken', 'on' => true],
        ]],
        ['title' => 'Eindtest','items' => [
          ['text' => 'Progressie vaststellen', 'on' => true],
          ['text' => 'E.v.t. vervolg trainingsschema, volgende trasiningsdoelen stellen', 'on' => true],
          ['text' => '30 min call met coach t.b.v. evaluatie trainingsplan', 'on' => true],
        ]],
        ['title' => 'Kortingen + Prijzen','items' => [
          ['text' => '10% militair / veteraan korting', 'on' => true],
          ['text' => '15% 2BeFit Supplements korting', 'on' => true],
          ['text' => '10% PT 2BeFit korting', 'on' => true],
          ['text' => 'Prijs per 4 weken', 'on' => true],
        ]],
      ],
    ],
    [
      'key' => 'pakket_a',
      'title' => 'Basis Pakket',
      'price' => ['label' => 'Vanaf 50,-', 'suffix' => '/ per 4 weken', 'total' => '≈ €150 totaal bij 12 weken'],
      'badge' => '5,- korting per 4 weken bij 24 weken traject!',
      'cta' => ['discount' => '5,- Korting'],
      'feature_groups' => [
        ['title' => 'Dashboard','items' => [
          ['text' => 'Toegang tot jouw persoonlijke omgeving', 'on' => true],
          ['text' => '30 min call met coach', 'on' => false],
        ]],
        ['title' => 'Gratis intake','items' => [
          ['text' => 'Vragenlijst voor jouw persoonlijke trainingsschema', 'on' => true],
        ]],
        ['title' => 'Intesten','items' => [
          ['text' => 'Afstemmen trainingsprogramma', 'on' => true],
        ]],
        ['title' => 'Trainingsschema','items' => [
          ['text' => 'Persoonlijk afgestemd', 'on' => true],
          ['text' => 'Tot 6 dagen per week gevuld', 'on' => true],
          ['text' => 'Na 11 weken check-up, bij 24 weken na 11 weken een tussenmeting', 'on' => true],
          ['text' => 'Mogelijkheid tot aanpassen trainingsplan 1x per 12 weken indien nodig', 'on' => false],
          ['text' => '1x per maand live video-call van 20-30 min voor vragen en uitleg', 'on' => false],
        ]],
        ['title' => 'Begeleiding Trainingsschema','items' => [
          ['text' => 'Inzage techniek en filmpjes', 'on' => true],
          ['text' => 'Optie tot vragen stellen via chat', 'on' => true],
          ['text' => 'Maandelijkse check-up trainingsplan', 'on' => false],
          ['text' => 'Updates via de app', 'on' => false],
          ['text' => 'Mogelijkheid tot aanpassen trainingsplan 1x per 12 weken', 'on' => false],
        ]],
        ['title' => 'Eindtest','items' => [
          ['text' => 'Progressie vaststellen', 'on' => true],
          ['text' => 'E.v.t. vervolg trainingsschema, volgende trasiningsdoelen stellen', 'on' => true],
          ['text' => '30 min call met coach t.b.v. evaluatie trainingsplan', 'on' => false],
        ]],
        ['title' => 'Kortingen + Prijzen','items' => [
          ['text' => '10% militair / veteraan korting', 'on' => true],
          ['text' => 'Prijs per 4 weken', 'on' => true],
        ]],
      ],
    ],
  ];
@endphp

@foreach ($packages as $pkg)
  <div class="swiper-slide">
    <div class="relative block rounded-2xl border p-4 transition hover:shadow-sm border-gray-300 h-full">
      <div class="flex flex-col gap-6 h-full">
        <div class="flex flex-col gap-4">
          <h4 class="font-bold text-lg text-black -mb-4">{{ $pkg['title'] }}</h4>

          <div>
            <p class="text-3xl font-black text-black">
              {{ $pkg['price']['label'] }} <span class="text-sm">{{ $pkg['price']['suffix'] }}</span>
            </p>
            <span class="text-xs text-gray-500">{{ $pkg['price']['total'] }}</span>
          </div>

          <p class="px-2 py-1 bg-[#c8ab7a]/75 text-white text-xs font-semibold rounded w-fit">
            {{ $pkg['badge'] }}
          </p>

          <hr class="border-gray-200">

          <ul class="flex flex-col gap-4">
            @foreach ($pkg['feature_groups'] as $group)
              <div class="flex flex-col gap-2">
                <li class="text-sm font-bold">{{ $group['title'] }}</li>
                @foreach ($group['items'] as $item)
                  @php $on = $item['on']; @endphp
                  <li class="flex items-center gap-4">
                    <i class="fa-solid fa-check {{ $on ? 'text-green-500' : 'text-gray-300' }}"></i>
                    <p class="text-xs font-semibold {{ $on ? 'text-black' : 'text-gray-400' }}">{{ $item['text'] }}</p>
                  </li>
                @endforeach
              </div>
            @endforeach
          </ul>

          <hr class="border-gray-200">
        </div>

        <div class="mt-auto flex flex-wrap gap-2">
          <button type="button" class="{{ $btnPrimary }}"
                  :disabled="isPaying"
                  @click="choosePackage('{{ $pkg['key'] }}', 24)">
            24 weken traject kiezen
            <span class="px-2 py-1 text-xs ml-2 rounded bg-[#e5c791]">{{ $pkg['cta']['discount'] }}</span>
          </button>

          <button type="button" class="{{ $btnPrimary }}"
                  :disabled="isPaying"
                  @click="choosePackage('{{ $pkg['key'] }}', 12)">
            12 weken traject kiezen
          </button>
        </div>
      </div>
    </div>
  </div>
@endforeach

            </div>
          </div>
        </div>

        <div class="flex items-center justify-between gap-2">
          <button type="button" class="{{ $btnGhost }}" @click="prev()">Vorige</button>
          <!-- keuze gebeurt via buttons -->
        </div>
      </div>
    </template>

    <!-- STEP 2: Overzicht -->
    <template x-if="step === 2">
      <div class="mt-2">
        <h3 class="text-md font-semibold mb-4">Overzicht</h3>
        <div class="text-sm mb-4 space-y-1">
          <p><strong>Naam:</strong> <span x-text="form.name || '-'"></span></p>
          <p><strong>E-mail:</strong> <span x-text="form.email || '-'"></span></p>
          <p><strong>Telefoon:</strong> <span x-text="form.phone || '-'"></span></p>
          <p><strong>Geboortedatum:</strong> <span x-text="form.dob || '-'"></span></p>
          <p><strong>Geslacht:</strong> <span x-text="form.gender || '-'"></span></p>
          <p><strong>Adres:</strong>
            <span x-text="(form.street || '-') + ' ' + (form.house_number || '') + (form.house_number ? ', ' : '') + (form.postcode || '-')"></span>
          </p>
          <p><strong>Gekozen pakket:</strong> <span x-text="packageLabel(form.package) || '-'"></span></p>
          <p><strong>Looptijd:</strong> <span x-text="form.duration ? (form.duration + ' weken') : '-'"></span></p>
        </div>

        <div class="flex items-center gap-2">
          <button type="button" class="{{ $btnGhost }}" @click="prev()">Vorige</button>
          <button type="button" class="{{ $btnPrimary }}" @click="submit()">Versturen</button>
        </div>
      </div>
    </template>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
  function intakeWizard() {
    const STORAGE_KEY = 'intakeWizard_v1';

    return {
      isPaying: false,

      // stappen
      steps: [0, 1, 2],
      step: 0,
      get totalSteps() { return this.steps.length },

      // formulier
      form: {
        name: '',
        email: '',
        phone: '',
        dob: '',
        gender: '',          // man | vrouw
        street: '',
        house_number: '',
        postcode: '',        // NL: 1234 AB, BE: 1000-9999
        package: '',         // pakket_a | pakket_b | pakket_c
        duration: null,      // 12 | 24
      },

      // state
      errors: {},
      _iti: null,
      _saveDebounced: null,

      // --- Swiper state ---
      _swiper: null,
      swiperReady: false,
      swiperAtStart: true,
      swiperAtEnd: false,

      // utils
      debounce(fn, delay = 250) {
        let t;
        return (...args) => { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), delay); };
      },
      saveState() {
        try { localStorage.setItem(STORAGE_KEY, JSON.stringify({ step: this.step, form: this.form })); } catch (e) {}
      },
      loadState() {
        try {
          const raw = localStorage.getItem(STORAGE_KEY);
          if (!raw) return;
          const parsed = JSON.parse(raw);
          if (parsed?.form) this.form = { ...this.form, ...parsed.form };
          if (Number.isInteger(parsed?.step) && parsed.step >= 0 && parsed.step < this.totalSteps) this.step = parsed.step;
        } catch (e) {}
      },
      clearState() { try { localStorage.removeItem(STORAGE_KEY); } catch (e) {} },

      init() {
        this.loadState();

        // --- URL hints (alleen toepassen als we net van checkout komen) ---
        const params   = new URLSearchParams(window.location.search);
        const urlStep  = parseInt(params.get('step'), 10);
        const advance  = params.get('advance') === '1';
        const canceled = params.get('canceled') === '1';

        // Waren we onderweg naar checkout?
        let cameFromCheckout = false;
        try { cameFromCheckout = sessionStorage.getItem('intakePending') === '1'; } catch (e) {}

        // 'step' uit URL mag altijd (handig voor directe deeplinks), maar clamp hem wel
        if (!Number.isNaN(urlStep)) {
          this.step = Math.min(Math.max(urlStep, 0), this.totalSteps - 1);
        }

        // Alleen verwerken als we echt uit checkout komen
        if (cameFromCheckout && advance) {
          // Succes → 1 stap verder
          this.step = Math.min(this.step + 1, this.totalSteps - 1);
        }
        if (cameFromCheckout && canceled) {
          // Geannuleerd → terug naar stap 1 (kies pakket)
          this.step = 1;
        }

        // Flag altijd opruimen zodat refresh niets opnieuw triggert
        try { sessionStorage.removeItem('intakePending'); } catch (e) {}

        // Fallback: als step door wat dan ook buiten bereik is → 0
        if (![0, 1, 2].includes(this.step)) this.step = 0;

        // autosave + watchers
        this._saveDebounced = this.debounce(() => this.saveState(), 200);
        this.$watch('form', () => this._saveDebounced());
        this.$watch('step', (val, oldVal) => {
          this._saveDebounced();
          if (oldVal === 0) this.destroyTelInput();
          if (val === 0) this.$nextTick(() => this.initTelInput());
          if (val === 1) { this.$nextTick(() => { initPackagesSwiperAndBind(); }); }
          else if (oldVal === 1) { this.destroyPackagesSwiper(); }
        });

        // init op first paint
        this.$nextTick(() => {
          const el = document.getElementById('dob');
          if (el) {
            const today = new Date();
            const max = today.toISOString().split('T')[0];
            const minDate = new Date(today.getFullYear() - 80, today.getMonth(), today.getDate());
            const min = minDate.toISOString().split('T')[0];
            el.setAttribute('min', min);
            el.setAttribute('max', max);
          }
          if (this.step === 0) this.initTelInput();
          if (this.step === 1) initPackagesSwiperAndBind();

          window.addEventListener('packages-swiper-ready', (e) => {
            const s = e?.detail?.swiper; if (!s) return;
            this.bindSwiper(s);
            this.updateSwiperEdges();
            requestAnimationFrame(() => this.updateSwiperEdges());
          });

          // Optioneel: maak de URL schoon (verwijder advance/canceled/step uit adresbalk)
          if (advance || canceled || !Number.isNaN(urlStep)) {
            const cleanUrl = window.location.pathname;
            history.replaceState(null, '', cleanUrl);
          }
        });
      },

      packageLabel(key) {
        if (key === 'pakket_a') return 'Basis Pakket';
        if (key === 'pakket_b') return 'Chasing Goals Pakket';
        if (key === 'pakket_c') return 'Elite Hyrox Pakket';
        return '';
      },

      // kiezen via card-buttons → direct naar betalen
      choosePackage(pkg, weeks) {
        this.form.package  = pkg;
        this.form.duration = weeks;
        delete this.errors.package;
        delete this.errors.duration;
        this.submit();
      },

      next() {
        if (!this.validateStep(this.step)) return;
        if (this.step < this.totalSteps - 1) this.step++;
      },
      prev() { if (this.step > 0) this.step--; },

      // === BELANGRIJK: valideer op data, niet op DOM ===
      validateStep(stepIndex) {
        this.errors = {};
        let firstInvalidEl = null;

        if (stepIndex === 0) {
          // NAAM
          if (!this.form.name?.trim()) {
            this.errors.name = 'Vul je volledige naam in.';
            firstInvalidEl = firstInvalidEl || document.getElementById('name');
          }

          // EMAIL
          const email = this.form.email?.trim() || '';
          if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            this.errors.email = 'Vul een geldig e-mailadres in.';
            firstInvalidEl = firstInvalidEl || document.getElementById('email');
          }

          // TELEFOON
          const phoneVal = (this.form.phone || '').trim();
          if (!phoneVal) {
            this.errors.phone = 'Vul je telefoonnummer in.';
            firstInvalidEl = firstInvalidEl || document.getElementById('phone');
          }

          // GEBOORTEDATUM
          const dob = this.form.dob;
          if (!dob) {
            this.errors.dob = 'Kies je geboortedatum.';
            firstInvalidEl = firstInvalidEl || document.getElementById('dob');
          } else {
            const v = new Date(dob);
            const today = new Date();
            const max = today.toISOString().split('T')[0];
            const minDate = new Date(today.getFullYear() - 80, today.getMonth(), today.getDate());
            const min = minDate.toISOString().split('T')[0];
            if (Number.isNaN(v.getTime()) || dob < min || dob > max) {
              this.errors.dob = 'Geboortedatum is ongeldig.';
              firstInvalidEl = firstInvalidEl || document.getElementById('dob');
            }
          }

          // GESLACHT
          if (!this.form.gender) {
            this.errors.gender = 'Kies je geslacht.';
          }

          // ADRES
          if (!this.form.street?.trim()) {
            this.errors.street = 'Vul je straatnaam in.';
            firstInvalidEl = firstInvalidEl || document.getElementById('street');
          }
          if (!this.form.house_number?.trim()) {
            this.errors.house_number = 'Vul je huisnummer in.';
            firstInvalidEl = firstInvalidEl || document.getElementById('house_number');
          }

          // POSTCODE
          const pcRaw = (this.form.postcode || '').trim().toUpperCase();
          const pc = pcRaw.replace(/\s+/g, ' ');
          const nlRe = /^\d{4}\s?[A-Z]{2}$/;
          const beRe = /^\d{4}$/;
          let okPostcode = nlRe.test(pc);
          if (!okPostcode && beRe.test(pc)) {
            const num = parseInt(pc, 10);
            okPostcode = num >= 1000 && num <= 9999;
          }
          if (!okPostcode) {
            this.errors.postcode = 'Vul een geldige postcode in (NL: 1234 AB, BE: 1000–9999).';
            firstInvalidEl = firstInvalidEl || document.getElementById('postcode');
          } else {
            this.form.postcode = pc;
          }
        }

        if (stepIndex === 1) {
          if (!this.form.package)  this.errors.package  = 'Kies een pakket via de knoppen.';
          if (!this.form.duration) this.errors.duration = 'Kies ook de duur (12 of 24 weken).';
        }

        this.$nextTick(() => { if (firstInvalidEl) firstInvalidEl.focus?.(); });
        return Object.keys(this.errors).length === 0;
      }, // <-- KOMMA TOEGEVOEGD

      submit() {
        if (this.isPaying) return;

        // 1) Valideer stap 0
        const ok0 = this.validateStep(0);
        if (!ok0) {
          this.step = 0;
          this.$nextTick(() => {
            const wrap = document.getElementById('stap-1');
            if (wrap?.scrollIntoView) wrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
            const first = document.querySelector(
              '#name.border-red-500, #email.border-red-500, #phone.border-red-500, #dob.border-red-500, #street.border-red-500, #house_number.border-red-500, #postcode.border-red-500'
            );
            first?.focus?.();
          });
          return;
        }

        // 2) Valideer stap 1
        const ok1 = this.validateStep(1);
        if (!ok1) {
          this.step = 1;
          return;
        }

        // 3) Alles oké → betalen
        this.isPaying = true;

        const payload = {
          name: this.form.name,
          email: this.form.email,
          phone: this.form.phone,
          dob: this.form.dob,
          gender: this.form.gender,
          street: this.form.street,
          house_number: this.form.house_number,
          postcode: this.form.postcode,
          package: this.form.package,
          duration: this.form.duration
        };

        // CSRF-token veilig ophalen (kan in layout ontbreken)
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';

        fetch('{{ route('intake.checkout') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {})
          },
          body: JSON.stringify(payload)
        })
        .then(async (res) => {
          if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.message || 'Kon betaalpagina niet starten.');
          }
          return res.json();
        })
        .then(({ redirect_url }) => {
          try { sessionStorage.setItem('intakePending', '1'); } catch (e) {}
          window.location.href = redirect_url;
        })
        .catch((e) => {
          console.error('Checkout error:', e);
          this.errors.general = e.message || 'Er ging iets mis bij het starten van de betaling.';
        })
        .finally(() => { this.isPaying = false; });
      }, // <-- KOMMA TOEGEVOEGD

      // --- intl-tel-input ---
      initTelInput() {
        const input = document.getElementById('phone');
        if (!input) return;

        if (input.parentElement?.classList.contains('iti') && this._iti) return;

        this._iti = window.intlTelInput(input, {
          initialCountry: "nl",
          onlyCountries: ["nl", "be"],
          separateDialCode: true,
          utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
        });

        if (this.form.phone) { try { this._iti.setNumber(this.form.phone); } catch (e) {} }

        input.addEventListener('blur', () => {
          if (this._iti && this._iti.isValidNumber()) {
            this.form.phone = this._iti.getNumber();
            this.errors.phone = '';
          }
        });
      },
      destroyTelInput() {
        if (this._iti) {
          try { this._iti.destroy(); } catch (e) {}
          this._iti = null;
        }
      },

      // --- Swiper binding & helpers ---
      bindSwiper(swiper) {
        this._swiper = swiper;
        this.swiperReady = true;
        this.updateSwiperEdges();

        swiper.on('slideChange resize transitionEnd reachBeginning reachEnd fromEdge', () => {
          this.updateSwiperEdges();
        });

        setTimeout(() => this.updateSwiperEdges(), 0);
      },
      updateSwiperEdges() {
        if (!this._swiper) return;
        this.swiperAtStart = this._swiper.isBeginning;
        this.swiperAtEnd   = this._swiper.isEnd;
      },
      swiperNext() { if (this._swiper) this._swiper.slideNext(); },
      swiperPrev() { if (this._swiper) this._swiper.slidePrev(); },
      destroyPackagesSwiper() {
        if (this._swiper) { this._swiper.destroy(true, true); this._swiper = null; }
        this.swiperReady = false;
        this.swiperAtStart = true;
        this.swiperAtEnd = false;
      },
    }
  }

  // Init Swiper wanneer stap 1 zichtbaar is en bind via event aan Alpine
  function initPackagesSwiperAndBind() {
    const el = document.querySelector('.packages-swiper');
    if (!el || typeof Swiper === 'undefined') return null;

    const swiper = new Swiper(el, {
      slidesPerView: 1,
      spaceBetween: 16,
      grabCursor: false,
      watchOverflow: true,
      keyboard: { enabled: true },
      a11y: { enabled: true },
      observer: true,
      observeParents: true,
      observeSlideChildren: true,
      breakpoints: {
        768:  { slidesPerView: 2, spaceBetween: 20 },
        1024: { slidesPerView: 2, spaceBetween: 24 },
      },
      on: {
        init(s) {
          window.dispatchEvent(new CustomEvent('packages-swiper-ready', { detail: { swiper: s } }));
          requestAnimationFrame(() => { s.update(); });
        }
      }
    });

    if (!swiper.initialized && typeof swiper.init === 'function') swiper.init();
    return swiper;
  }
</script>

@endsection
