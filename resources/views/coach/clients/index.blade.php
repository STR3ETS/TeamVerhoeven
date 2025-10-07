@extends('layouts.app')
@section('title','Team Verhoeven')

@section('content')
@php
  $card      = 'p-5 bg-white rounded-3xl border border-gray-300';
  $btn       = 'px-4 py-2 bg-[#c8ab7a] hover:bg-[#a89067] transition duration-300 rounded text-white font-semibold text-sm';
  $btnGhost  = 'text-xs cursor-pointer opacity-50 hover:opacity-100 transition duration-300 font-semibold';
  $inputBase = 'w-full rounded-xl border transition duration-300 p-3 focus:outline-none focus:ring-0 text-[16px] md:text-sm border-gray-300 hover:border-[#c7c7c7]';
@endphp

<h1 class="text-2xl font-bold mb-2">Klanten</h1>
<p class="text-sm text-black opacity-80 font-medium mb-6">
  Beheer je klanten, bekijk profielen en open details met UHV-berekening.
</p>

<div class="grid gap-4 grid-cols-1 md:grid-cols-3 mb-4">
  <form method="get" class="md:col-span-2">
    <div class="flex gap-2">
      <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Zoek op naam of e-mail"
             class="{{ $inputBase }}">
      <button class="{{ $btn }}">Zoeken</button>
      @if(($q ?? '') !== '')
        <a href="{{ route('coach.clients.index') }}" class="px-3 py-2 text-xs font-semibold rounded border border-gray-300 hover:bg-gray-50 transition">
          Reset
        </a>
      @endif
    </div>
  </form>

  <div class="md:col-span-1">
    <div class="{{ $card }}">
      <div class="text-sm text-black font-semibold opacity-50 mb-1">Resultaten</div>
      <div class="text-3xl font-bold text-[#c8ab7a]">
        {{ number_format($clients->total(), 0, ',', '.') }}
      </div>
      <div class="text-[12px] text-gray-500 mt-1">
        Pagina {{ $clients->currentPage() }} van {{ $clients->lastPage() }}
      </div>
    </div>
  </div>
</div>

<div class="{{ $card }} overflow-hidden">
  <table class="min-w-full text-sm">
    <thead class="bg-gray-50">
      <tr>
        <th class="text-left px-4 py-3">Klant</th>
        <th class="text-left px-4 py-3">E-mail</th>
        <th class="text-left px-4 py-3">Profiel</th>
        <th class="text-right px-4 py-3">Acties</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($clients as $c)
        @php $p = $c->clientProfile; @endphp
        <tr class="border-t">
          <td class="px-4 py-3">
            <div class="font-semibold">{{ $c->name }}</div>
            @if($p && ($p->goal['distance'] ?? null))
              <div class="text-[12px] text-gray-500">
                Doel: {{ $p->goal['distance'] }}
                @if(!empty($p->goal['time_hms'])) • {{ $p->goal['time_hms'] }} @endif
                @if(!empty($p->goal['date'])) • {{ \Illuminate\Support\Carbon::parse($p->goal['date'])->format('d-m-Y') }} @endif
              </div>
            @endif
          </td>
          <td class="px-4 py-3">
            <a href="mailto:{{ $c->email }}" class="hover:underline">{{ $c->email }}</a>
          </td>
          <td class="px-4 py-3">
            @if($p)
              <div class="flex flex-wrap gap-x-3 gap-y-1">
                <span class="text-[12px] text-gray-600">
                  Lengte: {{ $p->height_cm ? rtrim(rtrim(number_format($p->height_cm,2,',','.'), '0'), ',') : '–' }} cm
                </span>
                <span class="text-[12px] text-gray-600">
                  Gewicht: {{ $p->weight_kg ? rtrim(rtrim(number_format($p->weight_kg,2,',','.'), '0'), ',') : '–' }} kg
                </span>
                @php
                  $spw = $p->frequency['sessions_per_week'] ?? null;
                  $mps = $p->frequency['minutes_per_session'] ?? null;
                @endphp
                <span class="text-[12px] text-gray-600">
                  Frequentie: {{ $spw ? $spw.'x/wk' : '–' }}@if($mps) • {{ $mps }} min @endif
                </span>
              </div>
            @else
              <em class="text-[12px] text-gray-500">Geen profiel</em>
            @endif
          </td>
          <td class="px-4 py-3 text-right">
            <a href="{{ route('coach.clients.show', $c) }}" class="inline-block {{ $btn }}">Bekijken</a>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="4" class="px-4 py-8 text-center text-gray-500">
            Geen cliënten gevonden.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="p-4">
    {{ $clients->withQueryString()->links() }}
  </div>
</div>
@endsection
