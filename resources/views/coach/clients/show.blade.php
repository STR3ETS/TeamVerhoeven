@extends('layouts.app')
@section('title', 'Cliënt: ' . ($client->name ?? 'onbekend'))

@section('content')
@php
  $card  = 'bg-white rounded-2xl border border-gray-200 p-6 shadow-sm';
  $muted = 'text-gray-500';
@endphp

<div class="max-w-6xl mx-auto px-4 md:px-6 py-6 space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold">Cliënt</h1>
      <p class="text-sm {{ $muted }}">Profiel en UHV-berekening</p>
    </div>
    <a href="{{ route('coach.clients.index') }}" class="text-sm underline">Terug naar overzicht</a>
  </div>

  <div class="grid md:grid-cols-3 gap-6">
    <div class="{{ $card }}">
      <h2 class="font-semibold mb-4">Persoon</h2>
      <dl class="space-y-2 text-sm">
        <div class="flex justify-between gap-4"><dt class="{{ $muted }}">Naam</dt><dd class="text-right">{{ $client->name }}</dd></div>
        <div class="flex justify-between gap-4"><dt class="{{ $muted }}">E-mail</dt><dd class="text-right">{{ $client->email }}</dd></div>
        <div class="flex justify-between gap-4"><dt class="{{ $muted }}">Geboortedatum</dt><dd class="text-right">{{ $birthdate ? $birthdate->format('Y-m-d') : 'Onbekend' }}</dd></div>
        <div class="flex justify-between gap-4"><dt class="{{ $muted }}">Leeftijd</dt><dd class="text-right">{{ $ageYears !== null ? $ageYears . ' jaar' : 'Onbekend' }}</dd></div>
      </dl>
    </div>

    <div class="{{ $card }}">
      <h2 class="font-semibold mb-4">Inputs uit onboarding</h2>
      <dl class="space-y-2 text-sm">
        <div class="flex justify-between gap-4"><dt class="{{ $muted }}">12-min loop</dt><dd class="text-right">
          @if($distance12min) {{ number_format($distance12min, 0, ',', '.') }} m @else Niet ingevuld @endif
        </dd></div>
        <div class="flex justify-between gap-4"><dt class="{{ $muted }}">HF rust</dt><dd class="text-right">
          @if(!is_null($hrRest)) {{ $hrRest }} bpm @else Niet ingevuld @endif
        </dd></div>
        <div class="flex justify-between gap-4"><dt class="{{ $muted }}">HF max</dt><dd class="text-right">
          @if(!is_null($hrMax)) {{ $hrMax }} bpm @else Niet ingevuld @endif
        </dd></div>
      </dl>
    </div>

    <div class="{{ $card }}">
      <h2 class="font-semibold mb-4">Status</h2>
      @if($uhvData)
        <p class="text-sm">Berekening gereed op basis van 12-min loop, HF rust en HF max.</p>
      @else
        <p class="text-sm">Onvoldoende data om te berekenen. Vul 12-min loop, HF rust en HF max in.</p>
      @endif
    </div>
  </div>

  <div class="{{ $card }}">
    <h2 class="font-semibold mb-4">UHV-zones (Karvonen)</h2>

    @if($uhvData)
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="border rounded-xl p-4">
          <h3 class="font-semibold">Recovery <span class="text-xs {{ $muted }}">50% - 65%</span></h3>
          <p class="text-2xl mt-1">
            {{ $uhvData['zones']['recovery']['min_bpm'] }} - {{ $uhvData['zones']['recovery']['max_bpm'] }} bpm
          </p>
          <p class="text-xs mt-2 {{ $muted }}">{{ $uhvData['zones']['recovery']['label'] }}</p>
        </div>

        <div class="border rounded-xl p-4">
          <h3 class="font-semibold">LSD <span class="text-xs {{ $muted }}">70% - 75%</span></h3>
          <p class="text-2xl mt-1">
            {{ $uhvData['zones']['lsd']['min_bpm'] }} - {{ $uhvData['zones']['lsd']['max_bpm'] }} bpm
          </p>
          <p class="text-xs mt-2 {{ $muted }}">{{ $uhvData['zones']['lsd']['label'] }}</p>
        </div>

        <div class="border rounded-xl p-4">
          <h3 class="font-semibold">Pace/Tempo <span class="text-xs {{ $muted }}">80% - 85%</span></h3>
          <p class="text-2xl mt-1">
            {{ $uhvData['zones']['pace']['min_bpm'] }} - {{ $uhvData['zones']['pace']['max_bpm'] }} bpm
          </p>
          <p class="text-xs mt-2 {{ $muted }}">{{ $uhvData['zones']['pace']['label'] }}</p>
        </div>

        <div class="border rounded-xl p-4">
          <h3 class="font-semibold">Interval <span class="text-xs {{ $muted }}">90%</span></h3>
          <p class="text-2xl mt-1">{{ $uhvData['zones']['interval']['bpm'] }} bpm</p>
          <p class="text-xs mt-2 {{ $muted }}">{{ $uhvData['zones']['interval']['label'] }}</p>
        </div>

        <div class="border rounded-xl p-4">
          <h3 class="font-semibold">HIT <span class="text-xs {{ $muted }}">95%</span></h3>
          <p class="text-2xl mt-1">{{ $uhvData['zones']['hit']['bpm'] }} bpm</p>
          <p class="text-xs mt-2 {{ $muted }}">{{ $uhvData['zones']['hit']['label'] }}</p>
        </div>
      </div>
    @else
      <p class="text-sm {{ $muted }}">Nog geen zones beschikbaar.</p>
    @endif
  </div>

  <div class="{{ $card }}">
    <h2 class="font-semibold mb-4">Tempo tabellen</h2>
    <p class="text-sm {{ $muted }}">De tijden voor 200 m, 400 m, 600 m, 800 m en 1000 m volgen later. Nu leeg.</p>

    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="text-left {{ $muted }}">
          <tr>
            <th class="py-2">Afstand</th>
            <th class="py-2">Van</th>
            <th class="py-2">Tot</th>
          </tr>
        </thead>
        <tbody>
          @foreach(['200m','400m','600m','800m','1000m'] as $dist)
            <tr class="border-t">
              <td class="py-2 font-medium">{{ $dist }}</td>
              <td class="py-2">n.t.b.</td>
              <td class="py-2">n.t.b.</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
