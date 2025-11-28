@extends('layouts.app')
@section('title','Berichten')

@section('content')
@if($role === 'coach')
    <h1 class="text-2xl font-bold mb-2">Chat met je klanten</h1>
    <p class="text-sm text-black opacity-80 font-medium mb-6">Chat snel en gemakkelijk met jouw klanten<br>en begeleid ze door hun traject!</p>
    @elseif($role === 'client')
    <h1 class="text-2xl font-bold mb-2">Chat met je coach</h1>
    <p class="text-sm text-black opacity-80 font-medium mb-6">Chat snel en gemakkelijk met jouw coach.<br>Beschrijf wat je nodig hebt of geef blessures door.</p>
@endif
@if($role === 'client')
    @can('create', App\Models\Thread::class)
        <h2 class="text-lg font-bold mb-2">Snelkoppelingen</h2>
        <div class="grid gap-3 grid-cols-1 md:grid-cols-4 mb-6">
            <a href="{{ route('client.threads.create') }}"
            class="p-4 bg-[#c8ab7a] hover:bg-[#a89067] transition duration-300 rounded">
                <div class="font-semibold text-white">Nieuwe chat met coach</div>
                <p class="text-sm text-white">Start een nieuw gesprek</p>
            </a>
        </div>
    @endcan
@endif
@if($role === 'coach')
    @can('create', App\Models\Thread::class)
        <h2 class="text-lg font-bold mb-2">Snelkoppelingen</h2>
        <div class="grid gap-3 grid-cols-1 md:grid-cols-4 mb-6">
            <a href="{{ route('coach.threads.create') }}"
               class="p-4 bg-[#c8ab7a] hover:bg-[#a89067] transition duration-300 rounded">
                <div class="font-semibold text-white">Nieuwe chat met klant</div>
                <p class="text-sm text-white">Start een nieuw gesprek</p>
            </a>
        </div>
    @endcan
@endif

<div class="mt-7">
  <h2 class="text-lg font-bold">Gesprekken</h2>

  <ul class="mt-3 list-none p-0 space-y-2">
    @forelse($threads as $t)
        @php
        $href = $role === 'client'
            ? route('client.threads.show', $t)
            : route('coach.threads.show',  $t);

        // specifiek: pak naam via client_user_id -> users.name
        $clientName = optional($t->clientUser)->name ?? 'Onbekende klant';
        @endphp

      <li>
        <a href="{{ $href }}" class="block group">
          <div class="p-5 bg-white rounded-3xl border border-gray-300
                      flex items-center justify-between gap-4
                      transition duration-300 hover:border-[#c8ab7a]">
            <div>
                @if($role === 'coach')
                <span class="text-xs bg-gray-200 font-semibold text-gray-600 inline-flex items-center px-2 py-0.5 rounded mb-2">
                    {{ $clientName }}
                </span>
                @endif

              <div class="font-semibold text-sm mb-1 group-hover:opacity-90">
                {{ $t->subject ?? 'Gesprek' }}
              </div>

              <div class="flex text-xs items-center gap-2 text-gray-500">
                <span>Aangemaakt op {{ $t->created_at->format('d-m-Y H:i') }}</span>
              </div>
            </div>

            @if($role === 'coach')
              <span class="shrink-0 w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
            @endif
          </div>
        </a>
      </li>
    @empty
      <li class="p-5 bg-white rounded-3xl border border-gray-300 text-sm font-medium text-gray-400">
        Geen gesprekken gevonden.
      </li>
    @endforelse
  </ul>
</div>
@endsection