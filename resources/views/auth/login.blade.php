@extends('layouts.app')
@section('title','Team Verhoeven')

@section('content')
@php
  $cardClass  = 'w-full p-5 bg-white rounded-3xl border border-gray-300';
  $btnPrimary = 'cursor-pointer px-6 py-3 bg-[#c8ab7a] hover:bg-[#a38b62] transition duration-300 text-white font-medium text-sm rounded';
  $btnGhost   = 'text-xs cursor-pointer opacity-50 hover:opacity-100 transition duration-300 font-semibold';
@endphp

<div class="max-w-xl mx-auto h-[calc(100vh-96px)] flex flex-col items-start justify-center pb-28">
  <h1 class="text-2xl font-bold mb-2 flex items-center">
    <div class="flex relative">
      <div class="w-10 h-10 border-2 border-[#f9f6f1] rounded-full bg-black bg-cover bg-top relative bg-[url(https://cdn6.site-media.eu/images/640%2C1160x772%2B130%2B112/18694492/coachNicky-MjbAPBl6Pr1a23o9d6zbqA.webp)]"></div>
      <div class="-left-3 w-10 h-10 border-2 border-[#f9f6f1] rounded-full bg-black bg-cover bg-top relative bg-[url(https://cdn6.site-media.eu/images/576%2C1160x772%2B150%2B121/18694504/coachEline-DVsTZnUZ-eQ_EWm1zNyfww.webp)]"></div>
      <div class="-left-6 w-10 h-10 border-2 border-[#f9f6f1] rounded-full bg-black bg-cover bg-top relative bg-[url(https://cdn6.site-media.eu/images/576%2C1160x772%2B134%2B41/18694509/coachRoy-LCXiB9ufGNk2uXEnykijBA.webp)]"></div>
    </div>
    <div class="bg-white h-9 px-4 rounded-xl flex items-center relative -ml-2">
      <div class="w-4 h-4 rotate-[45deg] rounded-sm absolute -left-1 bg-white"></div>
      <p class="italic text-[10px] leading-[1] md:leading-tighter font-semibold">"Welkom terug. Let's go!"</p>
    </div>
  </h1>

  <h2 class="text-xl font-bold mb-2">Inloggen</h2>
  <p class="text-sm font-medium text-black/60 mb-8">
    Log in in jouw persoonlijke trainingsplatform.
  </p>

  <div class="{{ $cardClass }}">
    {{-- Status / errors --}}
    @if (session('status'))
      <div class="mb-4 rounded-xl border border-green-200 bg-green-50 text-green-700 p-3 text-sm">
        {{ session('status') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="mb-4 rounded-xl border border-red-200 bg-red-50 text-red-700 p-3 text-sm">
        <ul class="list-disc ps-5">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- Formulier 1: code aanvragen --}}
    <form method="POST" action="{{ route('login.request') }}" class="space-y-4">
      @csrf
      <div>
        <label for="email" class="text-sm font-medium text-black mb-1 block">Wat is je e-mailadres?</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required
               autocomplete="email"
               class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]">
      </div>
      <div class="flex items-center justify-between gap-2">
        <a href="{{ url('/') }}" class="{{ $btnGhost }}">Terug naar home</a>
        <button type="submit" class="{{ $btnPrimary }}">Verstuur code naar mijn e-mailadres</button>
      </div>
    </form>

    <hr class="my-6 border-gray-200">

    {{-- Formulier 2: inloggen met code --}}
    <form method="POST" action="{{ route('login.verify') }}" class="space-y-4">
      @csrf
      {{-- Neem e-mail mee uit stap 1 --}}
      <input type="hidden" name="email" value="{{ old('email') }}">

      <div>
        <label for="inlogencode" class="text-sm font-medium text-black mb-1 block">Voer inlogcode in</label>
        <input id="inlogencode" name="code" type="text" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" required
               class="w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]">
        <p class="text-xs text-black/50 mt-1">Dev-tip: code staat in <em>storage/logs/laravel.log</em>.</p>
      </div>

      <div class="flex items-center justify-end">
        <button type="submit" class="{{ $btnPrimary }}">Inloggen</button>
      </div>
    </form>
  </div>
</div>
@endsection
