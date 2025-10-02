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

    <!-- STEP 1: Coach voorkeur -->
    <template x-if="step === 1">
      <div>
        <h3 class="text-md font-semibold mb-4">Welke coach heeft je voorkeur?</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
          <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-300 cursor-pointer hover:bg-gray-50">
            <input type="radio" name="preferred_coach" value="roy"   x-model="form.preferred_coach" class="sr-only peer">
            <span class="w-4 h-4 rounded-full border border-gray-300 inline-flex items-center justify-center
                        peer-checked:bg-[#c8ab7a] peer-checked:border-[#c8ab7a]">
              <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 0 1 0 1.414l-7.364 7.364a1 1 0 0 1-1.414 0L3.293 9.435a1 1 0 1 1 1.414-1.414l3.05 3.05 6.657-6.657a1 1 0 0 1 1.414 0z" clip-rule="evenodd"/>
              </svg>
            </span>
            <span class="text-sm font-medium">Roy</span>
          </label>

          <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-300 cursor-pointer hover:bg-gray-50">
            <input type="radio" name="preferred_coach" value="eline" x-model="form.preferred_coach" class="sr-only peer">
            <span class="w-4 h-4 rounded-full border border-gray-300 inline-flex items-center justify-center
                        peer-checked:bg-[#c8ab7a] peer-checked:border-[#c8ab7a]">
              <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 0 1 0 1.414l-7.364 7.364a1 1 0 0 1-1.414 0L3.293 9.435a1 1 0 1 1 1.414-1.414l3.05 3.05 6.657-6.657a1 1 0 0 1 1.414 0z" clip-rule="evenodd"/>
              </svg>
            </span>
            <span class="text-sm font-medium">Eline</span>
          </label>

          <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-300 cursor-pointer hover:bg-gray-50">
            <input type="radio" name="preferred_coach" value="nicky" x-model="form.preferred_coach" class="sr-only peer">
            <span class="w-4 h-4 rounded-full border border-gray-300 inline-flex items-center justify-center
                        peer-checked:bg-[#c8ab7a] peer-checked:border-[#c8ab7a]">
              <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 0 1 0 1.414l-7.364 7.364a1 1 0 0 1-1.414 0L3.293 9.435a1 1 0 1 1 1.414-1.414l3.05 3.05 6.657-6.657a1 1 0 0 1 1.414 0z" clip-rule="evenodd"/>
              </svg>
            </span>
            <span class="text-sm font-medium">Nicky</span>
          </label>

          <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-300 cursor-pointer hover:bg-gray-50">
            <input type="radio" name="preferred_coach" value="none"  x-model="form.preferred_coach" class="sr-only peer">
            <span class="w-4 h-4 rounded-full border border-gray-300 inline-flex items-center justify-center
                        peer-checked:bg-[#c8ab7a] peer-checked:border-[#c8ab7a]">
              <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 0 1 0 1.414l-7.364 7.364a1 1 0 0 1-1.414 0L3.293 9.435a1 1 0 1 1 1.414-1.414l3.05 3.05 6.657-6.657a1 1 0 0 1 1.414 0z" clip-rule="evenodd"/>
              </svg>
            </span>
            <span class="text-sm font-medium">Geen voorkeur</span>
          </label>
        </div>

        <div class="flex items-center justify-between gap-2">
          <button type="button" class="{{ $btnGhost }}" @click="prev()">Vorige</button>
          <button type="button" class="{{ $btnPrimary }}" @click="next()">Volgende stap</button>
        </div>
      </div>
    </template>

    <!-- STEP 2: Pakket kiezen -->
    <template x-if="step === 2">
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

    <!-- STEP 3: Lengte & Gewicht -->
    <template x-if="step === 3">
      <div>
        <h3 class="text-md font-semibold mb-4">Lengte & Gewicht</h3>
        <div class="flex flex-col gap-4 mb-8">
          <div class="flex items-center gap-4">
            <div class="w-1/2">
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

            <div class="w-1/2">
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
          <div class="flex items-center gap-4">
            <!-- Dagen per week -->
            <div class="w-1/2">
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
            <div class="w-1/2">
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
              <option value="HYROXPRO">HYROX Pro</option>
              <option value="HYROXDOUBLE">HYROX Double</option>
              <option value="HYROXMIXDOUBLE">HYROX Mix Double</option>
              <option value="HYROXSINGLE">HYROX Single</option>
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
              <p class="text-sm font-medium text-black mb-1">5 km pace</p>
              <input
                id="test_5k_pace"
                name="test_5k_pace"
                type="text"
                placeholder="bijv. 04:55 (verplicht)"
                x-model.trim="form.test_5k_pace"
                class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
                :class="errors.test_5k_pace ? 'border-red-500 focus:border-red-500' : ''"
              >
            </div>
            <div>
              <p class="text-sm font-medium text-black mb-1">10 km pace</p>
              <input
                id="test_10k_pace"
                name="test_10k_pace"
                type="text"
                placeholder="bijv. 05:10"
                x-model.trim="form.test_10k_pace"
                class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]"
                :class="errors.test_10k_pace ? 'border-red-500 focus:border-red-500' : ''"
              >
            </div>
            <div>
              <p class="text-sm font-medium text-black mb-1">Marathon pace</p>
              <input
                id="marathon_pace"
                name="marathon_pace"
                type="text"
                placeholder="bijv. 05:40"
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
  function intakeWizard() {
    const STORAGE_KEY = 'intakeWizard_v1';

    return {
      isPaying: false,

      // stappen
      steps: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14],
      step: 0,
      get totalSteps() { return this.steps.length },

      form: {
        name: '',
        email: '',
        phone: '',
        dob: '',
        gender: '',
        street: '',
        house_number: '',
        postcode: '',
        preferred_coach: '',
        package: '',
        duration: null,
        height_cm: null,
        weight_kg: null,
        injuries: '',
        goals: '',
        max_days_per_week: null,
        session_minutes: null,
        sport_background: '',
        facilities: '',
        materials: '',
        working_hours: '',
        goal_distance: '',
        goal_time_hms: '',
        goal_ref_date: '',
        cooper_meters: null,
        test_5k_pace: '',
        test_10k_pace: '',
        marathon_pace: '',
        hr_max_bpm: null,
        rest_hr_bpm: null,
        hr_estimate_from_age: false,
        ftp_mode: 'w',      // 'w' of 'wkg'
        ftp_watt: null,
        ftp_wkg: null,
      },

      get estimatedHRMax() {
        if (!this.form.dob) return '';
        const dob = new Date(this.form.dob);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
        const est = 220 - age;
        return isFinite(est) ? est : '';
      },
      get computedFtpWkg() {
        if (this.form.ftp_mode === 'w' && this.form.ftp_watt && this.form.weight_kg) {
          const v = this.form.ftp_watt / this.form.weight_kg;
          return Math.round(v * 100) / 100;
        }
        if (this.form.ftp_mode === 'wkg' && this.form.ftp_wkg) {
          return Math.round(this.form.ftp_wkg * 100) / 100;
        }
        return null;
      },
      get computedFtpWkgDisplay() {
        const v = this.computedFtpWkg;
        return v ? `${v.toFixed(2)} W/kg` : '—';
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
        // Geannuleerd → terug naar stap 2 (kies pakket)
        if (cameFromCheckout && canceled) {
          this.step = 2;
        }

        // Flag altijd opruimen zodat refresh niets opnieuw triggert
        try { sessionStorage.removeItem('intakePending'); } catch (e) {}

        // Fallback: als step door wat dan ook buiten bereik is → 0
        if (![0,1,2,3,4,5,6,7,8,9,10,11,12,13,14].includes(this.step)) this.step = 0;

        // autosave + watchers
        this._saveDebounced = this.debounce(() => this.saveState(), 200);
        // sla álle wijzigingen in form (ook nested) op
        this.$watch('form', () => this._saveDebounced(), { deep: true });

        // (optionele fallback als jouw Alpine versie geen { deep: true } ondersteunt)
        ;[
          'name','email','phone','dob','gender','street','house_number','postcode',
          'package','duration','height_cm','weight_kg'
        ].forEach(k => this.$watch(`form.${k}`, () => this._saveDebounced()));
        this.$watch('step', (val, oldVal) => {
          this._saveDebounced();
          if (oldVal === 0) this.destroyTelInput();
          if (val === 0) this.$nextTick(() => this.initTelInput());
          if (val === 2) { this.$nextTick(() => { initPackagesSwiperAndBind(); }); }
          else if (oldVal === 2) { this.destroyPackagesSwiper(); }
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
          if (this.step === 2) initPackagesSwiperAndBind();

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
        if (!this.validateStep(this.step)) return; // toont errors voor huidige stap
        this.errors = {};                          // ↞ clear errors zodra we wél door mogen
        if (this.step < this.totalSteps - 1) this.step++;
      },
      prev() {
        if (this.step > 0) {
          this.errors = {};    // ↞ clear errors bij teruggaan
          this.step--;
        }
      },

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
          if (!['roy','eline','nicky','none'].includes(this.form.preferred_coach)) {
            this.errors.preferred_coach = 'Kies je coachvoorkeur (of selecteer “Geen voorkeur”).';
          }
        }
        if (stepIndex === 2) {
          if (!this.form.package)  this.errors.package  = 'Kies een pakket via de knoppen.';
          if (!this.form.duration) this.errors.duration = 'Kies ook de duur (12 of 24 weken).';
        }
        if (stepIndex === 3) {
          // LENGTE (cm)
          if (this.form.height_cm == null || isNaN(this.form.height_cm)) {
            this.errors.height_cm = 'Vul je lengte in (in cm).';
          } else if (this.form.height_cm < 120 || this.form.height_cm > 250) {
            this.errors.height_cm = 'Lengte moet tussen 120 en 250 cm liggen.';
          }

          // GEWICHT (kg)
          if (this.form.weight_kg == null || isNaN(this.form.weight_kg)) {
            this.errors.weight_kg = 'Vul je gewicht in (in kg).';
          } else if (this.form.weight_kg < 35 || this.form.weight_kg > 250) {
            this.errors.weight_kg = 'Gewicht moet tussen 35 en 250 kg liggen.';
          }
        }
        if (stepIndex === 4) {
          if ((this.form.injuries || '').length > 500) {
            this.errors.injuries = 'Maximaal 500 tekens.';
          }
        }
        if (stepIndex === 5) {
          if (!(this.form.goals || '').trim()) {
            this.errors.goals = 'Beschrijf kort je doelen.';
          } else if ((this.form.goals || '').length > 500) {
            this.errors.goals = 'Maximaal 500 tekens.';
          }
        }
        if (stepIndex === 6) {
          // Dagen per week (1–7)
          if (this.form.max_days_per_week == null || isNaN(this.form.max_days_per_week)) {
            this.errors.max_days_per_week = 'Vul het aantal dagen per week in.';
          } else if (this.form.max_days_per_week < 1 || this.form.max_days_per_week > 7) {
            this.errors.max_days_per_week = 'Kies een waarde tussen 1 en 7.';
          }

          // Duur per sessie (20–180 minuten)
          if (this.form.session_minutes == null || isNaN(this.form.session_minutes)) {
            this.errors.session_minutes = 'Vul de duur per sessie in (in minuten).';
          } else if (this.form.session_minutes < 20 || this.form.session_minutes > 180) {
            this.errors.session_minutes = 'Kies een duur tussen 20 en 180 minuten.';
          }
        }
        if (stepIndex === 7) {
          if ((this.form.sport_background || '').length > 500) {
            this.errors.sport_background = 'Maximaal 500 tekens.';
          }
        }
        if (stepIndex === 8) {
          if ((this.form.facilities || '').length > 500) {
            this.errors.facilities = 'Maximaal 500 tekens.';
          }
        }
        if (stepIndex === 9) {
          if ((this.form.materials || '').length > 500) {
            this.errors.materials = 'Maximaal 500 tekens.';
          }
        }
        if (stepIndex === 10) {
          if ((this.form.working_hours || '').length > 500) {
            this.errors.working_hours = 'Maximaal 500 tekens.';
          }
        }
        if (stepIndex === 11) {
          if (!this.form.goal_distance) {
            this.errors.goal_distance = 'Kies een afstand.';
          }
          const t = (this.form.goal_time_hms || '').trim();
          if (!/^\d{1,2}:\d{2}:\d{2}$/.test(t)) {
            this.errors.goal_time_hms = 'Voer doeltijd in als HH:MM:SS.';
          }
          if (!this.form.goal_ref_date) {
            this.errors.goal_ref_date = 'Kies de datum van je doelwedstrijd.';
          }
        }
        if (stepIndex === 12) {
          const paceRe = /^\d{1,2}:\d{2}$/; // MM:SS

          // Cooper meters: VERPLICHT (800–5000)
          if (this.form.cooper_meters == null || isNaN(this.form.cooper_meters)) {
            this.errors.cooper_meters = 'Vul je Cooper-afstand in (in meters).';
          } else if (this.form.cooper_meters < 800 || this.form.cooper_meters > 5000) {
            this.errors.cooper_meters = 'Cooper-afstand moet tussen 800 en 5000 meter liggen.';
          }

          // 5 km pace: VERPLICHT (MM:SS)
          if (!(this.form.test_5k_pace || '').trim()) {
            this.errors.test_5k_pace = 'Vul je 5 km pace in (MM:SS).';
          } else if (!paceRe.test(this.form.test_5k_pace)) {
            this.errors.test_5k_pace = '5 km pace moet in het formaat MM:SS (bijv. 04:55).';
          }

          // 10 km pace: OPTIONEEL maar als ingevuld, moet geldig zijn
          if ((this.form.test_10k_pace || '').trim() && !paceRe.test(this.form.test_10k_pace)) {
            this.errors.test_10k_pace = '10 km pace moet in het formaat MM:SS.';
          }

          // Marathon pace: OPTIONEEL maar als ingevuld, moet geldig zijn
          if ((this.form.marathon_pace || '').trim() && !paceRe.test(this.form.marathon_pace)) {
            this.errors.marathon_pace = 'Marathon pace moet in het formaat MM:SS.';
          }
        }
        if (stepIndex === 13) {
          if (this.form.hr_estimate_from_age) {
            // vul (niet vast opslaan) als placeholder — we staan lege hr_max_bpm toe bij schatting
          } else {
            if (this.form.hr_max_bpm == null || isNaN(this.form.hr_max_bpm) || this.form.hr_max_bpm < 120 || this.form.hr_max_bpm > 220) {
              this.errors.hr_max_bpm = 'Vul een HF-max tussen 120 en 220 in (of vink schatting aan).';
            }
          }
          if (this.form.rest_hr_bpm != null && (isNaN(this.form.rest_hr_bpm) || this.form.rest_hr_bpm < 30 || this.form.rest_hr_bpm > 100)) {
            this.errors.rest_hr_bpm = 'Rusthartslag tussen 30 en 100 bpm.';
          }
        }
        if (stepIndex === 14) {
          if (this.form.ftp_mode === 'w') {
            if (this.form.ftp_watt != null && (isNaN(this.form.ftp_watt) || this.form.ftp_watt < 80 || this.form.ftp_watt > 500)) {
              this.errors.ftp_watt = 'FTP (W) tussen 80 en 500.';
            }
          } else if (this.form.ftp_mode === 'wkg') {
            if (this.form.ftp_wkg != null && (isNaN(this.form.ftp_wkg) || this.form.ftp_wkg < 1.0 || this.form.ftp_wkg > 7.5)) {
              this.errors.ftp_wkg = 'FTP (W/kg) tussen 1.0 en 7.5.';
            }
          }
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

        // 2) Valideer stap 1 (coach)
        const ok1 = this.validateStep(1);
        if (!ok1) {
          this.step = 1;
          return;
        }

        // 3) Valideer stap 2 (pakket)
        const ok2 = this.validateStep(2);
        if (!ok2) {
          this.step = 2;
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
          preferred_coach: this.form.preferred_coach,
          package: this.form.package,
          duration: this.form.duration,
          height_cm: this.form.height_cm,
          weight_kg: this.form.weight_kg,
          injuries:  this.form.injuries,
          goals: this.form.goals,
          max_days_per_week: this.form.max_days_per_week,
          session_minutes: this.form.session_minutes,
          sport_background: this.form.sport_background,
          facilities: this.form.facilities,
          materials: this.form.materials,
          working_hours: this.form.working_hours,
          goal_distance: this.form.goal_distance,
          goal_time_hms: this.form.goal_time_hms,
          goal_ref_date: this.form.goal_ref_date,
          cooper_meters: this.form.cooper_meters,
          test_5k_pace: this.form.test_5k_pace,
          test_10k_pace: this.form.test_10k_pace,
          marathon_pace: this.form.marathon_pace,
          hr_max_bpm: this.form.hr_estimate_from_age ? this.estimatedHRMax : this.form.hr_max_bpm,
          rest_hr_bpm: this.form.rest_hr_bpm,
          hr_estimate_from_age: this.form.hr_estimate_from_age,
          ftp_mode: this.form.ftp_mode,
          ftp_watt: this.form.ftp_watt,
          ftp_wkg: (this.form.ftp_mode === 'w' ? this.computedFtpWkg : this.form.ftp_wkg),

        };

        // CSRF-token veilig ophalen (kan in layout ontbreken)
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';

        fetch('{{ route('intake.checkout') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',            // <-- belangrijk
            ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {})
        },
        credentials: 'same-origin',                // <-- voor zekerheid bij cookies/sessies
        body: JSON.stringify(payload)
        })
        .then(async (res) => {
        // Log detail bij fout om de exacte status/body te zien
        if (!res.ok) {
            const text = await res.text().catch(() => '');
            console.error('Checkout HTTP error', res.status, text);
            let msg = 'Kon betaalpagina niet starten.';
            try {
            const err = JSON.parse(text);
            if (err?.message) msg = err.message;
            } catch(_) {}
            throw new Error(msg);
        }
        return res.json();
        })
        .then(({ redirect_url }) => {
        if (!redirect_url) throw new Error('Server gaf geen redirect_url terug.');
        try { sessionStorage.setItem('intakePending', '1'); } catch (e) {}
        window.location.href = redirect_url;
        })
        .catch((e) => {
        console.error('Checkout error:', e);
        this.errors.general = e.message || 'Er ging iets mis bij het starten van de betaling.';
        })
        .finally(() => { this.isPaying = false; });
        },

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
