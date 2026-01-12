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
  $ak = session('ak');  // ['package'=>..., 'duration'=>...]
@endphp

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<div class="max-w-3xl mx-auto" x-data="intakeWizard({{ json_encode($ak ?? []) }}, {{ json_encode($renewData ?? null) }}, {{ json_encode($isRenew ?? false) }})" x-init="init()">
  @if(request()->has('key'))
    <script>
      (function () {
        try {
          // Intake wizard state + eventuele checkout-flags opruimen
          localStorage.removeItem('intakeWizard_v1');
          sessionStorage.removeItem('intakePending');
          sessionStorage.removeItem('intakeConfirmed');
        } catch (e) {}

        // Key direct uit de URL strippen zodat refresh "schoon" is
        try {
          var url = new URL(window.location.href);
          url.searchParams.delete('key');
          var qs = url.searchParams.toString();
          var clean = url.pathname + (qs ? '?' + qs : '') + url.hash;
          history.replaceState(null, '', clean);
        } catch (e) {}
      })();
    </script>
  @endif
  @if($isRenew ?? false)
    <script>
      (function () {
        try {
          // Bij renew: localStorage opruimen zodat we met schone lei beginnen
          localStorage.removeItem('intakeWizard_v1');
          sessionStorage.removeItem('intakePending');
          sessionStorage.removeItem('intakeConfirmed');
        } catch (e) {}

        // Renew parameter uit URL strippen na verwerking
        try {
          var url = new URL(window.location.href);
          url.searchParams.delete('renew');
          var qs = url.searchParams.toString();
          var clean = url.pathname + (qs ? '?' + qs : '') + url.hash;
          history.replaceState(null, '', clean);
        } catch (e) {}
      })();
    </script>
  @endif
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
    Goed om je te zien en welkom bij 2BeFit Coaching X Team Verhoeven<br class="hidden md:block">
    Laten we even je persoonlijke profiel samenstellen. Op basis hiervan worden de trainingen samengesteld.
  </p>
  @if($isRenew ?? false)
    <div class="mb-4 rounded-xl border border-blue-300 bg-blue-50 text-blue-800 p-3 text-sm">
      <strong><i class="fa-solid fa-arrow-rotate-right mr-1"></i> Abonnement verlengen</strong><br>
      Je persoonlijke gegevens en coach voorkeur zijn al ingevuld. Kies een nieuw pakket en vul de rest van de intake in om je abonnement te verlengen.
    </div>
  @endif
  @if(!empty($ak))
    <div class="mb-4 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 p-3 text-sm">
      <strong>Key geactiveerd:</strong><br>
      Dit intake-traject wordt afgehandeld zonder betaling.<br><br>
      Jouw pakket: <strong>{{ $ak['package'] === 'pakket_a' ? 'Basis' : ($ak['package'] === 'pakket_b' ? 'Chasing Goals' : 'Elite Hyrox') }}</strong><br>
      Traject: <strong>{{ $ak['duration'] }}</strong> weken.
    </div>
  @endif

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
          <div class="flex flex-col md:flex-row gap-4">
            <div class="w-full md:w-3/5 min-w-0">
              <p class="text-sm font-medium text-black mb-1">Wat is je emailadres?</p>
              <input id="email" type="email" name="email" x-model="form.email" required
                     class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm
                            border-gray-300 hover:border-[#c7c7c7]"
                     :class="errors.email ? 'border-red-500 focus:border-red-500' : ''">
            </div>

            <div class="w-full md:w-2/5 min-w-0">
              <p class="text-sm font-medium text-black mb-1">Wat is je telefoonnummer?</p>
              <div class="w-full">
                <input id="phone" type="tel" name="phone" required
                  x-model="form.phone"
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

          {{-- Startdatum: altijd tonen op normale plek --}}
          <div>
            <p class="text-sm font-medium text-black mb-1">Wanneer wil je beginnen?</p>
            <input id="start_date" type="date" name="start_date" x-model="form.start_date" required
                  class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm
                          border-gray-300 hover:border-[#c7c7c7]"
                  :class="errors.start_date ? 'border-red-500 focus:border-red-500' : ''">
          </div>

          <!-- Adres -->
          <div>
            <p class="text-sm font-medium text-black mb-2">Wat is je adres?</p>
            <div class="flex flex-col md:flex-row gap-4">
              <div class="flex-1 min-w-0">
                <input id="street" type="text" name="street" x-model="form.street" placeholder="Straatnaam"
                       class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm
                              border-gray-300 hover:border-[#c7c7c7]"
                       :class="errors.street ? 'border-red-500 focus:border-red-500' : ''">
              </div>
              <div class="w-full md:w-34 min-w-0">
                <input id="house_number" type="text" name="house_number" x-model="form.house_number" placeholder="Nr."
                       class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm
                              border-gray-300 hover:border-[#c7c7c7]"
                       :class="errors.house_number ? 'border-red-500 focus:border-red-500' : ''">
              </div>
              <div class="w-full md:w-44 min-w-0">
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

    <!-- STEP 1: Coach voorkeur -->
    <template x-if="step === 1">
      <div>
        <h3 class="text-md font-semibold mb-4">Welke coach heeft je voorkeur?</h3>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
          <label class="p-3 rounded-xl border border-gray-300 cursor-pointer hover:bg-gray-50 transition duration-300">
            <img src="/assets/nicky.webp" alt="Nicky" class="mb-4">
            <div class="flex items-center gap-3 ">
              <input type="radio" name="preferred_coach" value="nicky" x-model="form.preferred_coach" class="sr-only peer">
              <span class="w-4 h-4 rounded-full border border-gray-300 inline-flex items-center justify-center
                          peer-checked:bg-[#c8ab7a] peer-checked:border-[#c8ab7a]">
              </span>
              <span class="text-sm font-medium">Nicky</span>
            </div>
          </label>
          
          <label class="p-3 rounded-xl border border-gray-300 cursor-pointer hover:bg-gray-50 transition duration-300">
            <img src="/assets/eline.webp" alt="Eline" class="mb-4">
            <div class="flex items-center gap-3 ">
              <input type="radio" name="preferred_coach" value="eline" x-model="form.preferred_coach" class="sr-only peer">
              <span class="w-4 h-4 rounded-full border border-gray-300 inline-flex items-center justify-center
              peer-checked:bg-[#c8ab7a] peer-checked:border-[#c8ab7a]">
            </span>
            <span class="text-sm font-medium">Eline</span>
          </div>
        </label>
        
        <label class="p-3 rounded-xl border border-gray-300 cursor-pointer hover:bg-gray-50 transition duration-300">
          <img src="/assets/roy.webp" alt="Roy" class="mb-4">
          <div class="flex items-center gap-3 ">
            <input type="radio" name="preferred_coach" value="roy"   x-model="form.preferred_coach" class="sr-only peer">
            <span class="w-4 h-4 rounded-full border border-gray-300 inline-flex items-center justify-center
                        peer-checked:bg-[#c8ab7a] peer-checked:border-[#c8ab7a]">
            </span>
            <span class="text-sm font-medium">Roy</span>
          </div>
        </label>

          <label class="p-3 rounded-xl border border-gray-300 cursor-pointer hover:bg-gray-50 transition duration-300">
            <div class="w-full h-[223.2px] bg-gray-100 rounded-xl mb-4 flex items-center justify-center">
              <i class="fa-solid fa-user fa-2xl text-gray-300"></i>
            </div>
            <div class="flex items-center gap-3">
              <input type="radio" name="preferred_coach" value="none"  x-model="form.preferred_coach" class="sr-only peer">
              <span class="w-4 h-4 rounded-full border border-gray-300 inline-flex items-center justify-center
                          peer-checked:bg-[#c8ab7a] peer-checked:border-[#c8ab7a]">
              </span>
              <span class="text-sm font-medium">Geen voorkeur</span>
            </div>
          </label>
        </div>

        <div class="flex items-center justify-between gap-2">
          <button type="button" class="{{ $btnGhost }}" @click="prev()">Vorige</button>
          <button type="button" class="{{ $btnPrimary }}" @click="next()">Volgende stap</button>
        </div>
      </div>
    </template>

    <!-- STEP 2: Pakket kiezen -->
    <template x-if="!hasKey && step === 2">
      <div>
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-md font-semibold">Kies je pakket</h3>

          <div class="flex items-center gap-2">
            <!-- Prev -->
            <button type="button"
                    class="w-8 h-8 rounded-lg border border-gray-300 text-sm transition flex items-center justify-center hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed"
                    x-show="swiperReady"
                    :disabled="swiperAtStart"
                    @click="swiperPrev()"
                    aria-label="Vorige">
              <i class="fa-solid fa-right-long fa-sm fa-flip-horizontal"></i>
            </button>

            <!-- Next -->
            <button type="button"
                    class="w-8 h-8 rounded-lg border border-gray-300 text-sm transition flex items-center justify-center hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed"
                    x-show="swiperReady"
                    :disabled="swiperAtEnd"
                    @click="swiperNext()"
                    aria-label="Volgende">
              <i class="fa-solid fa-right-long fa-sm"></i>
            </button>
          </div>
        </div>

        <div class="relative mb-6">
          {{-- Bij renew: toon startdatum veld hier ipv stap 0 --}}
          <template x-if="isRenew">
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
              <p class="text-sm font-medium text-black mb-2">
                <i class="fa-solid fa-calendar-days mr-1 text-blue-500"></i>
                Wanneer wil je je nieuwe traject starten?
              </p>
              <input id="start_date_renew" type="date" name="start_date" x-model="form.start_date" required
                     class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm
                            border-gray-300 hover:border-[#c7c7c7]"
                     :class="errors.start_date ? 'border-red-500 focus:border-red-500' : ''">
              <p class="text-xs text-gray-500 mt-1">Kies een maandag als startdatum</p>
            </div>
          </template>

          <div class="swiper packages-swiper px-6">
            <div class="swiper-wrapper">
              @php
                $packages = [
                  [
                    'key' => 'pakket_c',
                    'title' => 'Elite Hyrox Pakket',
                    'price' => ['label' => 'Vanaf 130,-', 'suffix' => '/ per 4 weken', 'total' => '≈ €390 totaal bij 12 weken'],
                    'badge' => '10,- korting per 4 weken bij 24 weken traject!',
                    'cta' => ['discount' => '10,- Korting'],
                    'price_12w' => 140,
                    'price_24w' => 130,
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
                        // ['text' => '10% militair / veteraan korting', 'on' => true],
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
                    'price' => ['label' => 'Vanaf 80,-', 'suffix' => '/ per 4 weken', 'total' => '≈ €240 totaal bij 12 weken'],
                    'badge' => '10,- korting per 4 weken bij 24 weken traject!',
                    'cta' => ['discount' => '10,- Korting'],
                    'price_12w' => 90,
                    'price_24w' => 80,
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
                        // ['text' => '10% militair / veteraan korting', 'on' => true],
                        ['text' => '15% 2BeFit Supplements korting', 'on' => true],
                        ['text' => '10% PT 2BeFit korting', 'on' => true],
                        ['text' => 'Prijs per 4 weken', 'on' => true],
                      ]],
                    ],
                  ],
                  [
                    'key' => 'pakket_a',
                    'title' => 'Basis Pakket',
                    'price' => ['label' => 'Vanaf 60,-', 'suffix' => '/ per 4 weken', 'total' => '≈ 180 totaal bij 24 weken'],
                    'badge' => '5,- korting per 4 weken bij 24 weken traject!',
                    'cta' => ['discount' => '5,- Korting'],
                    'price_12w' => 65,
                    'price_24w' => 60,
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
                        // ['text' => '10% militair / veteraan korting', 'on' => true],
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
                        <button type="button" class="cursor-pointer w-full py-3 bg-[#c8ab7a] hover:bg-[#a38b62] transition duration-300 text-white font-medium text-sm rounded"
                                :disabled="isPaying"
                                @click="choosePackage('{{ $pkg['key'] }}', 24)">
                          24 weken traject kiezen
                          <span class="px-2 py-1 text-xs ml-2 rounded bg-[#e5c791]">{{ $pkg['cta']['discount'] }}</span>
                        </button>

                        <button type="button" class="cursor-pointer w-full py-3 bg-[#c8ab7a] hover:bg-[#a38b62] transition duration-300 text-white font-medium text-sm rounded"
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

    <!-- STEP 3: Lengte & Gewicht -->
    <template x-if="step === 3">
      <div>
        <h3 class="text-md font-semibold mb-4">Lengte & Gewicht</h3>
        <div class="flex flex-col gap-4 mb-8">
          <div class="flex flex-col md:flex-row md:items-center gap-4">
            <div class="w-full md:w-1/2">
              <p class="text-sm font-medium text-black mb-1">Wat is je lengte (in cm)?</p>
              <div class="relative">
                <input
                  id="height_cm"
                  name="height_cm"
                  type="number"
                  inputmode="numeric"
                  min="120" max="250" step="1"
                  placeholder="bijv. 178"
                  x-model.number="form.height_cm"
                  class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
                  :class="errors.height_cm ? 'border-red-500 focus:border-red-500' : ''"
                >
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-black/50">cm</span>
              </div>
            </div>

            <div class="w-full md:w-1/2">
              <p class="text-sm font-medium text-black mb-1">Wat is je gewicht (in kg)?</p>
              <div class="relative">
                <input
                  id="weight_kg"
                  name="weight_kg"
                  type="number"
                  inputmode="decimal"
                  min="35" max="250" step="0.1"
                  placeholder="bijv. 74.5"
                  x-model.number="form.weight_kg"
                  class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
                  :class="errors.weight_kg ? 'border-red-500 focus:border-red-500' : ''"
                >
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-black/50">kg</span>
              </div>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between gap-2">
          <p></p>
          <button type="button" class="{{ $btnPrimary }}" @click="next()">Volgende stap</button>
        </div>
      </div>
    </template>

    <!-- STEP 4: Blessures -->
    <template x-if="step === 4">
      <div>
        <h3 class="text-md font-semibold mb-4">Blessures <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-500 ml-2">Optioneel</span></h3>
        <div class="flex flex-col gap-4 mb-6.5">
          <div>
            <p class="text-sm font-medium text-black mb-1">
              Heb je blessures of aandachtspunten waar we rekening mee moeten houden?
            </p>
            <div class="relative">
              <textarea
                id="injuries"
                name="injuries"
                rows="4"
                placeholder="Bijv. knieblessure links; rekening houden met hardlopen en diepe squats."
                x-model.trim="form.injuries"
                class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
                :class="errors.injuries ? 'border-red-500 focus:border-red-500' : ''"
              ></textarea>
              <!-- optioneel: teller -->
              <span class="absolute right-3 bottom-2 text-xs text-black/40"
                    x-text="(form.injuries || '').length + ' / 500'"></span>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between gap-2">
          <button type="button" class="{{ $btnGhost }}" @click="prev()">Vorige</button>
          <button type="button" class="{{ $btnPrimary }}" @click="next()">Volgende stap</button>
        </div>
      </div>
    </template>

    <!-- STEP 5: Doelen -->
    <template x-if="step === 5">
      <div>
        <h3 class="text-md font-semibold mb-4">Doelen</h3>
        <div class="flex flex-col gap-4 mb-6.5">
          <div>
            <p class="text-sm font-medium text-black mb-1">
              Wat zijn de doelen die je wilt bereiken?
            </p>
            <div class="relative">
              <textarea
                id="goals"
                name="goals"
                rows="4"
                placeholder="Bijv. 10 kg afvallen in 6 maanden; Hyrox halen onder 1:15; 3x per week consistent trainen."
                x-model.trim="form.goals"
                class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
                :class="errors.goals ? 'border-red-500 focus:border-red-500' : ''"
                maxlength="500"
              ></textarea>
              <span class="absolute right-3 bottom-2 text-xs text-black/40"
                    x-text="(form.goals || '').length + ' / 500'"></span>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between gap-2">
          <button type="button" class="{{ $btnGhost }}" @click="prev()">Vorige</button>
          <button type="button" class="{{ $btnPrimary }}" @click="next()">Volgende stap</button>
        </div>
      </div>
    </template>

    <!-- STEP 6: Sessies & Duur -->
    <template x-if="step === 6">
      <div>
        <h3 class="text-md font-semibold mb-4">Sessies & Duur</h3>

        <div class="flex flex-col gap-4 mb-8">
          <div class="flex flex-col md:flex-row md:items-center gap-4">
            <!-- Dagen per week -->
            <div class="w-full md:w-1/2">
              <p class="text-sm font-medium text-black mb-1">Hoeveel dagen per week wil je maximaal?</p>
              <div class="relative">
                <input
                  id="max_days_per_week"
                  name="max_days_per_week"
                  type="number"
                  inputmode="numeric"
                  min="1" max="7" step="1"
                  placeholder="bijv. 4"
                  x-model.number="form.max_days_per_week"
                  class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
                  :class="errors.max_days_per_week ? 'border-red-500 focus:border-red-500' : ''"
                >
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-black/50">dagen</span>
              </div>
            </div>

            <!-- Duur per sessie (minuten) -->
            <div class="w-full md:w-1/2">
              <p class="text-sm font-medium text-black mb-1">Hoelang wil je trainen per sessie?</p>
              <div class="relative">
                <input
                  id="session_minutes"
                  name="session_minutes"
                  type="number"
                  inputmode="numeric"
                  min="20" max="180" step="5"
                  placeholder="bijv. 60"
                  x-model.number="form.session_minutes"
                  class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
                  :class="errors.session_minutes ? 'border-red-500 focus:border-red-500' : ''"
                >
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-black/50">min</span>
              </div>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between gap-2">
          <button type="button" class="{{ $btnGhost }}" @click="prev()">Vorige</button>
          <button type="button" class="{{ $btnPrimary }}" @click="next()">Volgende stap</button>
        </div>
      </div>
    </template>

    <!-- STEP 7: Sportachtergrond -->
    <template x-if="step === 7">
      <div>
        <h3 class="text-md font-semibold mb-4">Sportachtergrond <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-500 ml-2">Optioneel</span></h3>

        <div class="flex flex-col gap-4 mb-6.5">
          <div>
            <p class="text-sm font-medium text-black mb-1">
              Zou je wat kunnen vertellen over je sportachtergrond?
            </p>
            <div class="relative">
              <textarea
                id="sport_background"
                name="sport_background"
                rows="4"
                placeholder="Bijv. eerdere sportervaring (teamsport, krachtsport, duursport), huidige niveau, frequentie, recente pauzes/blessures, etc."
                x-model.trim="form.sport_background"
                class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
                :class="errors.sport_background ? 'border-red-500 focus:border-red-500' : ''"
                maxlength="500"
              ></textarea>
              <span class="absolute right-3 bottom-2 text-xs text-black/40"
                    x-text="(form.sport_background || '').length + ' / 500'"></span>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between gap-2">
          <button type="button" class="{{ $btnGhost }}" @click="prev()">Vorige</button>
          <button type="button" class="{{ $btnPrimary }}" @click="next()">Volgende stap</button>
        </div>
      </div>
    </template>

    <!-- STEP 8: Faciliteiten -->
    <template x-if="step === 8">
      <div>
        <h3 class="text-md font-semibold mb-4">
          Faciliteiten
          <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-500 ml-2">Optioneel</span>
        </h3>

        <div class="flex flex-col gap-4 mb-6">
          <div>
            <p class="text-sm font-medium text-black mb-1">
              Welke faciliteiten heb je in de buurt?
            </p>
            <div class="relative">
              <textarea
                id="facilities"
                name="facilities"
                rows="4"
                placeholder="Bijv. sportschool met squat rack en dumbbells (2–30 kg), roeier, loopband; thuis kettlebell 16/24 kg; atletiekbaan 1 km verder; park met trappen."
                x-model.trim="form.facilities"
                class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
                :class="errors.facilities ? 'border-red-500 focus:border-red-500' : ''"
                maxlength="500"
              ></textarea>
              <span class="absolute right-3 bottom-2 text-xs text-black/40"
                    x-text="(form.facilities || '').length + ' / 500'"></span>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between gap-2">
          <button type="button" class="{{ $btnGhost }}" @click="prev()">Vorige</button>
          <button type="button" class="{{ $btnPrimary }}" @click="next()">Volgende stap</button>
        </div>
      </div>
    </template>

    <!-- STEP 9: Materialen -->
    <template x-if="step === 9">
      <div>
        <h3 class="text-md font-semibold mb-4">
          Materialen
          <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-500 ml-2">Optioneel</span>
        </h3>

        <div class="flex flex-col gap-4 mb-6">
          <div>
            <p class="text-sm font-medium text-black mb-1">
              Welke materialen heb je tot je beschikking?
            </p>
            <div class="relative">
              <textarea
                id="materials"
                name="materials"
                rows="4"
                placeholder="Bijv. barbell + schijven, dumbbells (2–30 kg), kettlebells 12/16/24 kg, weerstandsbanden, indoor bike/roeier, pull-up bar."
                x-model.trim="form.materials"
                class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
                :class="errors.materials ? 'border-red-500 focus:border-red-500' : ''"
                maxlength="500"
              ></textarea>
              <span class="absolute right-3 bottom-2 text-xs text-black/40"
                    x-text="(form.materials || '').length + ' / 500'"></span>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between gap-2">
          <button type="button" class="{{ $btnGhost }}" @click="prev()">Vorige</button>
          <button type="button" class="{{ $btnPrimary }}" @click="next()">Volgende stap</button>
        </div>
      </div>
    </template>

    <!-- STEP 10: Werktijden -->
    <template x-if="step === 10">
      <div>
        <h3 class="text-md font-semibold mb-4">
          Werktijden
          <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-500 ml-2">Optioneel</span>
        </h3>

        <div class="flex flex-col gap-4 mb-6">
          <div>
            <p class="text-sm font-medium text-black mb-1">
              Wat zijn je werktijden?
            </p>
            <div class="relative">
              <textarea
                id="working_hours"
                name="working_hours"
                rows="4"
                placeholder="Bijv. ma–vr 08:30–17:00; ploegendienst om de week (ochtend/avond/nacht); 2x per maand weekenddienst."
                x-model.trim="form.working_hours"
                class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
                :class="errors.working_hours ? 'border-red-500 focus:border-red-500' : ''"
                maxlength="500"
              ></textarea>
              <span class="absolute right-3 bottom-2 text-xs text-black/40"
                    x-text="(form.working_hours || '').length + ' / 500'"></span>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between gap-2">
          <button type="button" class="{{ $btnGhost }}" @click="prev()">Vorige</button>
          <button type="button" class="{{ $btnPrimary }}" @click="next()">Volgende stap</button>
        </div>
      </div>
    </template>

    <!-- STEP 11: Doelwedstrijd -->
    <template x-if="step === 11">
      <div>
        <h3 class="text-md font-semibold mb-4">Doelwedstrijd</h3>

        <div class="flex flex-col gap-4 mb-8">
          <div>
            <p class="text-sm font-medium text-black mb-1">Welke afstand ga je doen?</p>
            <select
              id="goal_distance"
              name="goal_distance"
              x-model="form.goal_distance"
              class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
              :class="errors.goal_distance ? 'border-red-500 focus:border-red-500' : ''"
            >
              <option value="">Kies een afstand</option>
              <option value="HYROX_OPEN">HYROX Open</option>
              <option value="HYROX_PRO">HYROX Pro</option>
              <option value="HYROX_DOUBLES">HYROX Doubles</option>
              <option value="HYROX_DOUBLES_PRO">HYROX Doubles Pro</option>
              <option value="HYROX_MIXED_DOUBLES">HYROX Mixed Doubles</option>
              <option value="5K">5 km</option>
              <option value="10K">10 km</option>
              <option value="21K">Halve marathon (21,1 km)</option>
              <option value="42K">Marathon (42,2 km)</option>
            </select>
          </div>

          <div>
            <p class="text-sm font-medium text-black mb-1">Wat is je doeltijd?</p>
            <input
              id="goal_time_hms"
              name="goal_time_hms"
              type="text"
              placeholder="HH:MM:SS (bijv. 01:15:00)"
              x-model.trim="form.goal_time_hms"
              class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
              :class="errors.goal_time_hms ? 'border-red-500 focus:border-red-500' : ''"
            >
          </div>

          <div>
            <p class="text-sm font-medium text-black mb-1">Wanneer is je doelwedstrijd?</p>
            <input
              id="goal_ref_date"
              name="goal_ref_date"
              type="date"
              x-model="form.goal_ref_date"
              class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
              :class="errors.goal_ref_date ? 'border-red-500 focus:border-red-500' : ''"
            >
          </div>
        </div>

        <div class="flex items-center justify-between gap-2">
          <button type="button" class="{{ $btnGhost }}" @click="prev()">Vorige</button>
          <button type="button" class="{{ $btnPrimary }}" @click="next()">Volgende stap</button>
        </div>
      </div>
    </template>

    <!-- STEP 12: Testresultaten (lopen) -->
    <template x-if="step === 12">
      <div>
        <h3 class="text-md font-semibold mb-4">Testresultaten (lopen)</h3>

        <div class="flex flex-col gap-4 mb-8">
          <div>
            <p class="text-sm font-medium text-black mb-1">Cooper 12-min test</p>
            <div class="relative">
              <input
                id="cooper_meters"
                name="cooper_meters"
                type="number" min="800" max="5000" step="10"
                placeholder="bijv. 2800 (verplicht)"
                x-model.number="form.cooper_meters"
                class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
                :class="errors.cooper_meters ? 'border-red-500 focus:border-red-500' : ''"
              >
              <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-black/50">meter</span>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <p class="text-sm font-medium text-black mb-1">Totale tijd op 5 km (MM:SS)</p>
              <input
                id="test_5k_pace"
                name="test_5k_pace"
                type="text"
                placeholder="bijv. 24:30 "
                x-model.trim="form.test_5k_pace"
                class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
                :class="errors.test_5k_pace ? 'border-red-500 focus:border-red-500' : ''"
              >
            </div>
            <div>
              <p class="text-sm font-medium text-black mb-1">Totale tijd op 10 km (MM:SS)</p>
              <input
                id="test_10k_pace"
                name="test_10k_pace"
                type="text"
                placeholder="bijv. 51:40"
                x-model.trim="form.test_10k_pace"
                class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
                :class="errors.test_10k_pace ? 'border-red-500 focus:border-red-500' : ''"
              >
            </div>
            <div>
              <p class="text-sm font-medium text-black mb-1">Totale tijd marathon (HH:MM:SS)</p>
              <input
                id="marathon_pace"
                name="marathon_pace"
                type="text"
                placeholder="bijv. 03:59:08"
                x-model.trim="form.marathon_pace"
                class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
                :class="errors.marathon_pace ? 'border-red-500 focus:border-red-500' : ''"
              >
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between gap-2">
          <button type="button" class="{{ $btnGhost }}" @click="prev()">Vorige</button>
          <button type="button" class="{{ $btnPrimary }}" @click="next()">Volgende stap</button>
        </div>
      </div>
    </template>

    <!-- STEP 13: Hartslag -->
    <template x-if="step === 13">
      <div>
        <h3 class="text-md font-semibold mb-4">Hartslag</h3>

        <div class="flex flex-col gap-4 mb-8">
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <p class="text-sm font-medium text-black mb-1">HF-max</p>
              <div class="relative">
                <input
                id="hr_max_bpm"
                name="hr_max_bpm"
                type="number" min="120" max="220" step="1"
                :placeholder="form.hr_estimate_from_age ? ('Schatting: ' + estimatedHRMax) : 'bijv. 190'"
                :disabled="form.hr_estimate_from_age"
                x-model.number="form.hr_max_bpm"
                class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7] disabled:bg-gray-50"
                :class="errors.hr_max_bpm ? 'border-red-500 focus:border-red-500' : ''"
                >
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-black/50">BPM</span>
              </div>
            </div>
            <div>
              <p class="text-sm font-medium text-black mb-1">Rusthartslag</p>
              <div class="relative">
                <input
                id="rest_hr_bpm"
                name="rest_hr_bpm"
                type="number" min="30" max="100" step="1"
                placeholder="bijv. 55"
                x-model.number="form.rest_hr_bpm"
                class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
                :class="errors.rest_hr_bpm ? 'border-red-500 focus:border-red-500' : ''"
                >
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-black/50">BPM</span>
              </div>
            </div>
          </div>

          <label class="inline-flex items-center gap-2 select-none">
            <input type="checkbox" x-model="form.hr_estimate_from_age" class="rounded">
            <span class="text-sm">Ik weet mijn HF-max niet.</span>
          </label>
        </div>

        <div class="flex items-center justify-between gap-2">
          <button type="button" class="{{ $btnGhost }}" @click="prev()">Vorige</button>
          <button type="button" class="{{ $btnPrimary }}" @click="next()">Volgende stap</button>
        </div>
      </div>
    </template>

    <!-- STEP 14: Fiets / Vermogen (FTP) -->
    <template x-if="step === 14">
      <div>
        <h3 class="text-md font-semibold mb-4">Fiets / Vermogen (FTP) <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-500 ml-2">Optioneel</span></h3>

        <div class="flex flex-col gap-4 mb-8">
          <div class="flex items-center gap-6">
            <label class="inline-flex items-center gap-2 select-none">
              <input type="radio" name="ftp_mode" value="w"  x-model="form.ftp_mode"> <span class="text-sm">Ik vul FTP in Watt</span>
            </label>
            <label class="inline-flex items-center gap-2 select-none">
              <input type="radio" name="ftp_mode" value="wkg" x-model="form.ftp_mode"> <span class="text-sm">Ik vul FTP in W/kg</span>
            </label>
          </div>

          <div class="grid grid-cols-1 gap-4">
            <div x-show="form.ftp_mode === 'w'">
              <p class="text-sm font-medium text-black mb-1">FTP (W)</p>
              <input
                id="ftp_watt"
                name="ftp_watt"
                type="number" min="80" max="500" step="1"
                placeholder="bijv. 250"
                x-model.number="form.ftp_watt"
                class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
                :class="errors.ftp_watt ? 'border-red-500 focus:border-red-500' : ''"
              >
            </div>

            <div x-show="form.ftp_mode === 'wkg'">
              <p class="text-sm font-medium text-black mb-1">FTP (W/kg)</p>
              <input
                id="ftp_wkg"
                name="ftp_wkg"
                type="number" min="1.0" max="7.5" step="0.05"
                placeholder="bijv. 3.2"
                x-model.number="form.ftp_wkg"
                class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
                :class="errors.ftp_wkg ? 'border-red-500 focus:border-red-500' : ''"
              >
            </div>

            <div class="md:col-span-1 flex items-end">
              <div class="w-full p-3 rounded-xl bg-gray-50 border border-gray-200 text-sm">
                <div class="flex items-center justify-between">
                  <span class="text-black/60">Bereken W/kg</span>
                  <span class="font-semibold" x-text="computedFtpWkgDisplay"></span>
                </div>
                <p class="text-[11px] text-black/50 mt-1">Bij invoer in Watt (en ingevuld gewicht) berekenen we W/kg automatisch.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between gap-2">
          <button type="button" class="{{ $btnGhost }}" @click="prev()">Vorige</button>
          <button type="button" class="{{ $btnPrimary }}" @click="next()">Intake afronden</button>
        </div>
      </div>
    </template>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
  // Intake wizard – versie met key-ondersteuning (ak) om stap 2 te skippen/forceren
  // Gebruik in Blade: x-data="intakeWizard({{ json_encode($ak ?? []) }}, {{ json_encode($renewData ?? null) }}, {{ json_encode($isRenew ?? false) }})"
  function intakeWizard(ak = null, renewData = null, isRenew = false) {
    const STORAGE_KEY = 'intakeWizard_v1';

    return {
      // --- flags vanuit server ---
      hasKey: !!(ak && ak.package && ak.duration),
      isRenew: !!isRenew,

      // --- state ---
      isPaying: false,
      steps: [], // wordt in init() gezet op basis van hasKey
      step: 0,
      get totalSteps(){ return this.steps.length },

      form: {
        // stap 0
        name:'', email:'', phone:'', dob:'', start_date:'', gender:'',
        street:'', house_number:'', postcode:'',
        // stap 1/2
        preferred_coach:'',
        package:'', duration:null, // bij key vullen we deze vooraf
        // overige
        height_cm:null, weight_kg:null, injuries:'', goals:'',
        max_days_per_week:null, session_minutes:null,
        sport_background:'', facilities:'', materials:'', working_hours:'',
        goal_distance:'', goal_time_hms:'', goal_ref_date:'',
        cooper_meters:null, test_5k_pace:'', test_10k_pace:'', marathon_pace:'',
        hr_max_bpm:null, rest_hr_bpm:null, hr_estimate_from_age:false,
        ftp_mode:'w', ftp_watt:null, ftp_wkg:null,
      },

      // --- derived ---
      get estimatedHRMax() {
        if (!this.form.dob) return '';
        const d=new Date(this.form.dob), t=new Date();
        let age=t.getFullYear()-d.getFullYear();
        const m=t.getMonth()-d.getMonth();
        if (m<0 || (m===0 && t.getDate()<d.getDate())) age--;
        const est=220-age; return isFinite(est)?est:'';
      },
      get computedFtpWkg(){
        if (this.form.ftp_mode==='w' && this.form.ftp_watt && this.form.weight_kg) {
          const v=this.form.ftp_watt/this.form.weight_kg; return Math.round(v*100)/100;
        }
        if (this.form.ftp_mode==='wkg' && this.form.ftp_wkg) return Math.round(this.form.ftp_wkg*100)/100;
        return null;
      },
      get computedFtpWkgDisplay(){ const v=this.computedFtpWkg; return v?`${v.toFixed(2)} W/kg`:'—'; },

      // --- ui/errors/helpers ---
      errors:{},
      _iti:null, _saveDebounced:null,
      _swiper:null, swiperReady:false, swiperAtStart:true, swiperAtEnd:false,

      debounce(fn, delay=250){ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn.apply(this,a),delay); }; },
      saveState(){ try{ localStorage.setItem(STORAGE_KEY, JSON.stringify({step:this.step, form:this.form})); }catch(e){} },
      loadState(){
        try{
          const raw=localStorage.getItem(STORAGE_KEY); if(!raw) return;
          const p=JSON.parse(raw); if(p?.form) this.form={...this.form, ...p.form};
          if(Number.isInteger(p?.step) && p.step>=0 && p.step<this.totalSteps) this.step=p.step;
        }catch(e){}
      },
      clearState(){ try{ localStorage.removeItem(STORAGE_KEY);}catch(e){} },

      init(){
        // Stel stappen samen: met key halen we 2 (pakket) eruit
        this.steps = this.hasKey
          ? [0,1,3,4,5,6,7,8,9,10,11,12,13,14]
          : [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14];

        // Prefill package/duration als er een key is
        if (this.hasKey) {
          this.form.package  = ak.package;
          this.form.duration = ak.duration;
        }

        // Bij renew: vul persoonlijke gegevens en coach voorkeur in vanuit server data
        // en start bij stap 2 (pakket keuze)
        if (this.isRenew && renewData) {
          // Vul persoonlijke gegevens in (stap 0)
          this.form.name = renewData.name || '';
          this.form.email = renewData.email || '';
          this.form.phone = renewData.phone || '';
          this.form.dob = renewData.dob || '';
          this.form.gender = renewData.gender || '';
          this.form.street = renewData.street || '';
          this.form.house_number = renewData.house_number || '';
          this.form.postcode = renewData.postcode || '';
          // Coach voorkeur (stap 1)
          this.form.preferred_coach = renewData.preferred_coach || '';
          // Start bij pakket keuze (stap 2)
          this.step = 2;
          this.saveState();
        } else {
          this.loadState();
        }

        const params=new URLSearchParams(window.location.search);
        const urlStep=parseInt(params.get('step')||'',10);
        const advance=params.get('advance')==='1';
        const canceled=params.get('canceled')==='1';
        const sessionId=params.get('session_id');

        // was user onderweg naar checkout?
        let cameFromCheckout=false;
        try{ cameFromCheckout = sessionStorage.getItem('intakePending')==='1'; }catch(e){}
        if (!cameFromCheckout) {
          // fallback: als we advance=1 of session_id in URL hebben, behandelen als vanuit checkout
          cameFromCheckout = advance || !!sessionId;
        }

        // URL-step heeft voorrang; corrigeer eventuele 2 → 3 als key actief is
        if(!Number.isNaN(urlStep)) {
          this.step = Math.min(Math.max(urlStep,0), this.totalSteps-1);
          if (this.hasKey && this.step === 2) this.step = 3;
          this.saveState();
        } else {
          // Als localStorage nog 2 bevat maar we key hebben, corrigeer
          if (this.hasKey && this.step === 2) this.step = 3;
        }

        // Als we terug zijn van Stripe met session_id -> bevestigen
        if (sessionId && !sessionStorage.getItem('intakeConfirmed')) {
          this.doConfirm(sessionId).finally(()=>{
            if (advance) {
              this.step = Math.max(this.step, 3);
            }
            this.stripUrlParams();
            this.saveState();
          });
        } else {
          // Geen session_id: FAKE of direct terug
          if (cameFromCheckout && advance) {
            this.step = Math.max(this.step, 3);
          }
          if (cameFromCheckout && canceled) {
            // als key actief is bestaat stap 2 niet meer; ga terug naar coach (1)
            this.step = this.hasKey ? 1 : 2;
          }
          this.stripUrlParams();
          this.saveState();
        }

        // Ruim checkout-flags op
        try{
          sessionStorage.removeItem('intakePending');
          if (sessionId) sessionStorage.setItem('intakeConfirmed','1');
        }catch(e){}

        // Guards
        if (!this.steps.includes(this.step)) this.step=this.steps[0];

        // Debounced autosave
        this._saveDebounced=this.debounce(()=>this.saveState(), 200);
        this.$watch('form', ()=>this._saveDebounced(), { deep:true });
        ['name','email','phone','dob','start_date','gender','street','house_number','postcode','package','duration','height_cm','weight_kg']
          .forEach(k=>this.$watch(`form.${k}`, ()=>this._saveDebounced()));
        this.$watch('step', (val, oldVal)=>{
          this._saveDebounced();
          if (oldVal===0) this.destroyTelInput();
          if (val===0) this.$nextTick(()=>this.initTelInput());

          // Init/Destroy Swiper alleen als stap 2 bestaat (dus geen key)
          if (!this.hasKey && val===2) {
            this.$nextTick(()=>{ initPackagesSwiperAndBind(); });
          } else if (!this.hasKey && oldVal===2) {
            this.destroyPackagesSwiper();
          }
        });

        this.$nextTick(()=>{
          const el=document.getElementById('dob');
          if (el){
            const today=new Date();
            const max=today.toISOString().split('T')[0];
            const minDate=new Date(today.getFullYear()-80, today.getMonth(), today.getDate());
            const min=minDate.toISOString().split('T')[0];
            el.setAttribute('min',min); el.setAttribute('max',max);
          }
          const startEl = document.getElementById('start_date');
          if (startEl) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            // YYYY-MM-DD formatter zonder timezone gedoe
            const toLocalDateInputValue = (d) => {
              const y  = d.getFullYear();
              const m  = String(d.getMonth() + 1).padStart(2, '0');
              const dd = String(d.getDate()).padStart(2, '0');
              return `${y}-${m}-${dd}`;
            };

            // 1) alles vóór vandaag blokkeren
            const minStr = toLocalDateInputValue(today);
            startEl.setAttribute('min', minStr);

            // 2) eerste maandag vanaf vandaag
            const day = today.getDay(); // 0=zo, 1=ma, ...
            const offset = (1 - day + 7) % 7;
            const firstMonday = new Date(today);
            firstMonday.setDate(today.getDate() + offset);
            const firstMondayStr = toLocalDateInputValue(firstMonday);

            // ⬇️ BELANGRIJK: model en input gelijk zetten
            const initial = this.form.start_date || firstMondayStr;
            this.form.start_date = initial;
            startEl.value = initial;

            // 3) alleen nog stappen van 7 dagen (alleen maandagen)
            startEl.setAttribute('step', '7');
          }
          if (this.step===0) this.initTelInput();
          if (!this.hasKey && this.step===2) initPackagesSwiperAndBind();

          window.addEventListener('packages-swiper-ready',(e)=>{
            const s=e?.detail?.swiper; if(!s) return;
            this.bindSwiper(s); this.updateSwiperEdges(); requestAnimationFrame(()=>this.updateSwiperEdges());
          });
        });
      },

      stripUrlParams(){
        const clean=window.location.pathname;
        history.replaceState(null,'', clean);
      },

      finalizeIntake(){
        try{
          localStorage.removeItem('intakeWizard_v1');
          sessionStorage.removeItem('intakePending');
          sessionStorage.removeItem('intakeConfirmed');
        }catch(e){}

        const url = new URL('{{ url('/login') }}', window.location.origin);
        url.searchParams.set('intake', 'ok'); // 👈 succesvlag meegeven
        window.location.assign(url.toString());
      },

      packageLabel(k){
        return k==='pakket_a'?'Basis Pakket':k==='pakket_b'?'Chasing Goals Pakket':k==='pakket_c'?'Elite Hyrox Pakket':'';
      },

      // --- STRIPE: pakket kiezen -> checkout ---
      choosePackage(pkg, weeks){
        // Bij key overrulen we geen UI-keuze; maar deze functie kan nog vanuit UI van andere flows aangeroepen worden.
        if (this.hasKey) {
          // forceer key-keuze en ga direct submitten
          this.form.package = ak.package;
          this.form.duration = ak.duration;
        } else {
          this.form.package=pkg; this.form.duration=weeks;
        }
        delete this.errors.package; delete this.errors.duration;
        this.submit();
      },

      // --- VALIDATIE (per stap) ---
      validateStep(stepIndex){
        this.errors={}; let firstInvalidEl=null;

        if (stepIndex===0){
          if (!this.form.name?.trim()){
            this.errors.name='Vul je volledige naam in.';
            firstInvalidEl=firstInvalidEl||document.getElementById('name');
          }

          const email=this.form.email?.trim()||'';
          if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
            this.errors.email='Vul een geldig e-mailadres in.';
            firstInvalidEl=firstInvalidEl||document.getElementById('email');
          }

          const phoneVal=(this.form.phone||'').trim();
          if (!phoneVal){
            this.errors.phone='Vul je telefoonnummer in.';
            firstInvalidEl=firstInvalidEl||document.getElementById('phone');
          }

          const dob=this.form.dob;
          if (!dob){
            this.errors.dob='Kies je geboortedatum.';
            firstInvalidEl=firstInvalidEl||document.getElementById('dob');
          }

          // ✅ NIEUW: startdatum verplicht, niet in verleden, en op maandag
          const startDateStr = this.form.start_date;
          if (!startDateStr) {
            this.errors.start_date = 'Kies je startdatum.';
            firstInvalidEl = firstInvalidEl || document.getElementById('start_date');
          } else {
            const sd = new Date(startDateStr + 'T00:00:00');
            const today = new Date();
            today.setHours(0,0,0,0);

            if (sd < today) {
              this.errors.start_date = 'Startdatum mag niet in het verleden liggen.';
              firstInvalidEl = firstInvalidEl || document.getElementById('start_date');
            }

            const day = sd.getDay(); // 0=zo, 1=ma, ...
            if (day !== 1) {
              this.errors.start_date = 'Startdatum moet op een maandag vallen.';
              firstInvalidEl = firstInvalidEl || document.getElementById('start_date');
            }
          }

          if (!this.form.gender){
            this.errors.gender='Kies je geslacht.';
          }

          if (!this.form.street?.trim()){
            this.errors.street='Vul je straatnaam in.';
            firstInvalidEl=firstInvalidEl||document.getElementById('street');
          }

          if (!this.form.house_number?.trim()){
            this.errors.house_number='Vul je huisnummer in.';
            firstInvalidEl=firstInvalidEl||document.getElementById('house_number');
          }

          const pcRaw=(this.form.postcode||'').trim().toUpperCase();
          const pc=pcRaw.replace(/\s+/g,' ');
          const nlRe=/^\d{4}\s?[A-Z]{2}$/, beRe=/^\d{4}$/;
          let okPostcode=nlRe.test(pc);
          if(!okPostcode && beRe.test(pc)){
            const num=parseInt(pc,10);
            okPostcode = num>=1000 && num<=9999;
          }
          if (!okPostcode){
            this.errors.postcode='Vul een geldige postcode in (NL: 1234 AB, BE: 1000–9999).';
            firstInvalidEl=firstInvalidEl||document.getElementById('postcode');
          } else {
            this.form.postcode=pc;
          }
        }
        if (stepIndex===1){
          if (!['roy','eline','nicky','none'].includes(this.form.preferred_coach)) this.errors.preferred_coach='Kies je coachvoorkeur (of selecteer “Geen voorkeur”).';
        }
        if (stepIndex===2){
          if (this.hasKey) {
            // key forceert; geen validatie nodig
          } else {
            if (!this.form.package) this.errors.package='Kies een pakket via de knoppen.';
            if (!this.form.duration) this.errors.duration='Kies ook de duur (12 of 24 weken).';
          }
        }
        if (stepIndex===3){
          if (this.form.height_cm==null || isNaN(this.form.height_cm) || this.form.height_cm<120 || this.form.height_cm>250) this.errors.height_cm='Lengte 120–250 cm.';
          if (this.form.weight_kg==null || isNaN(this.form.weight_kg) || this.form.weight_kg<35 || this.form.weight_kg>250) this.errors.weight_kg='Gewicht 35–250 kg.';
        }
        if (stepIndex===4){ if ((this.form.injuries||'').length>500) this.errors.injuries='Maximaal 500 tekens.'; }
        if (stepIndex===5){
          if (!(this.form.goals||'').trim()) this.errors.goals='Beschrijf kort je doelen.';
          else if ((this.form.goals||'').length>500) this.errors.goals='Maximaal 500 tekens.';
        }
        if (stepIndex===6){
          if (this.form.max_days_per_week==null || isNaN(this.form.max_days_per_week) || this.form.max_days_per_week<1 || this.form.max_days_per_week>7) this.errors.max_days_per_week='Kies 1–7.';
          if (this.form.session_minutes==null || isNaN(this.form.session_minutes) || this.form.session_minutes<20 || this.form.session_minutes>180) this.errors.session_minutes='Kies 20–180 min.';
        }
        if (stepIndex===7){ if ((this.form.sport_background||'').length>500) this.errors.sport_background='Maximaal 500 tekens.'; }
        if (stepIndex===8){ if ((this.form.facilities||'').length>500) this.errors.facilities='Maximaal 500 tekens.'; }
        if (stepIndex===9){ if ((this.form.materials||'').length>500) this.errors.materials='Maximaal 500 tekens.'; }
        if (stepIndex===10){ if ((this.form.working_hours||'').length>500) this.errors.working_hours='Maximaal 500 tekens.'; }
        if (stepIndex===11){
          if (!this.form.goal_distance) this.errors.goal_distance='Kies een afstand.';
          const t=(this.form.goal_time_hms||'').trim();
          if (!/^\d{1,2}:\d{2}:\d{2}$/.test(t)) this.errors.goal_time_hms='Voer doeltijd in als HH:MM:SS.';
          if (!this.form.goal_ref_date) this.errors.goal_ref_date='Kies de datum.';
        }
        if (stepIndex===12){
          const mmssRe = /^\d{1,2}:\d{2}$/;
          const hmsRe  = /^\d{1,2}:\d{2}:\d{2}$/;
          if (this.form.cooper_meters==null || isNaN(this.form.cooper_meters) || this.form.cooper_meters<800 || this.form.cooper_meters>5000) {
            this.errors.cooper_meters = 'Cooper 800–5000 m.';
          }
          if (!(this.form.test_5k_pace||'').trim() || !mmssRe.test(this.form.test_5k_pace)) {
            this.errors.test_5k_pace = '5 km tijd MM:SS.';
          }
          if ((this.form.test_10k_pace||'').trim() && !mmssRe.test(this.form.test_10k_pace)) {
            this.errors.test_10k_pace = '10 km tijd MM:SS.';
          }
          if ((this.form.marathon_pace||'').trim() && !hmsRe.test(this.form.marathon_pace)) {
            this.errors.marathon_pace = 'Marathon tijd HH:MM:SS.';
          }
        }
        if (stepIndex===13){
          if (!this.form.hr_estimate_from_age){
            if (this.form.hr_max_bpm==null || isNaN(this.form.hr_max_bpm) || this.form.hr_max_bpm<120 || this.form.hr_max_bpm>220) this.errors.hr_max_bpm='HF-max 120–220 of vink schatting aan.';
          }
          if (this.form.rest_hr_bpm!=null && (isNaN(this.form.rest_hr_bpm) || this.form.rest_hr_bpm<30 || this.form.rest_hr_bpm>100)) this.errors.rest_hr_bpm='Rust 30–100 bpm.';
        }
        if (stepIndex===14){
          if (this.form.ftp_mode==='w'){
            if (this.form.ftp_watt!=null && (isNaN(this.form.ftp_watt)||this.form.ftp_watt<80||this.form.ftp_watt>500)) this.errors.ftp_watt='FTP 80–500 W.';
          } else {
            if (this.form.ftp_wkg!=null && (isNaN(this.form.ftp_wkg)||this.form.ftp_wkg<1||this.form.ftp_wkg>7.5)) this.errors.ftp_wkg='FTP 1.0–7.5 W/kg.';
          }
        }

        this.$nextTick(()=>{ const first=document.querySelector('.border-red-500'); first?.focus?.(); });
        return Object.keys(this.errors).length===0;
      },

      // --- NAV ---
      next(){
        if (!this.validateStep(this.step)) return;
        this.errors={};

        const isLast = this.steps.indexOf(this.step) === this.steps.length - 1;
        if (isLast){
          this.saveProgress(this.step).finally(()=> this.finalizeIntake());
          return;
        }

        if (this.hasKey && this.step === 1) {
          this.submit(); // valideert stap 0+1 en triggert de FAKE branch in CheckoutController@create
          return;        // backend redirect regelt doorgaan naar stap ≥ 3
        }

        const currentIndex = this.steps.indexOf(this.step);
        const nextStep = this.steps[currentIndex + 1];

        this.saveProgress(this.step).finally(()=>{
          if (typeof nextStep !== 'undefined') {
            // als nextStep 2 is en we hebben een key, sla direct door naar stap na 2
            const target = (this.hasKey && nextStep === 2)
              ? this.steps[currentIndex + 2]
              : nextStep;
            if (typeof target !== 'undefined') this.step = target;
          }
        });
      },
      prev(){ 
        const idx = this.steps.indexOf(this.step);
        if (idx > 0){
          this.errors={};
          this.step = this.steps[idx - 1];
        }
      },

      // --- START CHECKOUT ---
      submit(){
        if (this.isPaying) return;

        const ok0=this.validateStep(0); if(!ok0){ this.step=0; return; }
        const ok1=this.validateStep(1); if(!ok1){ this.step=1; return; }
        if (!this.hasKey) {
          const ok2=this.validateStep(2); if(!ok2){ this.step=2; return; }
        }

        this.isPaying=true;

        const payload={
          name:this.form.name, email:this.form.email, phone:this.form.phone,
          dob:this.form.dob, start_date:this.form.start_date, gender:this.form.gender,
          street:this.form.street, house_number:this.form.house_number, postcode:this.form.postcode,
          preferred_coach:this.form.preferred_coach,
          package: this.hasKey ? ak.package : this.form.package,
          duration: this.hasKey ? ak.duration : this.form.duration,
          height_cm:this.form.height_cm, weight_kg:this.form.weight_kg,
          injuries:this.form.injuries, goals:this.form.goals,
          max_days_per_week:this.form.max_days_per_week, session_minutes:this.form.session_minutes,
          sport_background:this.form.sport_background, facilities:this.form.facilities,
          materials:this.form.materials, working_hours:this.form.working_hours,
          goal_distance:this.form.goal_distance, goal_time_hms:this.form.goal_time_hms, goal_ref_date:this.form.goal_ref_date,
          cooper_meters:this.form.cooper_meters, test_5k_pace:this.form.test_5k_pace,
          test_10k_pace:this.form.test_10k_pace, marathon_pace:this.form.marathon_pace,
          hr_max_bpm:this.form.hr_estimate_from_age?this.estimatedHRMax:this.form.hr_max_bpm,
          rest_hr_bpm:this.form.rest_hr_bpm, hr_estimate_from_age:this.form.hr_estimate_from_age,
          ftp_mode:this.form.ftp_mode, ftp_watt:this.form.ftp_watt,
          ftp_wkg:(this.form.ftp_mode==='w' ? this.computedFtpWkg : this.form.ftp_wkg),
        };

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        fetch('{{ route('intake.checkout') }}', {
          method:'POST',
          headers:{ 'Content-Type':'application/json', 'Accept':'application/json', ...(csrf?{'X-CSRF-TOKEN':csrf}:{}) },
          credentials:'same-origin',
          body:JSON.stringify(payload)
        })
        .then(async res=>{
          if(!res.ok){
            const t = await res.text().catch(()=>''), msg = (JSON.parse(t||'{}').message) || 'Kon betaalpagina niet starten.';
            throw new Error(msg);
          }
          return res.json();
        })
        .then((payload)=>{
          const redirect_url = payload?.redirect_url;
          const redirect_to_login = payload?.redirect_to_login; // optioneel, mocht je dat in je controller meegeven

          if (redirect_url) {
            // Stripe-checkout pad
            try{ sessionStorage.setItem('intakePending','1'); }catch(e){}
            window.location.href = redirect_url;
            return;
          }

          // Geen redirect_url → intake is klaar (bijv. key/ak-flow zonder betaling)
          if (redirect_to_login || this.hasKey || payload?.ok === true) {
            this.finalizeIntake();
            return;
          }

          throw new Error('Onverwachte serverrespons bij intake.');
        })
        .catch(e=>{
          console.error('Checkout error:', e);
          this.errors.general = e.message || 'Er ging iets mis bij het starten van de betaling.';
        })
        .finally(()=>{ this.isPaying=false; });
      },

      // --- NA BETALING: session_id bevestigen ---
      async doConfirm(sessionId){
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        try{
          const res = await fetch('{{ route('intake.checkout.confirm') }}', {
            method:'POST',
            headers:{ 'Content-Type':'application/json', 'Accept':'application/json', ...(csrf?{'X-CSRF-TOKEN':csrf}:{}) },
            credentials:'same-origin',
            body: JSON.stringify({ session_id: sessionId })
          });
          if (!res.ok){
            const t = await res.text().catch(()=>''), msg = (JSON.parse(t||'{}').message) || 'Betaling kon niet bevestigd worden.';
            throw new Error(msg);
          }
          sessionStorage.setItem('intakeConfirmed','1');

          // ✅ Intake is succesvol afgerond → opruimen + naar /login
          this.finalizeIntake();
        } catch(err){
          console.error('Confirm error', err);
          this.errors.general = err.message || 'Betaling bevestigen is mislukt.';
        }
      },

      // --- PROGRESS SAVE (per stap) ---
      buildProgressPayload(stepIndex){
        const p={};
        if (stepIndex===3){ p.height_cm=this.form.height_cm; p.weight_kg=this.form.weight_kg; }
        if (stepIndex===4){ p.injuries=this.form.injuries; }
        if (stepIndex===5){ p.goals=this.form.goals; }
        if (stepIndex===6){ p.max_days_per_week=this.form.max_days_per_week; p.session_minutes=this.form.session_minutes; }
        if (stepIndex===7){ p.sport_background=this.form.sport_background; }
        if (stepIndex===8){ p.facilities=this.form.facilities; }
        if (stepIndex===9){ p.materials=this.form.materials; }
        if (stepIndex===10){ p.working_hours=this.form.working_hours; }
        if (stepIndex===11){ p.goal_distance=this.form.goal_distance; p.goal_time_hms=this.form.goal_time_hms; p.goal_ref_date=this.form.goal_ref_date; }
        if (stepIndex===12){ p.cooper_meters=this.form.cooper_meters; p.test_5k_pace=this.form.test_5k_pace; p.test_10k_pace=this.form.test_10k_pace; p.marathon_pace=this.form.marathon_pace; }
        if (stepIndex===13){
          p.hr_max_bpm = this.form.hr_estimate_from_age ? this.estimatedHRMax : this.form.hr_max_bpm;
          p.rest_hr_bpm=this.form.rest_hr_bpm;
        }
        if (stepIndex===14){
          p.ftp_mode=this.form.ftp_mode; p.ftp_watt=this.form.ftp_watt;
          p.ftp_wkg=(this.form.ftp_mode==='w' ? this.computedFtpWkg : this.form.ftp_wkg);
        }
        return p;
      },

      async saveProgress(stepIndex){
        const payload=this.buildProgressPayload(stepIndex);
        if (Object.values(payload).every(v => v===null || v===undefined || v==='')) return Promise.resolve();

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        try{
          const res = await fetch('{{ route('intake.progress') }}', {
            method:'POST',
            headers:{ 'Content-Type':'application/json', 'Accept':'application/json', ...(csrf?{'X-CSRF-TOKEN':csrf}:{}) },
            credentials:'same-origin',
            body: JSON.stringify(payload)
          });
          if (!res.ok){
            const t=await res.text().catch(()=>''), msg=(JSON.parse(t||'{}').message)||'Kon voortgang niet opslaan.';
            throw new Error(msg);
          }
        } catch(err){
          console.error('Progress save error', err);
          this.errors.general = err.message || 'Opslaan van voortgang is mislukt.';
        }
      },

      // --- intl-tel-input ---
      initTelInput(){
        const el=document.getElementById('phone'); if(!el) return;
        if (el.parentElement?.classList.contains('iti') && this._iti) return;

        this._iti=window.intlTelInput(el,{
          initialCountry:'nl', onlyCountries:['nl','be'], separateDialCode:true,
          utilsScript:'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js'
        });

        // Zet beginwaarde uit model (als aanwezig)
        if (this.form.phone){ try{ this._iti.setNumber(this.form.phone); }catch(e){} }

        const syncFromPlugin = () => {
          if (this._iti && this._iti.isValidNumber()){
            this.form.phone = this._iti.getNumber(); // E.164
            this.errors.phone = '';
          } else {
            this.form.phone = el.value; // fallback
          }
        };

        el.addEventListener('blur', syncFromPlugin);
        el.addEventListener('input', () => { this.form.phone = el.value; });
        el.addEventListener('countrychange', syncFromPlugin);
      },
      destroyTelInput(){ if(this._iti){ try{ this._iti.destroy(); }catch(e){} this._iti=null; } },

      // --- Swiper ---
      bindSwiper(s){ this._swiper=s; this.swiperReady=true; this.updateSwiperEdges();
        s.on('slideChange resize transitionEnd reachBeginning reachEnd fromEdge', ()=>this.updateSwiperEdges());
        setTimeout(()=>this.updateSwiperEdges(),0);
      },
      updateSwiperEdges(){ if(!this._swiper) return; this.swiperAtStart=this._swiper.isBeginning; this.swiperAtEnd=this._swiper.isEnd; },
      swiperNext(){ this._swiper?.slideNext(); }, swiperPrev(){ this._swiper?.slidePrev(); },
      destroyPackagesSwiper(){ if(this._swiper){ this._swiper.destroy(true,true); this._swiper=null; } this.swiperReady=false; this.swiperAtStart=true; this.swiperAtEnd=false; },
    }
  }

  // Init Swiper (packages) en koppel aan Alpine via custom event
  function initPackagesSwiperAndBind(){
    const el=document.querySelector('.packages-swiper');
    if (!el || typeof Swiper==='undefined') return null;

    const swiper=new Swiper(el,{
      slidesPerView:1.1, spaceBetween:16, grabCursor:false, watchOverflow:true,
      keyboard:{enabled:true}, a11y:{enabled:true}, observer:true, observeParents:true, observeSlideChildren:true,
      slidesOffsetAfter:8,
      breakpoints:{
        768:{ slidesPerView:1.5, spaceBetween:20, slidesOffsetAfter:10 },
        1024:{ slidesPerView:2.2, spaceBetween:24, slidesOffsetAfter:12 },
        1280:{ slidesPerView:2.2, spaceBetween:24, slidesOffsetAfter:16 },
      },
      on:{ init(s){ window.dispatchEvent(new CustomEvent('packages-swiper-ready',{ detail:{swiper:s} })); requestAnimationFrame(()=>s.update()); } }
    });

    if (!swiper.initialized && typeof swiper.init==='function') swiper.init();
    return swiper;
  }
</script>
@endsection
